<?php

namespace App\Filament\Personal\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\ParteTrabajo;

class PartesEstadoChart extends ChartWidget
{
    protected ?string $heading = 'Partes de trabajo por estado';

    protected static array $columns = [
        'default' => 1,
        'md' => 2,
    ];

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $user = Auth::user();

        $estados = [
            'borrador' => 'Borrador',
            'cerrado' => 'Cerrado',
            'facturado' => 'Facturado',
        ];

        $conteo = [];
        $labels = [];

        foreach ($estados as $estado => $label) {
            $count = ParteTrabajo::where('user_responsable_id', $user->id)
                ->where('estado', $estado)
                ->count();
            $conteo[] = $count;
            $labels[] = "{$label} ({$count})";
        }

        return [
            'datasets' => [
                [
                    'label' => 'Partes',
                    'data' => $conteo,
                    'backgroundColor' => [
                        '#d1d5db', // gris
                        '#fcd34d', // amarillo/warning
                        '#86efac', // verde/success
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
