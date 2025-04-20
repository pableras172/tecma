<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'direccion',
        'city_id',
        'telefono1',
        'telefono2',
        'contacto',
        'email',
        'logo',
        'observaciones',
    ];

    public function ciudad()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function plantas()
    {
        return $this->hasMany(Planta::class);
    }
}
