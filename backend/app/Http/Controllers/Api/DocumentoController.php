<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller {
    public function index(Request $request) {
        return Documento::where('empresa_id', $request->empresa_id)->get();
    }
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required|exists:empresas,id',
            'codigo' => 'required',
            'descripcion' => 'required',
            'modulo' => 'required',
            'cod_establecimiento' => 'nullable',
            'punto_emision' => 'nullable',
            'secuencia_actual' => 'nullable|integer',
            'tipo_numeracion' => 'nullable',
        ]);
        return response()->json(Documento::create($data), 201);
    }
    public function show(Documento $documento) { return $documento; }
    public function update(Request $request, Documento $documento) { $documento->update($request->all()); return $documento; }
    public function destroy(Documento $documento) { $documento->delete(); return response()->json(['message' => 'Eliminado']); }
}
