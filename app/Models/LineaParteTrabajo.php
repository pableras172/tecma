<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LineaParteTrabajo extends Model
{
     use HasFactory;
    protected $table = 'lineas_parte_trabajo';

    protected $fillable = [
        'parte_trabajo_id',
        'fecha',
        'hora_ida',
        'hora_llegada',
        'hora_inicio_trabajo',
        'hora_fin_trabajo',
        'hora_inicio_trabajo_2',
        'hora_fin_trabajo_2',
        'hora_vuelta',
        'hora_vuelta_llegada',
        'he',
        'hs',
        'ht1',
        'ht2',
        'hve',
        'kms',
        'media_dieta',
        'dieta_completa',
        'hotel',
    ];

    protected $casts = [
        'fecha' => 'date',

        'hora_ida' => 'datetime:H:i',
        'hora_llegada' => 'datetime:H:i',
        'hora_inicio_trabajo' => 'datetime:H:i',
        'hora_fin_trabajo' => 'datetime:H:i',
        'hora_inicio_trabajo_2' => 'datetime:H:i',
        'hora_fin_trabajo_2' => 'datetime:H:i',
        'hora_vuelta' => 'datetime:H:i',
        'hora_vuelta_llegada' => 'datetime:H:i',

        'media_dieta' => 'boolean',
        'dieta_completa' => 'boolean',
        'hotel' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relaciones
    |--------------------------------------------------------------------------
    */

    public function parteTrabajo()
    {
        return $this->belongsTo(ParteTrabajo::class);
    }

    /**
     * Usuarios asignados a esta línea de trabajo
     */
    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'linea_parte_trabajo_user');
    }
}