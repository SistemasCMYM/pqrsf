<?php

namespace App\Http\Controllers\Pqrsf;

use App\Http\Controllers\Controller;
use App\Models\Pqrsf;
use Illuminate\View\View;

class PqrsfDashboardController extends Controller
{
    public function __invoke(): View
    {
        $query = Pqrsf::query();

        return view('pqrsf.dashboard', [
            'total' => (clone $query)->count(),
            'abiertas' => (clone $query)->whereHas('estado', fn ($q) => $q->whereIn('slug', ['radicada', 'en-revision', 'asignada', 'en-gestion', 'pendiente-informacion']))->count(),
            'cerradas' => (clone $query)->whereHas('estado', fn ($q) => $q->whereIn('slug', ['cerrada', 'respondida']))->count(),
            'vencidas' => (clone $query)->where('vencida', true)->count(),
            'porTipo' => Pqrsf::query()->with('tipo')->get()->groupBy(fn ($item) => $item->tipo?->nombre ?? 'Sin tipo')->map->count(),
            'proximasVencer' => Pqrsf::query()->whereNotNull('fecha_limite_respuesta')->whereBetween('fecha_limite_respuesta', [now(), now()->addDays(2)])->count(),
        ]);
    }
}
