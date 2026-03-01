<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller {
    public function index(Request $request) { return Producto::where('empresa_id', $request->empresa_id)->with('impuestoIva')->get(); }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','codigo'=>'required','nombre'=>'required','categoria'=>'nullable','precio'=>'required|numeric','impuesto_iva_id'=>'nullable','cuenta_ingreso_id'=>'nullable','cuenta_costo_id'=>'nullable']);
        return response()->json(Producto::create($data), 201);
    }
    public function show(Producto $producto) { return $producto->load('impuestoIva'); }
    public function update(Request $request, Producto $producto) { $producto->update($request->all()); return $producto; }
    public function destroy(Producto $producto) { $producto->delete(); return response()->json(['message'=>'Eliminado']); }
}
