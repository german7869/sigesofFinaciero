<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Asiento;
use App\Models\AsientoDetalle;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AsientoController extends Controller {
    public function index(Request $request) {
        $query = Asiento::with(['detalles.cuenta', 'documento'])
            ->where('empresa_id', $request->empresa_id);
        if ($request->desde) $query->whereDate('fecha', '>=', $request->desde);
        if ($request->hasta) $query->whereDate('fecha', '<=', $request->hasta);
        if ($request->documento_id) $query->where('documento_id', $request->documento_id);
        return $query->orderBy('fecha')->orderBy('id')->get();
    }
    public function store(Request $request) {
        $data = $request->validate([
            'empresa_id' => 'required',
            'fecha' => 'required|date',
            'documento_id' => 'nullable|exists:documentos,id',
            'concepto' => 'required',
            'beneficiario' => 'nullable',
            'detalles' => 'required|array|min:2',
            'detalles.*.cuenta_contable_id' => 'required|exists:cuentas_contables,id',
            'detalles.*.debe' => 'required|numeric|min:0',
            'detalles.*.haber' => 'required|numeric|min:0',
        ]);
        $totalDebe = collect($data['detalles'])->sum('debe');
        $totalHaber = collect($data['detalles'])->sum('haber');
        if (round($totalDebe, 2) !== round($totalHaber, 2)) {
            return response()->json(['message' => 'El total Debe debe ser igual al total Haber'], 422);
        }
        return DB::transaction(function () use ($data, $request) {
            $numero = '';
            if (!empty($data['documento_id'])) {
                $doc = Documento::find($data['documento_id']);
                $numero = $doc->generarNumero();
            }
            $asiento = Asiento::create([
                'empresa_id' => $data['empresa_id'],
                'fecha' => $data['fecha'],
                'documento_id' => $data['documento_id'] ?? null,
                'numero' => $numero,
                'concepto' => $data['concepto'],
                'beneficiario' => $data['beneficiario'] ?? null,
                'origen_modulo' => $request->origen_modulo ?? null,
            ]);
            foreach ($data['detalles'] as $det) {
                $asiento->detalles()->create($det);
            }
            return response()->json($asiento->load('detalles.cuenta'), 201);
        });
    }
    public function show(Asiento $asiento) { return $asiento->load('detalles.cuenta', 'documento'); }
    
    public function libroDiario(Request $request) {
        $request->validate(['empresa_id' => 'required', 'desde' => 'required|date', 'hasta' => 'required|date']);
        return Asiento::with(['detalles.cuenta'])
            ->where('empresa_id', $request->empresa_id)
            ->whereBetween('fecha', [$request->desde, $request->hasta])
            ->orderBy('fecha')->orderBy('id')
            ->get();
    }
    
    public function mayorCuenta(Request $request) {
        $request->validate(['empresa_id' => 'required', 'cuenta_contable_id' => 'required', 'desde' => 'required|date', 'hasta' => 'required|date']);
        return AsientoDetalle::with(['asiento'])
            ->where('cuenta_contable_id', $request->cuenta_contable_id)
            ->whereHas('asiento', fn($q) => $q
                ->where('empresa_id', $request->empresa_id)
                ->whereBetween('fecha', [$request->desde, $request->hasta]))
            ->get();
    }
}
