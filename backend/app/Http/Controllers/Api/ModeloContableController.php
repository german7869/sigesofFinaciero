<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ModeloContableDetalle;
use Illuminate\Http\Request;

class ModeloContableController extends Controller {
    public function index(Request $request) { return ModeloContableDetalle::where('empresa_id', $request->empresa_id)->with('cuentaContable')->get(); }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','modulo'=>'required','campo'=>'required','cuenta_contable_id'=>'required']);
        return response()->json(ModeloContableDetalle::create($data), 201);
    }
    public function show(ModeloContableDetalle $modeloContable) { return $modeloContable; }
    public function update(Request $request, ModeloContableDetalle $modeloContable) { $modeloContable->update($request->all()); return $modeloContable; }
    public function destroy(ModeloContableDetalle $modeloContable) { $modeloContable->delete(); return response()->json(['message' => 'Eliminado']); }
}
