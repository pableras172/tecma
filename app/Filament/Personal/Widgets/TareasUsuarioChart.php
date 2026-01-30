<?php

namespace App\Filament\Personal\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class TareasUsuarioChart extends ChartWidget
{
    protected ?string $heading = 'Tareas por estado';
    

    // o para más control por breakpoints:
    protected static array $columns = [
        'default' => 1,
        'md' => 2,
    ];

    protected static ?int $sort = 1;

    protected function getData(): array
{
    $user = Auth::user();

    $estados = [
        'pendiente' => 'Pendientes',
        'progreso' => 'En progreso',
        'finalizada' => 'Finalizadas',
    ];

    $conteo = [];
    $labels = [];

    foreach ($estados as $estado => $label) {
        $count = $user->tareas()->where('estado', $estado)->count();
        $conteo[] = $count;
        $labels[] = "{$label} ({$count})";
    }

    return [
        'datasets' => [
            [
                'label' => 'Tareas',
                'data' => $conteo,
                'backgroundColor' => [
                    '#fef08a', // amarillo pastel
                    '#bfdbfe', // azul pastel
                    '#bbf7d0', // verde pastel
                ],
                'borderColor' => '#ffffff',
                'borderWidth' => 2,
            ],
        ],
        'labels' => $labels,
    ];
}




    protected function getType(): string
    {
        return 'doughnut';
    }
}
