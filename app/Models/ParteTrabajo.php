<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

class ParteTrabajo extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'parte_trabajo';

    protected $fillable = [
        'numero',
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

    /**
     * Recalcula todos los totales del parte de trabajo basándose en sus líneas
     */
    public function recalcularTotales(): void
    {
        $lineas = $this->lineasParteTrabajo;

        // Inicializar totales
        $totalHorasViaje = 0;
        $totalHorasTrabajo = 0;

        // Calcular horas de viaje y trabajo para cada línea
        foreach ($lineas as $linea) {
            // Calcular horas de viaje
            if ($linea->hora_ida && $linea->hora_llegada) {
                $totalHorasViaje += \Carbon\Carbon::parse($linea->hora_ida)->diffInMinutes(\Carbon\Carbon::parse($linea->hora_llegada)) / 60;
            }
            if ($linea->hora_vuelta && $linea->hora_vuelta_llegada) {
                $totalHorasViaje += \Carbon\Carbon::parse($linea->hora_vuelta)->diffInMinutes(\Carbon\Carbon::parse($linea->hora_vuelta_llegada)) / 60;
            }

            // Calcular horas de trabajo
            if ($linea->hora_inicio_trabajo && $linea->hora_fin_trabajo) {
                $totalHorasTrabajo += \Carbon\Carbon::parse($linea->hora_inicio_trabajo)->diffInMinutes(\Carbon\Carbon::parse($linea->hora_fin_trabajo)) / 60;
            }
            if ($linea->hora_inicio_trabajo_2 && $linea->hora_fin_trabajo_2) {
                $totalHorasTrabajo += \Carbon\Carbon::parse($linea->hora_inicio_trabajo_2)->diffInMinutes(\Carbon\Carbon::parse($linea->hora_fin_trabajo_2)) / 60;
            }
        }

        // Actualizar el parte de trabajo
        $this->update([
            'total_horas_viaje' => round($totalHorasViaje, 2),
            'total_horas_trabajo' => round($totalHorasTrabajo, 2),
            'total_ht1' => round($lineas->sum('ht1'), 2),
            'total_ht2' => round($lineas->sum('ht2'), 2),
            'total_hve' => round($lineas->sum('hve'), 2),
            'total_km' => $lineas->sum('kms'),
            'total_media_dieta' => $lineas->where('media_dieta', true)->count(),
            'total_dieta' => $lineas->where('dieta_completa', true)->count(),
            'total_hotel' => $lineas->where('hotel', true)->count(),
        ]);
    }
}
