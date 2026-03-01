<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\PermisoModulo;
use Illuminate\Http\Request;

class PermisoController extends Controller {
    public function index(Request $request) {
        return PermisoModulo::where('user_id', $request->user_id)->where('empresa_id', $request->empresa_id)->get();
    }
    public function sync(Request $request) {
        $data = $request->validate(['user_id'=>'required','empresa_id'=>'required','modulos'=>'required|array']);
        PermisoModulo::where('user_id',$data['user_id'])->where('empresa_id',$data['empresa_id'])->delete();
        foreach ($data['modulos'] as $m) {
            PermisoModulo::create(['user_id'=>$data['user_id'],'empresa_id'=>$data['empresa_id'],'modulo'=>$m]);
        }
        return response()->json(['message'=>'Permisos actualizados']);
    }
}
