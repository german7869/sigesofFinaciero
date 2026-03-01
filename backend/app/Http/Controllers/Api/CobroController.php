<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Cobro, Venta, Asiento, ModeloContableDetalle, FormaPago};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobroController extends Controller {
    public function index(Request $request) {
        return Cobro::with(['cliente','detalles.venta','pagos.formaPago'])
            ->where('empresa_id', $request->empresa_id)->get();
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required',
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'concepto' => 'nullable',
            'detalles' => 'required|array|min:1',
            'detalles.*.venta_id' => 'required|exists:ventas,id',
            'detalles.*.monto' => 'required|numeric|min:0.01',
            'pagos' => 'required|array|min:1',
            'pagos.*.forma_pago_id' => 'nullable',
            'pagos.*.descripcion' => 'required',
            'pagos.*.monto' => 'required|numeric|min:0.01',
        ]);
        
        return DB::transaction(function () use ($data) {
            $total = collect($data['detalles'])->sum('monto');
            
            foreach ($data['detalles'] as $det) {
                $venta = Venta::findOrFail($det['venta_id']);
                if ($det['monto'] > $venta->saldo) {
                    return response()->json(['message' => 'Monto de cobro supera el saldo de la factura '.$venta->numero], 422);
                }
            }
            
            $cobro = Cobro::create([
                'empresa_id' => $data['empresa_id'], 'cliente_id' => $data['cliente_id'],
                'fecha' => $data['fecha'], 'concepto' => $data['concepto'] ?? null, 'total' => $total,
            ]);
            
            foreach ($data['detalles'] as $det) {
                $cobro->detalles()->create($det);
                $venta = Venta::find($det['venta_id']);
                $venta->saldo -= $det['monto'];
                $venta->save();
            }
            foreach ($data['pagos'] as $pago) { $cobro->pagos()->create($pago); }
            
            $modelos = ModeloContableDetalle::where('empresa_id', $data['empresa_id'])
                ->where('modulo', 'VENTAS')->pluck('cuenta_contable_id', 'campo');
            $asientoLineas = [];
            foreach ($data['pagos'] as $pago) {
                if (!empty($pago['forma_pago_id'])) {
                    $fp = FormaPago::find($pago['forma_pago_id']);
                    if ($fp && $fp->cuenta_contable_id) {
                        $asientoLineas[] = ['cuenta_contable_id' => $fp->cuenta_contable_id, 'descripcion' => $pago['descripcion'], 'debe' => $pago['monto'], 'haber' => 0];
                    }
                }
            }
            if (!empty($modelos['CREDITO'])) {
                $asientoLineas[] = ['cuenta_contable_id' => $modelos['CREDITO'], 'descripcion' => 'Cobro CxC', 'debe' => 0, 'haber' => $total];
            }
            
            if (!empty($asientoLineas)) {
                $td = collect($asientoLineas)->sum('debe'); $th = collect($asientoLineas)->sum('haber');
                if (round($td, 2) == round($th, 2)) {
                    $asiento = Asiento::create([
                        'empresa_id' => $data['empresa_id'], 'fecha' => $data['fecha'],
                        'numero' => 'COB-'.date('YmdHis'), 'concepto' => 'Cobro cliente', 'origen_modulo' => 'COBROS',
                    ]);
                    foreach ($asientoLineas as $linea) { $asiento->detalles()->create($linea); }
                    $cobro->update(['asiento_id' => $asiento->id]);
                }
            }
            
            return response()->json($cobro->load('detalles.venta', 'pagos.formaPago', 'asiento'), 201);
        });
    }
}
