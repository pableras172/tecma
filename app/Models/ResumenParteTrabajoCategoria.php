<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResumenParteTrabajoCategoria extends Model
{
    use HasFactory;

    protected $table = 'resumen_parte_trabajo_categorias';

    protected $fillable = [
        'parte_trabajo_id',
        'categoria_profesional_id',
        'categoria_nombre',
        'horas_viaje',
        'horas_trabajo',
        'ht1',
        'ht2',
        'hve',
    ];

    public function parteTrabajo()
    {
        return $this->belongsTo(ParteTrabajo::class);
    }

    public function categoriaProfesional()
    {
        return $this->belongsTo(CategoriaProfesional::class);
    }
}
