<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\{Compra, Asiento, ModeloContableDetalle, ImpuestoIva, FormaPago};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller {
    public function index(Request $request) {
        return Compra::with(['proveedor'])->where('empresa_id', $request->empresa_id)->orderByDesc('fecha')->get();
    }
    
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required',
            'proveedor_id' => 'required|exists:proveedores,id',
            'numero' => 'required',
            'fecha' => 'required|date',
            'concepto' => 'nullable',
            'detalles' => 'required|array|min:1',
            'detalles.*.descripcion' => 'required',
            'detalles.*.cantidad' => 'required|numeric',
            'detalles.*.precio_unitario' => 'required|numeric',
            'detalles.*.impuesto_iva_id' => 'nullable',
            'retenciones' => 'nullable|array',
            'retenciones.*.tipo' => 'required',
            'retenciones.*.porcentaje' => 'required|numeric',
            'retenciones.*.base' => 'required|numeric',
            'retenciones.*.valor' => 'required|numeric',
            'pagos' => 'required|array|min:1',
            'pagos.*.forma_pago_id' => 'nullable',
            'pagos.*.descripcion' => 'required',
            'pagos.*.monto' => 'required|numeric',
        ]);
        
        return DB::transaction(function () use ($data) {
            $subtotal_15=0; $subtotal_5=0; $subtotal_0=0; $iva_15=0; $iva_5=0;
            $detallesCalc = [];
            foreach ($data['detalles'] as $det) {
                $sub = $det['cantidad'] * $det['precio_unitario'];
                $iva=0; $pct=0;
                if (!empty($det['impuesto_iva_id'])) {
                    $imp = ImpuestoIva::find($det['impuesto_iva_id']);
                    if ($imp) { $pct=$imp->porcentaje; $iva=round($sub*$pct/100,2); }
                }
                if ($pct==15) { $subtotal_15+=$sub; $iva_15+=$iva; }
                elseif ($pct==5) { $subtotal_5+=$sub; $iva_5+=$iva; }
                else { $subtotal_0+=$sub; }
                $detallesCalc[] = array_merge($det, ['subtotal'=>$sub, 'iva'=>$iva, 'total'=>$sub+$iva]);
            }
            $total = $subtotal_15+$subtotal_5+$subtotal_0+$iva_15+$iva_5;
            $totalRet = collect($data['retenciones'] ?? [])->sum('valor');
            $totalPagos = collect($data['pagos'])->sum('monto');
            $saldo = max(0, $total - $totalPagos - $totalRet);
            
            $compra = Compra::create([
                'empresa_id'=>$data['empresa_id'], 'proveedor_id'=>$data['proveedor_id'],
                'numero'=>$data['numero'], 'fecha'=>$data['fecha'], 'concepto'=>$data['concepto']??null,
                'subtotal_15'=>$subtotal_15,'subtotal_5'=>$subtotal_5,'subtotal_0'=>$subtotal_0,
                'iva_15'=>$iva_15,'iva_5'=>$iva_5,'total'=>$total,'saldo'=>$saldo,
            ]);
            foreach ($detallesCalc as $d) { $compra->detalles()->create($d); }
            foreach ($data['retenciones']??[] as $r) { $compra->retenciones()->create($r); }
            foreach ($data['pagos'] as $p) { $compra->pagos()->create($p); }
            
            $modelos = ModeloContableDetalle::where('empresa_id', $data['empresa_id'])
                ->where('modulo','COMPRAS')->pluck('cuenta_contable_id','campo');
            $asientoLineas = [];
            if ($subtotal_15>0 && !empty($modelos['GASTO_15'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['GASTO_15'],'descripcion'=>'Gasto compra 15%','debe'=>$subtotal_15,'haber'=>0];
            if ($subtotal_5>0 && !empty($modelos['GASTO_5'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['GASTO_5'],'descripcion'=>'Gasto compra 5%','debe'=>$subtotal_5,'haber'=>0];
            if ($subtotal_0>0 && !empty($modelos['GASTO_0'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['GASTO_0'],'descripcion'=>'Gasto compra 0%','debe'=>$subtotal_0,'haber'=>0];
            if ($iva_15>0 && !empty($modelos['IVA_COMPRAS_15'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['IVA_COMPRAS_15'],'descripcion'=>'IVA compra 15%','debe'=>$iva_15,'haber'=>0];
            if ($iva_5>0 && !empty($modelos['IVA_COMPRAS_5'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['IVA_COMPRAS_5'],'descripcion'=>'IVA compra 5%','debe'=>$iva_5,'haber'=>0];
            foreach ($data['retenciones']??[] as $r) {
                $campo = 'RETENCION_'.$r['tipo'];
                if (!empty($modelos[$campo])) $asientoLineas[]=['cuenta_contable_id'=>$modelos[$campo],'descripcion'=>'Ret '.$r['tipo'],'debe'=>0,'haber'=>$r['valor']];
            }
            foreach ($data['pagos'] as $p) {
                if (!empty($p['forma_pago_id'])) {
                    $fp=FormaPago::find($p['forma_pago_id']);
                    if ($fp&&$fp->cuenta_contable_id) $asientoLineas[]=['cuenta_contable_id'=>$fp->cuenta_contable_id,'descripcion'=>$p['descripcion'],'debe'=>0,'haber'=>$p['monto']];
                }
            }
            if ($saldo>0&&!empty($modelos['CXP'])) $asientoLineas[]=['cuenta_contable_id'=>$modelos['CXP'],'descripcion'=>'CxP compra','debe'=>0,'haber'=>$saldo];
            
            if (!empty($asientoLineas)) {
                $td=collect($asientoLineas)->sum('debe'); $th=collect($asientoLineas)->sum('haber');
                if (round($td,2)==round($th,2)) {
                    $asiento=Asiento::create(['empresa_id'=>$data['empresa_id'],'fecha'=>$data['fecha'],'numero'=>$data['numero'],'concepto'=>'Compra '.$data['numero'],'origen_modulo'=>'COMPRAS']);
                    foreach ($asientoLineas as $l) { $asiento->detalles()->create($l); }
                    $compra->update(['asiento_id'=>$asiento->id]);
                }
            }
            
            return response()->json($compra->load('detalles','retenciones','pagos','asiento.detalles.cuenta'), 201);
        });
    }
    
    public function show(Compra $compra) { return $compra->load('proveedor','detalles','retenciones','pagos.formaPago','asiento.detalles.cuenta'); }
}
