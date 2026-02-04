<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.7;
            color: #2d3748;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 40px 20px;
        }
        .email-wrapper {
            max-width: 650px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 15px;
            text-align: center;
        }
        .logo-container {
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            max-height: 60px;
            object-fit: contain;
        }
        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .header .subtitle {
            font-size: 16px;
            opacity: 0.95;
            font-weight: 300;
        }
        .parte-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 20px;
            border-radius: 20px;
            margin-top: 15px;
            font-size: 14px;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #1a202c;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .intro-text {
            color: #4a5568;
            margin-bottom: 30px;
            font-size: 15px;
        }
        .section-title {
            font-size: 16px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        .documentos-box {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            border-left: 4px solid #667eea;
        }
        .documentos-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            margin-top: 15px;
        }
        .documento-row {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .documento-row td {
            padding: 15px;
            border: none;
        }
        .doc-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .doc-info {
            flex: 1;
        }
        .doc-name {
            font-weight: 600;
            color: #2d3748;
            font-size: 15px;
            margin-bottom: 4px;
        }
        .doc-date {
            font-size: 13px;
            color: #718096;
        }
        .info-box {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #a0aec0;
            font-weight: 600;
        }
        .info-value {
            font-size: 15px;
            color: #2d3748;
            font-weight: 500;
        }
        .closing-text {
            color: #4a5568;
            font-size: 15px;
            margin-top: 25px;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
            border-left: 3px solid #667eea;
        }
        .footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            font-size: 13px;
            color: #718096;
            margin-bottom: 8px;
        }
        .footer-brand {
            font-weight: 600;
            color: #667eea;
            margin-top: 10px;
        }
        @media only screen and (max-width: 600px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            .header h1 {
                font-size: 24px;
            }
            .content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            @if(setting('site_logo'))
            <div class="logo-container">
                <img src="{{ asset('storage/' . setting('site_logo')) }}" alt="Logo" class="logo">
            </div>
            @endif
            <h1>📋 Documentos del Parte</h1>
            <p class="subtitle">Documentación Técnica</p>
            <div class="parte-badge">Parte #{{ $parte->numero }}</div>
        </div>
        
        <div class="content">
            <p class="greeting">Estimado/a {{ $cliente->nombre }},</p>
            
            <p class="intro-text">
                Le adjuntamos la documentación técnica correspondiente al parte de trabajo. 
                A continuación encontrará el detalle de los archivos incluidos en este envío.
            </p>
            
            <div class="documentos-box">
                <div class="section-title">Documentos Adjuntos</div>
                <table class="documentos-table">
                    @foreach($documentos as $index => $documento)
                    <tr class="documento-row">
                        <td style="width: 50px;">
                            <div class="doc-icon">{{ $index + 1 }}</div>
                        </td>
                        <td>
                            <div class="doc-info">
                                <div class="doc-name">{{ $documento->nombre_documento }}</div>
                                <div class="doc-date">📅 {{ $documento->fecha->format('d/m/Y') }}</div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
            
            <div class="info-box">
                <div class="section-title">Información del Parte</div>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Número de Parte</span>
                        <span class="info-value">#{{ $parte->numero }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Fecha</span>
                        <span class="info-value">{{ $parte->fecha_parte?->format('d/m/Y') ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tipo de Trabajo</span>
                        <span class="info-value">{{ $parte->tipoTrabajo?->nombre ?? 'N/A' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Planta</span>
                        <span class="info-value">{{ $parte->planta?->nombre ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            
            <div class="closing-text">
                💬 Si tiene alguna duda o necesita información adicional sobre estos documentos, 
                no dude en ponerse en contacto con nuestro equipo técnico.
            </div>
        </div>
        
        <div class="footer">
            <p>Este es un correo automático generado por el sistema.</p>
            <p>Por favor, no responda directamente a este mensaje.</p>
            <p class="footer-brand">Sistema de Gestión de Partes de Trabajo</p>
        </div>
    </div>
</body>
</html>
