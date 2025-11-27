<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago #{{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #2563eb;
        }
        
        .header h1 {
            font-size: 28px;
            color: #2563eb;
            margin-bottom: 10px;
        }
        
        .header .info {
            font-size: 11px;
            color: #666;
            margin: 5px 0;
        }
        
        .header h2 {
            font-size: 20px;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        
        .header .recibo-num {
            font-size: 16px;
            color: #666;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 2px solid #e5e7eb;
        }
        
        .grid {
            display: table;
            width: 100%;
        }
        
        .grid-row {
            display: table-row;
        }
        
        .grid-col {
            display: table-cell;
            padding: 8px 10px;
            width: 50%;
        }
        
        .label {
            font-size: 10px;
            color: #666;
            display: block;
            margin-bottom: 3px;
        }
        
        .value {
            font-weight: bold;
        }
        
        .detail-row {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            color: #666;
        }
        
        .detail-value {
            font-weight: bold;
            text-align: right;
        }
        
        .total-box {
            background-color: #eff6ff;
            border: 2px solid #2563eb;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }
        
        .total-label {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .total-amount {
            font-size: 32px;
            font-weight: bold;
            color: #2563eb;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px;
            color: #666;
        }
        
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $informacion->nombre ?? 'Elevation Gym' }}</h1>
        <div class="info">{{ $informacion->direccion }}</div>
        <div class="info">
            Tel: {{ $informacion->telefono }} | Email: {{ $informacion->correo }}
        </div>
        <h2>COMPROBANTE DE PAGO</h2>
        <div class="recibo-num">N° {{ str_pad($pago->id, 6, '0', STR_PAD_LEFT) }}</div>
    </div>

    <div class="section">
        <div class="section-title">Información del Cliente</div>
        <div class="grid">
            <div class="grid-row">
                <div class="grid-col">
                    <span class="label">Nombre</span>
                    <div class="value">{{ $pago->usuario->name }}</div>
                </div>
                <div class="grid-col">
                    <span class="label">Email</span>
                    <div class="value">{{ $pago->usuario->email }}</div>
                </div>
            </div>
            <div class="grid-row">
                <div class="grid-col">
                    <span class="label">Teléfono</span>
                    <div class="value">{{ $pago->usuario->telefono ?? 'No especificado' }}</div>
                </div>
                <div class="grid-col">
                    <span class="label">CI</span>
                    <div class="value">{{ $pago->usuario->ci ?? 'No especificado' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Detalles del Pago</div>
        <div class="detail-row">
            <span class="detail-label">Concepto</span>
            <span class="detail-value">{{ $pago->motivo }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Método de Pago</span>
            <span class="detail-value">{{ ucfirst($pago->metodo) }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Pago</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($pago->fecha)->translatedFormat('d \d\e F \d\e Y') }}</span>
        </div>
        <div class="detail-row">
            <span class="detail-label">Fecha de Vencimiento</span>
            <span class="detail-value">{{ \Carbon\Carbon::parse($pago->vence)->translatedFormat('d \d\e F \d\e Y') }}</span>
        </div>
    </div>

    <div class="total-box">
        <div class="total-label">TOTAL PAGADO</div>
        <div class="total-amount">Bs {{ number_format($pago->monto, 2) }}</div>
    </div>

    <div class="footer">
        
        <p>Generado el {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y \a \l\a\s H:i') }}</p>
    </div>
</body>
</html>
