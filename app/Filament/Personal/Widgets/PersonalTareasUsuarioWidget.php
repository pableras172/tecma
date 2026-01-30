<?php

namespace App\Filament\Personal\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use App\Models\Tarea;
use Filament\Facades\Filament;

class PersonalTareasUsuarioWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();

        // Determinar el panel actual y la ruta correspondiente
        $panelId = Filament::getCurrentOrDefaultPanel()->getId();
        $tareasUrl = $panelId === 'dashboard' ? '/dashboard/tareas' : '/personal/tareas';

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
                ->icon('heroicon-o-clock')
                ->url($tareasUrl),

            Stat::make('Tareas en curso', $tareasEnCurso)
                ->description('Tareas en progreso')
                ->color('warning')
                ->icon('heroicon-o-arrow-path')
                ->url($tareasUrl),

            Stat::make('Tareas finalizadas', $tareasFinalizadas)
                ->description('Tareas completadas')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->url($tareasUrl),
        ];
    }
}
