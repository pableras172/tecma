<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;


class Country extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'iso2',
    ];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }
}
