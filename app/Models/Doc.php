<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    protected $fillable = [
        'nombre_documento',
        'fecha',
        'ruta',
        'parte_trabajo_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    public function parteTrabajo()
    {
        return $this->belongsTo(ParteTrabajo::class);
    }
}
