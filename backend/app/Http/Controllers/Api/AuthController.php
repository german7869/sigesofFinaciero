<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller {
    public function login(Request $request) {
        $request->validate(['correo' => 'required|email', 'password' => 'required']);
        $user = User::where('correo', $request->correo)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['correo' => ['Credenciales inválidas.']]);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        $user->load('empresas');
        return response()->json(['token' => $token, 'user' => $user]);
    }
    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada']);
    }
    public function me(Request $request) {
        $user = $request->user()->load('empresas');
        return response()->json($user);
    }
}
