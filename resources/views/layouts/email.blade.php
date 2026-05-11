<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Iris Aerospace' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&family=JetBrains+Mono&display=swap');

        body {
            font-family: 'Inter', Helvetica, Arial, sans-serif;
            background-color: #0a0a0f;
            color: #ffffff;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }

        .email-wrapper {
            width: 100%;
            background-color: #0a0a0f;
            padding: 40px 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #14141f;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
        }

        .header {
            background-color: #1c1c28;
            padding: 40px;
            text-align: center;
            border-bottom: 1px solid rgba(14, 165, 233, 0.2);
        }

        .logo-img {
            height: 60px;
            margin-bottom: 15px;
        }

        .system-status {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: #10b981;
            text-transform: uppercase;
            letter-spacing: 0.3em;
        }

        .content {
            padding: 40px;
        }

        .title {
            font-size: 24px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.02em;
            margin-bottom: 10px;
            color: #ffffff;
        }

        .subtitle {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: #0ea5e9;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 30px;
        }

        .box {
            background-color: rgba(255, 255, 255, 0.03);
            border-left: 3px solid #0ea5e9;
            padding: 25px;
            border-radius: 4px 12px 12px 4px;
            margin: 30px 0;
        }

        .field-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 5px;
        }

        .field-value {
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 20px;
        }

        .cta-button {
            display: inline-block;
            background-color: #0ea5e9;
            color: #000000 !important;
            text-decoration: none;
            padding: 18px 35px;
            border-radius: 12px;
            font-weight: 900;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(14, 165, 233, 0.2);
        }

        .footer {
            padding: 30px;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            background-color: #0d0d16;
        }

        .footer p {
            margin: 5px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }
        
        .status-emerald { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .status-amber { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-rose { background: rgba(244, 63, 94, 0.15); color: #f43f5e; border: 1px solid rgba(244, 63, 94, 0.3); }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="container">
            <div class="header">
                @php
                    $logoPath = public_path('assets/logo_iris.png');
                    $logoUrl = file_exists($logoPath) ? $message->embed($logoPath) : config('app.url') . '/assets/logo_iris.png';
                @endphp
                <img src="{{ $logoUrl }}" alt="Iris Aerospace" class="logo-img">
                <div class="system-status">Sistema de Control Operativo</div>
            </div>
            <div class="content" style="padding: 40px 50px;">
                <div style="line-height: 1.6; margin-bottom: 20px;">
                    @yield('content')
                </div>
            </div>
            <div class="footer">
                <p>&copy; {{ date('Y') }} IRIS AEROSPACE &middot; LOGISTICA DE VUELOS ESPACIALES</p>
                <p style="color: #2a2f3b; margin-top: 15px;">Este mensaje es confidencial y generado automáticamente por la terminal IRIS.</p>
            </div>
        </div>
    </div>
</body>
</html>
