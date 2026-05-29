@php
    $cliente = $parte->cliente;
    $planta = $parte->planta;
    $tipoTrabajo = $parte->tipoTrabajo;
    $responsable = $parte->creador;
    $lineas = $parte->lineas;
    $resumenPorCategoria = $parte->obtenerResumenPorCategoria();
    $totalHorasViajeResumen = round($resumenPorCategoria->sum('horas_viaje'), 2);
    $totalHorasTrabajoResumen = round($resumenPorCategoria->sum('horas_trabajo'), 2);
    $totalHt1Resumen = round($resumenPorCategoria->sum('ht1'), 2);
    $totalHt2Resumen = round($resumenPorCategoria->sum('ht2'), 2);
    $totalHveResumen = round($resumenPorCategoria->sum('hve'), 2);
@endphp

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 11px;
        margin: 10px;
        padding: 10px;
        background-color: white;
        color: black;
    }

    h1 {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 10px;
        margin-top: 15px;
        color: black;
    }

    h3 {
        font-size: 13px;
        margin: 5px 0;
        color: black;
    }

    .bg-section {
        background-color: #f3f3f3;
        padding: 10px;
        margin-bottom: 10px;
    }

    .info-table {
        width: 100%;
        margin-bottom: 5px;
        border-collapse: collapse;
    }

    .info-table td {
        padding: 3px 5px;
        vertical-align: top;
    }

    .info-table td strong {
        color: black;
    }

    table.table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table.table th,
    table.table td {
        border: 1px solid #ccc;
        padding: 4px 6px;
        text-align: left;
        font-size: 9px;
    }

    table.table th {
        background-color: #bbbbbb;
        font-weight: bold;
        color: black;
    }

    table.table tbody tr:nth-child(4n+1),
    table.table tbody tr:nth-child(4n+2) {
        background-color: #f0f0f0;
    }

    .firmas-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .firmas-table td {
        width: 50%;
        text-align: center;
        vertical-align: top;
        padding: 10px;
    }

    .firma-img {
        max-width: 200px;
        max-height: 100px;
        border: 1px solid #ccc;
    }

    .firma-placeholder {
        height: 80px;
        border: 1px dashed #aaa;
        margin: 0 auto;
        width: 200px;
    }

    hr {
        border: 0;
        border-top: 1px solid #ccc;
        margin: 10px 0;
    }
</style>



<h1>Parte de trabajo #{{ $parte->numero }}</h1>
<hr>

<div class="bg-section">
    <table class="info-table">
        <tr>
            <td width="50%"><strong>Fecha:</strong> {{ $parte->fecha_parte?->format('d/m/Y') }}</td>
            <td width="50%"><strong>Tipo de trabajo:</strong> {{ $tipoTrabajo?->nombre }}</td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong> {{ $cliente?->nombre }}</td>
            <td><strong>Planta:</strong> {{ $planta?->nombre }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Responsable:</strong> {{ $responsable?->name }}</td>
        </tr>
        @if($parte->comentarios)
        <tr>
            <td colspan="2"><strong>Comentarios:</strong> {{ $parte->comentarios }}</td>
        </tr>
        @endif
    </table>
</div>

<div class="bg-section">
    <h3>Datos del motor</h3>
    <table class="info-table">
        <tr>
            <td width="25%"><strong>Modelo:</strong> {{ $parte->modelo }}</td>
            <td width="25%"><strong>Nº Motor:</strong> {{ $parte->numero_motor }}</td>
            <td width="25%"><strong>Horas motor:</strong> {{ $parte->horas_motor }}</td>
            <td width="25%"><strong>Arranques:</strong> {{ $parte->arranques }}</td>
        </tr>
    </table>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>H.ida</th>
            <th>H.llegada</th>
            <th>H.Ini.T.</th>
            <th>H.Fin.T.</th>
            <th>H.Ini.T2.</th>
            <th>H.Fin.T2.</th>
            <th>H.Vuelta</th>
            <th>H.V.Llega</th>
            <th>H.E.</th>
            <th>H.S.</th>
            <th>HT1</th>
            <th>HT2</th>
            <th>HVE</th>
            <th>Kms</th>
            <th>M/D</th>
            <th>D/C</th>
            <th>Hotel</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($lineas as $linea)
            <tr>
                <td>{{ $linea->fecha?->format('d/m/Y') }}</td>
                <td>{{ $linea->hora_ida?->format('H:i') }}</td>
                <td>{{ $linea->hora_llegada?->format('H:i') }}</td>
                <td>{{ $linea->hora_inicio_trabajo?->format('H:i') }}</td>
                <td>{{ $linea->hora_fin_trabajo?->format('H:i') }}</td>
                <td>{{ $linea->hora_inicio_trabajo_2?->format('H:i') }}</td>
                <td>{{ $linea->hora_fin_trabajo_2?->format('H:i') }}</td>
                <td>{{ $linea->hora_vuelta?->format('H:i') }}</td>
                <td>{{ $linea->hora_vuelta_llegada?->format('H:i') }}</td>
                <td>{{ $linea->hora_entrada?->format('H:i') }}</td>
                <td>{{ $linea->hora_salida?->format('H:i') }}</td>
                <td>{{ $linea->ht1 }}</td>
                <td>{{ $linea->ht2 }}</td>
                <td>{{ $linea->hve }}</td>
                <td>{{ $linea->kms }}</td>
                <td>{{ $linea->media_dieta ? 'Sí' : '' }}</td>
                <td>{{ $linea->dieta_completa ? 'Sí' : '' }}</td>
                <td>{{ $linea->hotel ? 'Sí' : '' }}</td>
            </tr>
            <tr>
                <td colspan="18" style="font-size: 9px; padding: 3px 6px;"><strong>Trabajo realizado por:</strong>
                    {{ $linea->usuarios->map(fn($usuario) => $usuario->name . ' (' . ($usuario->categoriaProfesional?->nombre ?? 'Sin categoría') . ')')->join(', ') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@if($parte->trabajo_realizado)
<div class="bg-section">
    <strong>Trabajo realizado: </strong>{{ $parte->trabajo_realizado }}
</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>Categoría</th>
            <th>H. viaje</th>
            <th>H. trabajo</th>
            <th>HT1</th>
            <th>HT2</th>
            <th>HVE</th>
        </tr>
    </thead>
    <tbody>
        @forelse($resumenPorCategoria as $resumen)
            <tr>
                <td>{{ $resumen['categoria'] }}</td>
                <td>{{ $resumen['horas_viaje'] }}</td>
                <td>{{ $resumen['horas_trabajo'] }}</td>
                <td>{{ $resumen['ht1'] }}</td>
                <td>{{ $resumen['ht2'] }}</td>
                <td>{{ $resumen['hve'] }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">Sin líneas de resumen por categoría.</td>
            </tr>
        @endforelse
        <tr>
            <td><strong>Total</strong></td>
            <td><strong>{{ $totalHorasViajeResumen }}</strong></td>
            <td><strong>{{ $totalHorasTrabajoResumen }}</strong></td>
            <td><strong>{{ $totalHt1Resumen }}</strong></td>
            <td><strong>{{ $totalHt2Resumen }}</strong></td>
            <td><strong>{{ $totalHveResumen }}</strong></td>
        </tr>
    </tbody>
</table>

<table class="table">
    <thead>
        <tr>
            <th>Kms</th>
            <th>M/D</th>
            <th>D/C</th>
            <th>Hotel</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $parte->total_km }}</td>
            <td>{{ $parte->total_media_dieta }}</td>
            <td>{{ $parte->total_dieta }}</td>
            <td>{{ $parte->total_hotel }}</td>
        </tr>
    </tbody>
</table>

<table class="firmas-table">
    <tr>
        <td>
            <strong>Firma técnico</strong><br><br>
            @if ($parte->firma_tecnico)
                @php
                    $firmaPath = storage_path('app/public/' . $parte->firma_tecnico);
                    $firmaBase64 = file_exists($firmaPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($firmaPath)) : '';
                @endphp
                @if($firmaBase64)
                    <img class="firma-img" src="{{ $firmaBase64 }}" alt="Firma técnico">
                @else
                    <div class="firma-placeholder"></div>
                @endif
            @else
                <div class="firma-placeholder"></div>
            @endif
        </td>
        <td>
            <strong>Firma supervisor</strong><br><br>
            @if ($parte->firma_supervisor)
                @php
                    $firmaPath = storage_path('app/public/' . $parte->firma_supervisor);
                    $firmaBase64 = file_exists($firmaPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($firmaPath)) : '';
                @endphp
                @if($firmaBase64)
                    <img class="firma-img" src="{{ $firmaBase64 }}" alt="Firma supervisor">
                @else
                    <div class="firma-placeholder"></div>
                @endif
            @else
                <div class="firma-placeholder"></div>
            @endif
        </td>
    </tr>
</table>
