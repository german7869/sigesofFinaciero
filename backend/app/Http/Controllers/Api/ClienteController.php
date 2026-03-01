<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller {
    public function index(Request $request) {
        $q = Cliente::where('empresa_id', $request->empresa_id);
        if ($request->search) $q->where(function($query) use ($request) {
            $query->where('nombres', 'like', '%'.$request->search.'%')->orWhere('identificacion', 'like', '%'.$request->search.'%');
        });
        return $q->get();
    }
    public function store(Request $request) {
        $data = $request->validate(['empresa_id'=>'required','identificacion'=>'required','nombres'=>'required','direccion'=>'nullable','telefono'=>'nullable','correo'=>'nullable|email','ciudad'=>'nullable']);
        return response()->json(Cliente::create($data), 201);
    }
    public function show(Cliente $cliente) { return $cliente; }
    public function update(Request $request, Cliente $cliente) { $cliente->update($request->all()); return $cliente; }
    public function destroy(Cliente $cliente) { $cliente->delete(); return response()->json(['message'=>'Eliminado']); }
}
