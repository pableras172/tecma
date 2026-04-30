<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function docs()
    {
        return $this->hasMany(Doc::class);
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

        // Calcular horas normales (totales - extra) para cada línea
        foreach ($lineas as $linea) {
            // Horas de viaje normales
            if ($linea->hora_ida && $linea->hora_llegada) {
                $horasViaje = Carbon::parse($linea->hora_ida)->diffInMinutes(Carbon::parse($linea->hora_llegada)) / 60;
                $horasViajeExtra = $this->calcularHorasExtraRango($linea->hora_ida, $linea->hora_llegada);
                $totalHorasViaje += ($horasViaje - $horasViajeExtra);
            }
            if ($linea->hora_vuelta && $linea->hora_vuelta_llegada) {
                $horasViaje = Carbon::parse($linea->hora_vuelta)->diffInMinutes(Carbon::parse($linea->hora_vuelta_llegada)) / 60;
                $horasViajeExtra = $this->calcularHorasExtraRango($linea->hora_vuelta, $linea->hora_vuelta_llegada);
                $totalHorasViaje += ($horasViaje - $horasViajeExtra);
            }

            // Horas de trabajo normales
            if ($linea->hora_inicio_trabajo && $linea->hora_fin_trabajo) {
                $horasTrabajo = Carbon::parse($linea->hora_inicio_trabajo)->diffInMinutes(Carbon::parse($linea->hora_fin_trabajo)) / 60;
                $horasTrabajoExtra = $this->calcularHorasExtraRango($linea->hora_inicio_trabajo, $linea->hora_fin_trabajo);
                $totalHorasTrabajo += ($horasTrabajo - $horasTrabajoExtra);
            }
            if ($linea->hora_inicio_trabajo_2 && $linea->hora_fin_trabajo_2) {
                $horasTrabajo = Carbon::parse($linea->hora_inicio_trabajo_2)->diffInMinutes(Carbon::parse($linea->hora_fin_trabajo_2)) / 60;
                $horasTrabajoExtra = $this->calcularHorasExtraRango($linea->hora_inicio_trabajo_2, $linea->hora_fin_trabajo_2);
                $totalHorasTrabajo += ($horasTrabajo - $horasTrabajoExtra);
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
