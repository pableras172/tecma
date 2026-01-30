<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ParteTrabajo;
use App\Models\User;

class PartesUsuarioChart extends ChartWidget
{
    protected ?string $heading = 'Partes de trabajo por usuario';

    protected static array $columns = [
        'default' => 1,
        'md' => 2,
    ];

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Obtener usuarios que tienen partes de trabajo asignados
        $usuariosConPartes = User::withCount('partesResponsable')
            ->having('partes_responsable_count', '>', 0)
            ->get();

        $labels = [];
        $conteo = [];
        $colores = [];

        // Paleta de colores para diferentes usuarios
        $paletaColores = [
            '#60a5fa', // azul
            '#34d399', // verde
            '#f472b6', // rosa
            '#fbbf24', // amarillo
            '#a78bfa', // morado
            '#fb923c', // naranja
            '#22d3ee', // cyan
            '#f87171', // rojo
        ];

        foreach ($usuariosConPartes as $index => $usuario) {
            $labels[] = $usuario->name . " ({$usuario->partes_responsable_count})";
            $conteo[] = $usuario->partes_responsable_count;
            $colores[] = $paletaColores[$index % count($paletaColores)];
        }

        return [
            'datasets' => [
                [
                    'label' => 'Partes',
                    'data' => $conteo,
                    'backgroundColor' => $colores,
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
