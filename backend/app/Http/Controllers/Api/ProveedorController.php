<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller {
    public function index(Request $request) { return Proveedor::where('empresa_id', $request->empresa_id)->get(); }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','identificacion'=>'required','nombres'=>'required','direccion'=>'nullable','telefono'=>'nullable','correo'=>'nullable|email','ciudad'=>'nullable']);
        return response()->json(Proveedor::create($data), 201);
    }
    public function show(Proveedor $proveedor) { return $proveedor; }
    public function update(Request $request, Proveedor $proveedor) { $proveedor->update($request->all()); return $proveedor; }
    public function destroy(Proveedor $proveedor) { $proveedor->delete(); return response()->json(['message'=>'Eliminado']); }
}
