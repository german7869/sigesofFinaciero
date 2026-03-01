<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\FormaPago;
use Illuminate\Http\Request;

class FormaPagoController extends Controller {
    public function index(Request $request) { return FormaPago::where('empresa_id', $request->empresa_id)->get(); }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','codigo'=>'required','nombre'=>'required','cuenta_contable_id'=>'nullable']);
        return response()->json(FormaPago::create($data), 201);
    }
    public function show(FormaPago $formaPago) { return $formaPago; }
    public function update(Request $request, FormaPago $formaPago) { $formaPago->update($request->all()); return $formaPago; }
    public function destroy(FormaPago $formaPago) { $formaPago->delete(); return response()->json(['message' => 'Eliminado']); }
}
