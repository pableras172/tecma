<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class ParteTrabajo extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'partes_trabajo';

    protected $fillable = [
        'numero',
        'codigo',
        'anio',
        'tarea_id',
        'fecha_parte',
        'tipo_trabajo_id',
        'cliente_id',
        'planta_id',
        'horas_motor',
        'arranques',
        'modelo',
        'numero_motor',
        'comentarios',
        'created_by',
        'user_responsable_id',
        'estado',
        'trabajo_realizado',
        'total_horas_viaje',
        'total_horas_trabajo',
        'total_ht1',
        'total_ht2',
        'total_hve',
        'total_km',
        'total_media_dieta',
        'total_dieta',
        'total_hotel',
        'firma_tecnico',
        'firma_supervisor',
    ];

    protected $casts = [
        'fecha_parte' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Accessors & Mutators
    |--------------------------------------------------------------------------
    */

    /**
     * Obtener la URL pública de la firma del técnico
     */
    public function getFirmaTecnicoUrlAttribute(): ?string
    {
        if (!$this->firma_tecnico) {
            return null;
        }
        
        // Si ya es una URL completa, devolverla tal cual
        if (str_starts_with($this->firma_tecnico, 'http')) {
            return $this->firma_tecnico;
        }
        
        return asset('storage/' . $this->firma_tecnico);
    }

    /**
     * Obtener la URL pública de la firma del supervisor
     */
    public function getFirmaSupervisorUrlAttribute(): ?string
    {
        if (!$this->firma_supervisor) {
            return null;
        }
        
        // Si ya es una URL completa, devolverla tal cual
        if (str_starts_with($this->firma_supervisor, 'http')) {
            return $this->firma_supervisor;
        }
        
        return asset('storage/' . $this->firma_supervisor);
    }

    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function tipoTrabajo()
    {
        return $this->belongsTo(TipoTrabajo::class);
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'user_responsable_id');
    }

    public function lineas()
    {
        return $this->hasMany(LineaParteTrabajo::class);
    }

    public function lineasParteTrabajo()
    {
        return $this->hasMany(LineaParteTrabajo::class);
    }

    public function docs()
    {
        return $this->hasMany(Doc::class);
    }

    public function resumenCategorias()
    {
        return $this->hasMany(ResumenParteTrabajoCategoria::class);
    }

    /**
     * Recalcula todos los totales del parte de trabajo basándose en sus líneas
     */
    public function recalcularTotales(): void
    {
        $lineas = $this->lineasParteTrabajo;
        $resumenPorCategoria = $this->calcularResumenPorCategoriaDesdeLineas();

        DB::transaction(function () use ($resumenPorCategoria): void {
            $this->resumenCategorias()->delete();

            foreach ($resumenPorCategoria as $fila) {
                $this->resumenCategorias()->create([
                    'categoria_profesional_id' => $fila['categoria_profesional_id'],
                    'categoria_nombre' => $fila['categoria'],
                    'horas_viaje' => $fila['horas_viaje'],
                    'horas_trabajo' => $fila['horas_trabajo'],
                    'ht1' => $fila['ht1'],
                    'ht2' => $fila['ht2'],
                    'hve' => $fila['hve'],
                ]);
            }
        });

        // Actualizar el parte de trabajo
        $this->update([
            'total_horas_viaje' => round($resumenPorCategoria->sum('horas_viaje'), 2),
            'total_horas_trabajo' => round($resumenPorCategoria->sum('horas_trabajo'), 2),
            'total_ht1' => round($resumenPorCategoria->sum('ht1'), 2),
            'total_ht2' => round($resumenPorCategoria->sum('ht2'), 2),
            'total_hve' => round($resumenPorCategoria->sum('hve'), 2),
            'total_km' => $lineas->sum('kms'),
            'total_media_dieta' => $lineas->where('media_dieta', true)->count(),
            'total_dieta' => $lineas->where('dieta_completa', true)->count(),
            'total_hotel' => $lineas->where('hotel', true)->count(),
        ]);
    }

    /**
     * Genera líneas de resumen por categoría profesional de técnicos.
     */
    public function obtenerResumenPorCategoria(): Collection
    {
        if ($this->relationLoaded('resumenCategorias')) {
            $resumen = $this->resumenCategorias;
        } else {
            $resumen = $this->resumenCategorias()->orderBy('categoria_nombre')->get();
        }

        if ($resumen->isNotEmpty()) {
            return $resumen->map(function (ResumenParteTrabajoCategoria $fila): array {
                return [
                    'categoria_profesional_id' => $fila->categoria_profesional_id,
                    'categoria' => $fila->categoria_nombre,
                    'horas_viaje' => round((float) $fila->horas_viaje, 2),
                    'horas_trabajo' => round((float) $fila->horas_trabajo, 2),
                    'ht1' => round((float) $fila->ht1, 2),
                    'ht2' => round((float) $fila->ht2, 2),
                    'hve' => round((float) $fila->hve, 2),
                ];
            })->values();
        }

        // Compatibilidad para partes antiguos sin resumen persistido.
        return $this->calcularResumenPorCategoriaDesdeLineas();
    }

    protected function calcularResumenPorCategoriaDesdeLineas(): Collection
    {
        $lineas = $this->lineasParteTrabajo()->with('usuarios.categoriaProfesional')->get();
        $resumen = [];

        foreach ($lineas as $linea) {
            $valoresLinea = [
                'horas_viaje' => $this->calcularHorasViajeNormalesLinea($linea),
                'horas_trabajo' => $this->calcularHorasTrabajoNormalesLinea($linea),
                'ht1' => (float) $linea->ht1,
                'ht2' => (float) $linea->ht2,
                'hve' => (float) $linea->hve,
            ];

            $usuariosPorCategoria = $linea->usuarios->groupBy(fn (User $usuario) => $usuario->categoria_profesional_id ?? 'sin_categoria');

            if ($usuariosPorCategoria->isEmpty()) {
                $this->acumularResumenCategoria($resumen, null, 'Sin categoria', 1, $valoresLinea);
                continue;
            }

            foreach ($usuariosPorCategoria as $categoriaKey => $usuariosCategoria) {
                $categoriaId = is_numeric($categoriaKey) ? (int) $categoriaKey : null;
                $categoriaNombre = $usuariosCategoria->first()?->categoriaProfesional?->nombre ?? 'Sin categoria';
                $this->acumularResumenCategoria($resumen, $categoriaId, $categoriaNombre, $usuariosCategoria->count(), $valoresLinea);
            }
        }

        return collect(array_values($resumen))
            ->map(function (array $fila): array {
                $fila['horas_viaje'] = round($fila['horas_viaje'], 2);
                $fila['horas_trabajo'] = round($fila['horas_trabajo'], 2);
                $fila['ht1'] = round($fila['ht1'], 2);
                $fila['ht2'] = round($fila['ht2'], 2);
                $fila['hve'] = round($fila['hve'], 2);

                return $fila;
            })
            ->sortBy('categoria')
            ->values();
    }

    protected function acumularResumenCategoria(array &$resumen, ?int $categoriaId, string $categoriaNombre, int $factor, array $valoresLinea): void
    {
        $categoriaKey = $categoriaId !== null ? (string) $categoriaId : 'sin_categoria';

        if (!isset($resumen[$categoriaKey])) {
            $resumen[$categoriaKey] = [
                'categoria_profesional_id' => $categoriaId,
                'categoria' => $categoriaNombre,
                'horas_viaje' => 0.0,
                'horas_trabajo' => 0.0,
                'ht1' => 0.0,
                'ht2' => 0.0,
                'hve' => 0.0,
            ];
        }

        $resumen[$categoriaKey]['horas_viaje'] += $valoresLinea['horas_viaje'] * $factor;
        $resumen[$categoriaKey]['horas_trabajo'] += $valoresLinea['horas_trabajo'] * $factor;
        $resumen[$categoriaKey]['ht1'] += $valoresLinea['ht1'] * $factor;
        $resumen[$categoriaKey]['ht2'] += $valoresLinea['ht2'] * $factor;
        $resumen[$categoriaKey]['hve'] += $valoresLinea['hve'] * $factor;
    }

    protected function calcularHorasViajeNormalesLinea(LineaParteTrabajo $linea): float
    {
        $total = 0;

        if ($linea->hora_ida && $linea->hora_llegada) {
            $horasViaje = Carbon::parse($linea->hora_ida)->diffInMinutes(Carbon::parse($linea->hora_llegada)) / 60;
            $horasViajeExtra = $this->calcularHorasExtraRango((string) $linea->hora_ida, (string) $linea->hora_llegada);
            $total += ($horasViaje - $horasViajeExtra);
        }

        if ($linea->hora_vuelta && $linea->hora_vuelta_llegada) {
            $horasViaje = Carbon::parse($linea->hora_vuelta)->diffInMinutes(Carbon::parse($linea->hora_vuelta_llegada)) / 60;
            $horasViajeExtra = $this->calcularHorasExtraRango((string) $linea->hora_vuelta, (string) $linea->hora_vuelta_llegada);
            $total += ($horasViaje - $horasViajeExtra);
        }

        return $total;
    }

    protected function calcularHorasTrabajoNormalesLinea(LineaParteTrabajo $linea): float
    {
        $total = 0;

        if ($linea->hora_inicio_trabajo && $linea->hora_fin_trabajo) {
            $horasTrabajo = Carbon::parse($linea->hora_inicio_trabajo)->diffInMinutes(Carbon::parse($linea->hora_fin_trabajo)) / 60;
            $horasTrabajoExtra = $this->calcularHorasExtraRango((string) $linea->hora_inicio_trabajo, (string) $linea->hora_fin_trabajo);
            $total += ($horasTrabajo - $horasTrabajoExtra);
        }

        if ($linea->hora_inicio_trabajo_2 && $linea->hora_fin_trabajo_2) {
            $horasTrabajo = Carbon::parse($linea->hora_inicio_trabajo_2)->diffInMinutes(Carbon::parse($linea->hora_fin_trabajo_2)) / 60;
            $horasTrabajoExtra = $this->calcularHorasExtraRango((string) $linea->hora_inicio_trabajo_2, (string) $linea->hora_fin_trabajo_2);
            $total += ($horasTrabajo - $horasTrabajoExtra);
        }

        return $total;
    }

    protected function calcularHorasExtraRango(string $horaInicio, string $horaFin): float
    {
        Log::debug('calcularHorasExtraRango params', [
            'horaInicio' => $horaInicio,
            'horaFin' => $horaFin,
        ]);
        $horaEntrada = setting('horarios.hora_entrada', '08:00');
        $horasSalida = setting('horarios.hora_salida', '17:00');

        // Normalizar a formato H:i si viene con fecha
        $horaInicioFmt = preg_match('/\d{2}:\d{2}:\d{2}/', $horaInicio) ? Carbon::parse($horaInicio)->format('H:i') : $horaInicio;
        $horaFinFmt = preg_match('/\d{2}:\d{2}:\d{2}/', $horaFin) ? Carbon::parse($horaFin)->format('H:i') : $horaFin;

        // Convertir a objetos Carbon para facilitar los cálculos
        $inicio = Carbon::createFromFormat('H:i', $horaInicioFmt);
        $fin = Carbon::createFromFormat('H:i', $horaFinFmt);
        $entrada = Carbon::createFromFormat('H:i', $horaEntrada);
        $salida = Carbon::createFromFormat('H:i', $horasSalida);

        $horasExtra = 0;

        // Si el rango de trabajo termina antes de empezar, ajustar (pasa por medianoche)
        if ($fin->lt($inicio)) {
            $fin->addDay();
        }

        // Calcular horas antes de las 08:00
        if ($inicio->lt($entrada)) {
            $finAntesEntrada = $fin->lt($entrada) ? $fin : $entrada;
            $horasExtra += $inicio->diffInMinutes($finAntesEntrada) / 60;
        }

        // Calcular horas después de las 17:00
        if ($fin->gt($salida)) {
            $inicioDespuesSalida = $inicio->gt($salida) ? $inicio : $salida;
            $horasExtra += $inicioDespuesSalida->diffInMinutes($fin) / 60;
        }

        return $horasExtra;
    }
}
