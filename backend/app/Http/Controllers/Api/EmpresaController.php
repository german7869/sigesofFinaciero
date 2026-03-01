<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller {
    public function index() { return Empresa::all(); }
    public function store(Request $request) {
        $data = $request->validate([
            'ruc' => 'required|unique:empresas,ruc',
            'razon_social' => 'required',
            'nombre_comercial' => 'nullable',
            'direccion' => 'nullable',
            'ciudad' => 'nullable',
            'email' => 'nullable|email',
            'telefono' => 'nullable',
            'logo' => 'nullable|string',
        ]);
        return response()->json(Empresa::create($data), 201);
    }
    public function show(Empresa $empresa) { return $empresa; }
    public function update(Request $request, Empresa $empresa) {
        $data = $request->validate([
            'ruc' => 'required|unique:empresas,ruc,'.$empresa->id,
            'razon_social' => 'required',
            'nombre_comercial' => 'nullable',
            'direccion' => 'nullable',
            'ciudad' => 'nullable',
            'email' => 'nullable|email',
            'telefono' => 'nullable',
            'logo' => 'nullable|string',
        ]);
        $empresa->update($data);
        return $empresa;
    }
    public function destroy(Empresa $empresa) { $empresa->delete(); return response()->json(['message' => 'Eliminada']); }
}
