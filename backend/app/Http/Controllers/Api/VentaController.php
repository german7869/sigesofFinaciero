<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Venta, Documento, Asiento, ModeloContableDetalle, ImpuestoIva, FormaPago};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller {
    public function index(Request $request) {
        $q = Venta::with(['cliente','documento'])->where('empresa_id', $request->empresa_id);
        if ($request->cliente_id) $q->where('cliente_id', $request->cliente_id);
        if ($request->estado) $q->where('estado', $request->estado);
        return $q->orderByDesc('fecha')->get();
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required',
            'cliente_id' => 'required|exists:clientes,id',
            'documento_id' => 'required|exists:documentos,id',
            'fecha' => 'required|date',
            'concepto' => 'nullable',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'nullable',
            'detalles.*.descripcion' => 'required',
            'detalles.*.cantidad' => 'required|numeric|min:0',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
            'detalles.*.descuento' => 'nullable|numeric|min:0',
            'detalles.*.impuesto_iva_id' => 'nullable',
            'pagos' => 'required|array|min:1',
            'pagos.*.forma_pago_id' => 'nullable',
            'pagos.*.descripcion' => 'required',
            'pagos.*.monto' => 'required|numeric|min:0',
        ]);
        
        return DB::transaction(function () use ($data) {
            $subtotal_15 = 0; $subtotal_5 = 0; $subtotal_0 = 0; $iva_15 = 0; $iva_5 = 0; $descuento = 0;
            $detallesCalc = [];
            foreach ($data['detalles'] as $det) {
                $desc = $det['descuento'] ?? 0;
                $sub = ($det['cantidad'] * $det['precio_unitario']) - $desc;
                $iva = 0; $pct = 0;
                if (!empty($det['impuesto_iva_id'])) {
                    $imp = ImpuestoIva::find($det['impuesto_iva_id']);
                    if ($imp) { $pct = $imp->porcentaje; $iva = round($sub * $pct / 100, 2); }
                }
                if ($pct == 15) { $subtotal_15 += $sub; $iva_15 += $iva; }
                elseif ($pct == 5) { $subtotal_5 += $sub; $iva_5 += $iva; }
                else { $subtotal_0 += $sub; }
                $descuento += $desc;
                $detallesCalc[] = array_merge($det, ['subtotal' => $sub, 'iva' => $iva, 'total' => $sub + $iva, 'descuento' => $desc]);
            }
            $total = $subtotal_15 + $subtotal_5 + $subtotal_0 + $iva_15 + $iva_5;
            $totalPagos = collect($data['pagos'])->sum('monto');
            $saldo = max(0, $total - $totalPagos);
            
            $doc = Documento::findOrFail($data['documento_id']);
            $numero = $doc->generarNumero();
            
            $modelos = ModeloContableDetalle::where('empresa_id', $data['empresa_id'])
                ->where('modulo', 'VENTAS')->pluck('cuenta_contable_id', 'campo');
            
            $venta = Venta::create([
                'empresa_id' => $data['empresa_id'],
                'cliente_id' => $data['cliente_id'],
                'documento_id' => $data['documento_id'],
                'numero' => $numero,
                'fecha' => $data['fecha'],
                'concepto' => $data['concepto'] ?? null,
                'subtotal_15' => $subtotal_15, 'subtotal_5' => $subtotal_5, 'subtotal_0' => $subtotal_0,
                'descuento' => $descuento, 'iva_15' => $iva_15, 'iva_5' => $iva_5,
                'total' => $total, 'saldo' => $saldo, 'estado' => 'ACTIVA',
            ]);
            
            foreach ($detallesCalc as $det) { $venta->detalles()->create($det); }
            foreach ($data['pagos'] as $pago) { $venta->pagos()->create($pago); }
            
            $asientoLineas = [];
            foreach ($data['pagos'] as $pago) {
                if (!empty($pago['forma_pago_id'])) {
                    $fp = FormaPago::find($pago['forma_pago_id']);
                    if ($fp && $fp->cuenta_contable_id) {
                        $asientoLineas[] = ['cuenta_contable_id' => $fp->cuenta_contable_id, 'descripcion' => $pago['descripcion'], 'debe' => $pago['monto'], 'haber' => 0];
                    }
                }
            }
            if ($saldo > 0 && !empty($modelos['CREDITO'])) {
                $asientoLineas[] = ['cuenta_contable_id' => $modelos['CREDITO'], 'descripcion' => 'Crédito venta '.$numero, 'debe' => $saldo, 'haber' => 0];
            }
            if ($subtotal_15 > 0 && !empty($modelos['SUBTOTAL_15'])) {
                $asientoLineas[] = ['cuenta_contable_id' => $modelos['SUBTOTAL_15'], 'descripcion' => 'Ingreso subtotal 15%', 'debe' => 0, 'haber' => $subtotal_15];
            }
            if ($subtotal_5 > 0 && !empty($modelos['SUBTOTAL_5'])) {
                $asientoLineas[] = ['cuenta_contable_id' => $modelos['SUBTOTAL_5'], 'descripcion' => 'Ingreso subtotal 5%', 'debe' => 0, 'haber' => $subtotal_5];
            }
            if ($subtotal_0 > 0 && !empty($modelos['SUBTOTAL_0'])) {
                $asientoLineas[] = ['cuenta_contable_id' => $modelos['SUBTOTAL_0'], 'descripcion' => 'Ingreso subtotal 0%', 'debe' => 0, 'haber' => $subtotal_0];
            }
            $ivaAgrupadoPorCuenta = [];
            foreach ($data['detalles'] as $det) {
                if (!empty($det['impuesto_iva_id'])) {
                    $imp = ImpuestoIva::find($det['impuesto_iva_id']);
                    if ($imp && $imp->porcentaje > 0 && $imp->cuenta_contable_id) {
                        $sub2 = ($det['cantidad'] * $det['precio_unitario']) - ($det['descuento'] ?? 0);
                        $iva2 = round($sub2 * $imp->porcentaje / 100, 2);
                        $ivaAgrupadoPorCuenta[$imp->cuenta_contable_id] = ($ivaAgrupadoPorCuenta[$imp->cuenta_contable_id] ?? 0) + $iva2;
                    }
                }
            }
            foreach ($ivaAgrupadoPorCuenta as $cuentaId => $ivaVal) {
                if ($ivaVal > 0) {
                    $asientoLineas[] = ['cuenta_contable_id' => $cuentaId, 'descripcion' => 'IVA venta '.$numero, 'debe' => 0, 'haber' => $ivaVal];
                }
            }
            
            if (!empty($asientoLineas)) {
                $totalDebe = collect($asientoLineas)->sum('debe');
                $totalHaber = collect($asientoLineas)->sum('haber');
                if (round($totalDebe, 2) == round($totalHaber, 2)) {
                    $asiento = Asiento::create([
                        'empresa_id' => $data['empresa_id'], 'fecha' => $data['fecha'],
                        'documento_id' => $data['documento_id'], 'numero' => $numero,
                        'concepto' => 'Venta '.$numero, 'beneficiario' => null, 'origen_modulo' => 'VENTAS',
                    ]);
                    foreach ($asientoLineas as $linea) { $asiento->detalles()->create($linea); }
                    $venta->update(['asiento_id' => $asiento->id]);
                }
            }
            
            return response()->json($venta->load('detalles', 'pagos', 'asiento.detalles.cuenta'), 201);
        });
    }
    
    public function show(Venta $venta) { return $venta->load('cliente', 'detalles.producto', 'pagos.formaPago', 'asiento.detalles.cuenta', 'documento'); }
}
