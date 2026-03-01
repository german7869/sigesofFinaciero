<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\ImpuestoIva;
use Illuminate\Http\Request;

class ImpuestoIvaController extends Controller {
    public function index(Request $request) { return ImpuestoIva::where('empresa_id', $request->empresa_id)->get(); }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','codigo'=>'required','nombre'=>'required','porcentaje'=>'required|numeric','cuenta_contable_id'=>'nullable']);
        return response()->json(ImpuestoIva::create($data), 201);
    }
    public function show(ImpuestoIva $impuestoIva) { return $impuestoIva; }
    public function update(Request $request, ImpuestoIva $impuestoIva) { $impuestoIva->update($request->all()); return $impuestoIva; }
    public function destroy(ImpuestoIva $impuestoIva) { $impuestoIva->delete(); return response()->json(['message' => 'Eliminado']); }
}
