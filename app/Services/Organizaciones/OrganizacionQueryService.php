<?php

namespace App\Services\Organizaciones;

use App\Models\Organizacion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class OrganizacionQueryService
{
    /**
     * Paginate organizations with filters
     */
    public function paginate(array $filters = [], int $perPage = 25): LengthAwarePaginator
    {
        $query = Organizacion::query();

        // Aplicar filtros
        if (!empty($filters['buscar'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('codigo', 'like', '%' . $filters['buscar'] . '%')
                  ->orWhere('email', 'like', '%' . $filters['buscar'] . '%');
            });
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Ordenar por nombre
        $query->orderBy('nombre', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Paginate organizations for API
     */
    public function paginateForApi(array $filters = [], int $perPage = 20): array
    {
        $organizaciones = $this->paginate($filters, $perPage);

        return [
            'data' => $organizaciones->items(),
            'pagination' => [
                'current_page' => $organizaciones->currentPage(),
                'per_page' => $organizaciones->perPage(),
                'total' => $organizaciones->total(),
                'last_page' => $organizaciones->lastPage(),
                'from' => $organizaciones->firstItem(),
                'to' => $organizaciones->lastItem(),
            ],
            'filters' => $filters,
        ];
    }

    /**
     * Search organizations
     */
    public function search(string $term): Collection
    {
        if (strlen($term) < 2) {
            return collect();
        }

        return Organizacion::where(function ($query) use ($term) {
            $query->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
        })
        ->limit(10)
        ->get()
        ->map(function ($organizacion) {
            return [
                'id' => $organizacion->id,
                'nombre' => $organizacion->nombre,
                'codigo' => $organizacion->codigo,
                'tipo' => $organizacion->tipo,
                'estado' => $organizacion->estado,
                'miembros_count' => $organizacion->miembros()->count(),
            ];
        });
    }

    /**
     * Get organization statistics
     */
    public function getEstadisticas(): array
    {
        return [
            'total' => Organizacion::count(),
            'activas' => Organizacion::where('estado', 'activa')->count(),
            'inactivas' => Organizacion::where('estado', 'inactiva')->count(),
            'suspendidas' => Organizacion::where('estado', 'suspendida')->count(),
            'nacionales' => Organizacion::where('tipo', 'nacional')->count(),
            'seccionales' => Organizacion::where('tipo', 'seccional')->count(),
            'seccionales_internacionales' => Organizacion::where('tipo', 'seccional_internacional')->count(),
            'diaspora' => Organizacion::where('tipo', 'diaspora')->count(),
            'con_miembros' => Organizacion::has('miembros')->count(),
            'sin_miembros' => Organizacion::doesntHave('miembros')->count(),
        ];
    }

    /**
     * Get organizations by type
     */
    public function porTipo(string $tipo): Collection
    {
        return Organizacion::where('tipo', $tipo)
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Get organizations by state
     */
    public function porEstado(string $estado): Collection
    {
        return Organizacion::where('estado', $estado)
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Get organizations with most members
     */
    public function conMasMiembros(int $limit = 10): Collection
    {
        return Organizacion::withCount('miembros')
            ->orderBy('miembros_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get organizations without members
     */
    public function sinMiembros(): Collection
    {
        return Organizacion::doesntHave('miembros')
            ->where('estado', 'activa')
            ->orderBy('nombre')
            ->get();
    }

    /**
     * Get organizations created in date range
     */
    public function creadasEnRango(string $fechaInicio, string $fechaFin): Collection
    {
        return Organizacion::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get organizations with upcoming events
     */
    public function conEventosProximos(int $dias = 30): Collection
    {
        $fechaLimite = now()->addDays($dias);

        return Organizacion::whereHas('asambleas', function ($query) use ($fechaLimite) {
            $query->where('fecha_asamblea', '<=', $fechaLimite)
                  ->where('estado', 'convocada');
        })
        ->orWhereHas('elecciones', function ($query) use ($fechaLimite) {
            $query->where('fecha_inicio', '<=', $fechaLimite)
                  ->where('estado', 'programada');
        })
        ->with(['asambleas', 'elecciones'])
        ->get();
    }

    /**
     * Get organization performance metrics
     */
    public function getMetricasRendimiento(int $organizacionId): array
    {
        $organizacion = Organizacion::find($organizacionId);
        
        if (!$organizacion) {
            return [];
        }

        return [
            'miembros_activos' => $organizacion->miembros()->activos()->count(),
            'miembros_nuevos_mes' => $organizacion->miembros()
                ->where('created_at', '>=', now()->subMonth())
                ->count(),
            'asambleas_realizadas' => $organizacion->asambleas()
                ->where('estado', 'realizada')
                ->count(),
            'elecciones_completadas' => $organizacion->elecciones()
                ->where('estado', 'finalizada')
                ->count(),
            'cursos_activos' => $organizacion->cursos()
                ->where('estado', 'activo')
                ->count(),
            'participacion_promedio' => $this->calcularParticipacionPromedio($organizacion),
        ];
    }

    /**
     * Calculate average participation rate
     */
    private function calcularParticipacionPromedio(Organizacion $organizacion): float
    {
        $asambleas = $organizacion->asambleas()
            ->where('estado', 'realizada')
            ->withCount('asistencias')
            ->get();

        if ($asambleas->isEmpty()) {
            return 0.0;
        }

        $totalMiembros = $organizacion->miembros()->activos()->count();
        
        if ($totalMiembros === 0) {
            return 0.0;
        }

        $participacionTotal = $asambleas->sum(function ($asamblea) use ($totalMiembros) {
            return ($asamblea->asistencias_count / $totalMiembros) * 100;
        });

        return round($participacionTotal / $asambleas->count(), 2);
    }
}

