<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\CuentaContable;
use Illuminate\Http\Request;

class CuentaContableController extends Controller {
    public function index(Request $request) {
        return CuentaContable::where('empresa_id', $request->empresa_id)
            ->with('children')
            ->whereNull('parent_id')
            ->get();
    }
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required',
            'parent_id' => 'nullable|exists:cuentas_contables,id',
            'codigo' => 'required',
            'descripcion' => 'required',
            'nivel' => 'required|integer',
            'tipo' => 'required|in:ACTIVO,PASIVO,PATRIMONIO,INGRESO,GASTO,COSTO',
            'es_auxiliar' => 'boolean',
        ]);
        return response()->json(CuentaContable::create($data), 201);
    }
    public function show(CuentaContable $cuentaContable) { return $cuentaContable; }
    public function update(Request $request, CuentaContable $cuentaContable) {
        $cuentaContable->update($request->all());
        return $cuentaContable;
    }
    public function destroy(CuentaContable $cuentaContable) { $cuentaContable->delete(); return response()->json(['message' => 'Eliminado']); }
    
    public function flat(Request $request) {
        return CuentaContable::where('empresa_id', $request->empresa_id)->orderBy('codigo')->get();
    }
}
