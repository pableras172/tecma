<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TipoTrabajo extends Model
{
    use HasFactory;
    protected $table = 'tipos_trabajo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    public function partesTrabajo(): HasMany
    {
        return $this->hasMany(ParteTrabajo::class);
    }
}
