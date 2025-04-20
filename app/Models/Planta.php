<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Planta extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono1',
        'telefono2',
        'contacto',
        'email',
        'observaciones',
        'cliente_id',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    
    public function ciudad() {
        return $this->belongsTo(City::class, 'city_id');
    }
}
