<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'planta_id',
        'titulo',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'estado',
    ];

    public function planta()
    {
        return $this->belongsTo(Planta::class);
    }


    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'tarea_usuario');
    }
}
