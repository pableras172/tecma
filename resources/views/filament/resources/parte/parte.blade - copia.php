@php
    $cliente = $parte->cliente;
    $planta = $parte->planta;
    $tipoTrabajo = $parte->tipoTrabajo;
    $responsable = $parte->creador;
    $lineas = $parte->lineas;
@endphp

<style>
    * {
        color-scheme: light !important;
    }

    body {
        font-family: Arial, sans-serif;
        font-size: 12px;
        margin: 0;
        padding: 20px;
        background-color: white !important;
        color: black !important;
    }

    .header,
    .footer {
        background: #f3f3f3 !important;
        padding: 10px;
    }

    .header {
        border-bottom: 1px solid #ccc;
    }

    .footer {
        border-top: 1px solid #ccc;
        margin-top: 30px;
    }

    .section {
        margin: 15px 0;
        page-break-inside: avoid;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 10px;
    }

    .table th,
    .table td {
        border: 1px solid #ccc;
        padding: 4px 6px;
        text-align: left;
        color: black !important;
        background-color: white !important;
    }

    .table th {
        background: #f9f9f9 !important;
        font-weight: bold;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f0f0f0 !important;
    }

    .firmas {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        page-break-inside: avoid;
    }

    .firma-box {
        width: 45%;
        text-align: center;
    }

    .firma-img {
        max-width: 100%;
        max-height: 100px;
        border: 1px solid #ccc;
        margin-bottom: 5px;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6,
    p,
    div,
    span,
    strong {
        color: black !important;
    }

    .bg-section {
        background: #f3f3f3 !important;
        padding: 12px;
        border-radius: 6px;
    }
</style>



<h1 style="text-align: center; font-size: 1.5em; font-weight: bold; margin-bottom:10px; color: black !important;">Parte
    de trabajo #{{ $parte->numero }}</h1>
<hr style="border-color: #ccc;">

<div class="section bg-section">
    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
        <div><strong>Fecha:</strong> {{ $parte->fecha_parte?->format('d/m/Y') }}</div>
        <div><strong>Tipo de trabajo:</strong> {{ $tipoTrabajo?->nombre }}</div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
        <div><strong>Cliente:</strong> {{ $cliente?->nombre }}</div>
        <div><strong>Planta:</strong> {{ $planta?->nombre }}</div>
    </div>
    <div style="margin-bottom: 4px;">
        <div><strong>Responsable:</strong> {{ $responsable?->name }}</div>
    </div>
    @if ($parte->comentarios)
        <div style="margin-top: 8px;">
            <div><strong>Comentarios:</strong> {{ $parte->comentarios }}</div>
        </div>
    @endif
</div>


<div class="section bg-section">
    <h3 style="margin-top: 0; margin-bottom: 8px;">Datos del motor</h3>
    <div style="display: flex; gap: 40px;">
        <div><strong>Modelo:</strong> {{ $parte->modelo }}</div>
        <div><strong>Nº Motor:</strong> {{ $parte->numero_motor }}</div>
    </div>
    <div style="display: flex; gap: 40px; margin-top: 4px;">
        <div><strong>Horas motor:</strong> {{ $parte->horas_motor }}</div>
        <div><strong>Arranques:</strong> {{ $parte->arranques }}</div>
    </div>
</div>

<div class="section">
    <h3 style="margin-top: 0; margin-bottom: 8px;">Detalle de líneas de trabajo</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>H.ida</th>
                <th>H.llegada</th>
                <th>H.Ini.T.</th>
                <th>H.Fin.T.</th>
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
                    <td>{{ $linea->hora_ida }}</td>
                    <td>{{ $linea->hora_llegada }}</td>
                    <td>{{ $linea->hora_inicio_trabajo }}</td>
                    <td>{{ $linea->hora_fin_trabajo }}</td>
                    <td>{{ $linea->ht1 }}</td>
                    <td>{{ $linea->ht2 }}</td>
                    <td>{{ $linea->hve }}</td>
                    <td>{{ $linea->kms }}</td>
                    <td>{{ $linea->media_dieta ? 'Sí' : '' }}</td>
                    <td>{{ $linea->dieta_completa ? 'Sí' : '' }}</td>
                    <td>{{ $linea->hotel ? 'Sí' : '' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if ($parte->trabajo_realizado)
    <div class="section bg-section">
        <div><strong>Trabajo realizado: </strong>{{ $parte->trabajo_realizado }}</div>
    </div>
@endif

<div class="section">
    <h3 style="margin-top: 0; margin-bottom: 8px;">Resumen total</h3>
    <table class="table">
        <thead>
            <tr>
                <th>H. viaje</th>
                <th>H. trabajo</th>
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
            <tr>
                <td>{{ $parte->total_horas_viaje }}</td>
                <td>{{ $parte->total_horas_trabajo }}</td>
                <td>{{ $parte->total_ht1 }}</td>
                <td>{{ $parte->total_ht2 }}</td>
                <td>{{ $parte->total_hve }}</td>
                <td>{{ $parte->total_km }}</td>
                <td>{{ $parte->total_media_dieta }}</td>
                <td>{{ $parte->total_dieta }}</td>
                <td>{{ $parte->total_hotel }}</td>
            </tr>
        </tbody>
    </table>
</div>

<div class="firmas">
    <div class="firma-box">
        <div><strong>Firma técnico</strong></div>
        @if ($parte->firma_tecnico)
            <img class="firma-img" src="{{ asset('storage/' . $parte->firma_tecnico) }}" alt="Firma técnico">
        @else
            <div style="height: 80px; border: 1px dashed #aaa; margin-bottom: 5px;"></div>
        @endif
    </div>
    <div class="firma-box">
        <div><strong>Firma supervisor</strong></div>
        @if ($parte->firma_supervisor)
            <img class="firma-img" src="{{ asset('storage/' . $parte->firma_supervisor) }}" alt="Firma supervisor">
        @else
            <div style="height: 80px; border: 1px dashed #aaa; margin-bottom: 5px;"></div>
        @endif
    </div>
</div>
