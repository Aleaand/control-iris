<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iris Aerospace - Bienvenido al Sistema</title>
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
            color: #0ea5e9;
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

        .info-box {
            background-color: rgba(14, 165, 233, 0.05);
            border-left: 3px solid #0ea5e9;
            padding: 25px;
            border-radius: 4px 12px 12px 4px;
            margin: 30px 0;
        }

        .info-text {
            color: #94a3b8;
            font-size: 14px;
            line-height: 1.6;
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
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="container">
            <div class="header">
                <img src="{{ $message->embed(public_path('assets/logo_iris.png')) }}" alt="Iris Aerospace" class="logo-img">
                <div class="system-status">● ACTIVACIÓN DE CUENTA — IRIS AEROSPACE</div>
            </div>

            <div class="content">
                <h1 class="title">Bienvenido/a, {{ $user->name }}</h1>
                <div class="subtitle">Tu cuenta de Gestor ha sido creada</div>

                <p class="info-text">
                    El administrador del sistema ha creado una cuenta de Gestor para ti en <strong>Iris Aerospace Mission Control</strong>. Para completar tu incorporación, necesitas establecer tu contraseña de acceso.
                </p>

                <div class="info-box">
                    <p class="info-text" style="margin: 0; font-size: 12px;">
                        <strong>Siguiente Paso:</strong> Haz clic en el botón inferior para configurar tu contraseña personal. El enlace es válido durante <strong>60 minutos</strong>. Si expira, contacta con el administrador para que te envíe uno nuevo.
                    </p>
                </div>

                <div style="text-align: center;">
                    <a href="{{ $url }}" class="cta-button">Configurar mi Contraseña</a>
                </div>

                <p style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                    Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:<br>
                    <span style="color: #0ea5e9; word-break: break-all;">{{ $url }}</span>
                </p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} IRIS AEROSPACE &middot; INCORPORACIÓN DE PERSONAL</p>
            </div>
        </div>
    </div>
</body>

</html>
