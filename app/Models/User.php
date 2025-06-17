<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements FilamentUser, HasAvatar,Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use \OwenIt\Auditing\Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'telefono',
        'direccion',
        'fecha_nacimiento',
        'fecha_ingreso',
        'categoria_profesional_id',
        'city_id',
        'foto',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function categoriaProfesional()
    {
        return $this->belongsTo(CategoriaProfesional::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->foto;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
    public function tareas()
    {
        return $this->belongsToMany(Tarea::class, 'tarea_usuario');
    }

}
