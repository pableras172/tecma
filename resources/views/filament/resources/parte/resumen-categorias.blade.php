@php
    $record = $getRecord();
    $filas = $record ? $record->resumenCategorias()->orderBy('categoria_nombre')->get() : collect();
@endphp

@if ($filas->isEmpty())
    <x-filament::empty-state
        heading="Sin datos de resumen"
        description="Añade líneas de trabajo para que este bloque se calcule automáticamente."
        icon="heroicon-o-information-circle"
    />
@else
    <x-filament::section
        heading="Horas por categoría"
        description="Resumen acumulado por categoría profesional"
    >
        <div class="fi-ta-content-ctn fi-fixed-positioning-context">
            <div class="fi-ta-content">
                <table class="fi-ta-table">
                    <thead>
                        <tr>
                            <th class="fi-ta-header-cell">Categoría</th>
                            <th class="fi-ta-header-cell fi-align-end">H. Viaje</th>
                            <th class="fi-ta-header-cell fi-align-end">H. Trabajo</th>
                            <th class="fi-ta-header-cell fi-align-end">HT1</th>
                            <th class="fi-ta-header-cell fi-align-end">HT2</th>
                            <th class="fi-ta-header-cell fi-align-end">HVE</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($filas as $fila)
                            <tr class="fi-ta-row">
                                <td class="fi-ta-cell">
                                    <x-filament::badge color="primary">
                                        {{ $fila->categoria_nombre }}
                                    </x-filament::badge>
                                </td>
                                <td class="fi-ta-cell fi-align-end">{{ number_format($fila->horas_viaje, 2) }}</td>
                                <td class="fi-ta-cell fi-align-end">{{ number_format($fila->horas_trabajo, 2) }}</td>
                                <td class="fi-ta-cell fi-align-end">{{ number_format($fila->ht1, 2) }}</td>
                                <td class="fi-ta-cell fi-align-end">{{ number_format($fila->ht2, 2) }}</td>
                                <td class="fi-ta-cell fi-align-end">{{ number_format($fila->hve, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="fi-ta-row fi-ta-summary-row">
                            <td class="fi-ta-cell fi-ta-summary-row-heading-cell">Total</td>
                            <td class="fi-ta-cell fi-align-end">{{ number_format($filas->sum('horas_viaje'), 2) }}</td>
                            <td class="fi-ta-cell fi-align-end">{{ number_format($filas->sum('horas_trabajo'), 2) }}</td>
                            <td class="fi-ta-cell fi-align-end">{{ number_format($filas->sum('ht1'), 2) }}</td>
                            <td class="fi-ta-cell fi-align-end">{{ number_format($filas->sum('ht2'), 2) }}</td>
                            <td class="fi-ta-cell fi-align-end">{{ number_format($filas->sum('hve'), 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </x-filament::section>

    <x-filament::section
        heading="Desplazamientos y dietas"
        description="Totales generales del parte"
    >
        <div class="fi-ta-content-ctn fi-fixed-positioning-context">
            <div class="fi-ta-content">
                <table class="fi-ta-table">
                    <thead>
                        <tr>
                            <th class="fi-ta-header-cell">Kms</th>
                            <th class="fi-ta-header-cell">Media dieta</th>
                            <th class="fi-ta-header-cell">Dieta completa</th>
                            <th class="fi-ta-header-cell">Hotel</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="fi-ta-row">
                            <td class="fi-ta-cell">{{ $record->total_km }}</td>
                            <td class="fi-ta-cell">{{ $record->total_media_dieta }}</td>
                            <td class="fi-ta-cell">{{ $record->total_dieta }}</td>
                            <td class="fi-ta-cell">{{ $record->total_hotel }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::section>
@endif
