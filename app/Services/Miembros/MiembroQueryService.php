<?php

namespace App\Services\Miembros;

use App\Models\Miembro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MiembroQueryService
{
    /**
     * Obtiene un listado paginado de miembros con filtros aplicados.
     */
    public function paginate(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Miembro::with('organizacion');

        $this->applyFilters($query, $filters);

        $paginator = $query->paginate($perPage);

        return $paginator->appends(array_filter($filters));
    }

    /**
     * Obtiene un listado paginado optimizado para consumo vía API.
     */
    public function paginateForApi(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = Miembro::with(['organizacion', 'estadoMembresia'])
            ->select([
                'id',
                'nombre_completo',
                'email',
                'numero_carnet',
                'estado_membresia_id',
                'fecha_ingreso',
                'foto_url',
                'organizacion_id',
            ]);

        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Retorna una colección lista para exportarse.
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = Miembro::with('organizacion');

        $this->applyFilters($query, $filters);

        return $query->get();
    }

    /**
     * Busca miembros rápidamente por nombre, cédula o carnet.
     */
    public function search(string $term, int $limit = 10): Collection
    {
        if (mb_strlen($term) < 2) {
            return collect();
        }

        return Miembro::buscar($term)
            ->with(['organizacion', 'estadoMembresia'])
            ->select([
                'id',
                'nombre_completo',
                'numero_carnet',
                'estado_membresia_id',
                'foto_url',
                'organizacion_id',
            ])
            ->limit($limit)
            ->get();
    }

    /**
     * Obtiene estadísticas globales del módulo de miembros.
     */
    public function getEstadisticas(): array
    {
        return [
            'total_miembros' => Miembro::count(),
            'miembros_activos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'activa');
            })->count(),
            'miembros_suspendidos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'suspendida');
            })->count(),
            'miembros_inactivos' => Miembro::whereHas('estadoMembresia', function($query) {
                $query->where('nombre', 'inactiva');
            })->count(),
            'por_estado' => Miembro::join('estados_membresia', 'miembros.estado_membresia_id', '=', 'estados_membresia.id')
                ->select('estados_membresia.nombre as estado', DB::raw('count(*) as cantidad'))
                ->groupBy('estados_membresia.nombre')
                ->get(),
            'por_organizacion' => Miembro::with('organizacion')
                ->select('organizacion_id', DB::raw('count(*) as cantidad'))
                ->groupBy('organizacion_id')
                ->get(),
            'nuevos_este_mes' => Miembro::whereMonth('fecha_ingreso', now()->month)
                ->whereYear('fecha_ingreso', now()->year)
                ->count(),
        ];
    }

    /**
     * Aplica filtros al query builder recibido.
     */
    protected function applyFilters(Builder $query, array $filters = []): Builder
    {
        if (!empty($filters['buscar'])) {
            $query->buscar($filters['buscar']);
        }

        if (!empty($filters['estado_membresia_id'])) {
            $query->where('estado_membresia_id', $filters['estado_membresia_id']);
        }

        if (!empty($filters['tipo_membresia'])) {
            $query->where('tipo_membresia', $filters['tipo_membresia']);
        }

        if (!empty($filters['organizacion_id'])) {
            $query->porOrganizacion($filters['organizacion_id']);
        }

        return $query;
    }
}
