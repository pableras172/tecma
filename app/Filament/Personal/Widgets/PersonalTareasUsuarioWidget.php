<?php

namespace App\Filament\Personal\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Tarea;

class PersonalTareasUsuarioWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        $tareasPendientes = Tarea::where('estado', 'pendiente')
            ->whereHas('usuarios', fn ($q) => $q->where('users.id', $userId))
            ->count();

        $tareasEnCurso = Tarea::where('estado', 'progreso')
            ->whereHas('usuarios', fn ($q) => $q->where('users.id', $userId))
            ->count();

        $tareasFinalizadas = Tarea::where('estado', 'finalizada')
            ->whereHas('usuarios', fn ($q) => $q->where('users.id', $userId))
            ->count();

        return [
            Stat::make('Tareas pendientes', $tareasPendientes)
                ->description('Tareas aún no iniciadas')
                ->color('gray')
                ->icon('heroicon-o-clock'),

            Stat::make('Tareas en curso', $tareasEnCurso)
                ->description('Tareas en progreso')
                ->color('warning')
                ->icon('heroicon-o-arrow-path'),

            Stat::make('Tareas finalizadas', $tareasFinalizadas)
                ->description('Tareas completadas')
                ->color('success')
                ->icon('heroicon-o-check-circle'),
        ];
    }
}
