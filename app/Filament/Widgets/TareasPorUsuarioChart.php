<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class TareasPorUsuarioChart extends ChartWidget
{
    protected static ?string $heading = 'Tareas por usuario';
    protected static ?string $pollingInterval = null; // desactiva auto-refresh
    protected static ?int $sort = 1;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $usuarios = User::withCount('tareas')->get();

        $labels = [];
        $data = [];

        foreach ($usuarios as $usuario) {
            $labels[] = "{$usuario->name} ({$usuario->tareas_count})";
            $data[] = $usuario->tareas_count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tareas asignadas',
                    'data' => $data,
                    'backgroundColor' => collect(['#fde68a', '#a5f3fc', '#c4b5fd', '#bbf7d0', '#fca5a5', '#fdba74'])
                        ->pad(count($data), '#ddd') // por si hay más usuarios que colores
                        ->toArray(),
                    'borderColor' => '#ffffff',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
