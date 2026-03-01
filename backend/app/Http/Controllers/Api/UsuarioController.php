<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller {
    public function index() { return User::with('empresas')->get(); }
    public function store(Request $request) {
        $data = $request->validate([
            'nombres' => 'required',
            'correo' => 'required|email|unique:users,correo',
            'password' => 'required|min:6',
            'rol' => 'required|in:admin,contador,auxiliar,cajero',
        ]);
        $data['password'] = Hash::make($data['password']);
        return response()->json(User::create($data), 201);
    }
    public function show(User $usuario) { return $usuario->load('empresas'); }
    public function update(Request $request, User $usuario) {
        $data = $request->validate([
            'nombres' => 'required',
            'correo' => 'required|email|unique:users,correo,'.$usuario->id,
            'password' => 'nullable|min:6',
            'rol' => 'required|in:admin,contador,auxiliar,cajero',
        ]);
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }
        $usuario->update($data);
        return $usuario;
    }
    public function destroy(User $usuario) { $usuario->delete(); return response()->json(['message' => 'Eliminado']); }
    public function asignarEmpresas(Request $request, User $usuario) {
        $request->validate(['empresas' => 'required|array']);
        $usuario->empresas()->sync($request->empresas);
        return $usuario->load('empresas');
    }
}
