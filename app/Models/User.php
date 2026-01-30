<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar,Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;
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
        'departamento_id',
        'city_id',
        'foto',
        'active',
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
            'active' => 'boolean',
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
        if (!$this->foto) {
            return null;
        }
        
        // Si ya es una URL completa, devolverla tal cual
        if (str_starts_with($this->foto, 'http')) {
            return $this->foto;
        }
        
        // Convertir ruta a URL pública
        return asset('storage/' . $this->foto);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (!$this->active) {
            return false;
        }

        //Mostrar en el log el id del panel
        \Log::info('Panel Access Attempt', [
            'panel_id' => $panel->getId(),
            'user_id' => $this->id,
            'user_email' => $this->email,
        ]);
        
        return match ($panel->getId()) {
            'dashboard' => $this->hasRole('admin'),
            'personal'  => $this->hasRole('empleado') || $this->hasRole('admin'),
            default     => false,
        };
    }


    public function tareas()
    {
        return $this->belongsToMany(Tarea::class, 'tarea_usuario');
    }

    /**
     * Líneas de parte de trabajo asignadas a este usuario
     */
    public function lineasParteTrabajo()
    {
        return $this->belongsToMany(LineaParteTrabajo::class, 'linea_parte_trabajo_user');
    }

    /**
     * Partes de trabajo de los cuales este usuario es responsable
     */
    public function partesResponsable()
    {
        return $this->hasMany(ParteTrabajo::class, 'user_responsable_id');
    }

}
