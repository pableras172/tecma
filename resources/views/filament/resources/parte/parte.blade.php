@php
    $cliente = $parte->cliente;
    $planta = $parte->planta;
    $tipoTrabajo = $parte->tipoTrabajo;
    $responsable = $parte->creador;
    $lineas = $parte->lineas;
@endphp

<style>
    body {
        font-family: Arial, sans-serif;
        font-size: 15px;
        margin: 0;
        padding: 0;
    }

    .header,
    .footer {
        background: #f3f3f3;
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
        margin: 20px 0;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        border: 1px solid #ccc;
        padding: 6px 8px;
        text-align: left;
    }

    .table th {
        background: #f9f9f9;
    }

    .firmas {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
    }

    .firma-box {
        width: 45%;
        text-align: center;
    }

    .firma-img {
        max-width: 100%;
        max-height: 120px;
        border: 1px solid #ccc;
        margin-bottom: 5px;
    }
</style>



<h1 style="text-align: center; font-size: 1.5em; font-weight: bold; margin-bottom:5px">Parte de trabajo #{{ $parte->numero }}</h1>
<hr>
<div>
    <div style="display: flex; justify-content: space-between;">
        <div><strong>Fecha:</strong> {{ $parte->fecha_parte?->format('d/m/Y') }}</div>
        <div><strong>Tipo de trabajo:</strong> {{ $tipoTrabajo?->nombre }}</div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
        <div><strong>Cliente:</strong> {{ $cliente?->nombre }}</div>
        <div><strong>Planta:</strong> {{ $planta?->nombre }}</div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
        <div><strong>Responsable:</strong> {{ $responsable?->name }}</div>
    </div>
    <div style="display: flex; justify-content: space-between; margin-top: 4px;">
        <div><strong>Comentarios:</strong> {{ $parte->comentarios }}</div>
    </div>
</div>


<div class="section" style="background: #f3f3f3; padding: 10px; border-radius: 6px;">
    <h3>Datos del motor</h3>
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
                <tr @if($loop->even) style="background-color: #f0f0f0;" @endif>
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

<div class="section" style="background: #f3f3f3; padding: 16px; border-radius: 6px;">
    <div><strong>Trabajo realizado: </strong>{{ $parte->trabajo_realizado }}</div>
</div>

<div class="section">
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
