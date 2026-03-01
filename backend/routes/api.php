<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\{
    AuthController, EmpresaController, UsuarioController, DocumentoController,
    FormaPagoController, ImpuestoIvaController, ModeloContableController,
    CuentaContableController, AsientoController, ClienteController, ProductoController,
    VentaController, CobroController, ProveedorController, CompraController, PermisoController
};

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    
    Route::apiResource('empresas', EmpresaController::class);
    Route::apiResource('usuarios', UsuarioController::class);
    Route::post('usuarios/{usuario}/empresas', [UsuarioController::class, 'asignarEmpresas']);
    Route::apiResource('documentos', DocumentoController::class);
    Route::apiResource('formas-pago', FormaPagoController::class);
    Route::apiResource('impuestos-iva', ImpuestoIvaController::class);
    Route::apiResource('modelos-contables', ModeloContableController::class);
    Route::get('permisos', [PermisoController::class, 'index']);
    Route::post('permisos/sync', [PermisoController::class, 'sync']);
    
    Route::get('cuentas-contables/flat', [CuentaContableController::class, 'flat']);
    Route::apiResource('cuentas-contables', CuentaContableController::class);
    Route::apiResource('asientos', AsientoController::class)->only(['index','store','show']);
    Route::get('reportes/libro-diario', [AsientoController::class, 'libroDiario']);
    Route::get('reportes/mayor-cuenta', [AsientoController::class, 'mayorCuenta']);
    
    Route::apiResource('clientes', ClienteController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::get('ventas', [VentaController::class, 'index']);
    Route::post('ventas', [VentaController::class, 'store']);
    Route::get('ventas/{venta}', [VentaController::class, 'show']);
    
    Route::get('cobros', [CobroController::class, 'index']);
    Route::post('cobros', [CobroController::class, 'store']);
    
    Route::apiResource('proveedores', ProveedorController::class);
    Route::get('compras', [CompraController::class, 'index']);
    Route::post('compras', [CompraController::class, 'store']);
    Route::get('compras/{compra}', [CompraController::class, 'show']);
});
