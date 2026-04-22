<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #050505;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #0a0a0a;
            border: 1px solid #1a1a1a;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 40px;
        }

        .header {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            padding: 40px;
            text-align: center;
            border-bottom: 2px solid #a855f7;
        }

        .logo {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: 0.2em;
            color: #ffffff;
            text-transform: uppercase;
        }

        .content {
            padding: 40px;
        }

        .welcome-text {
            font-size: 20px;
            color: #a855f7;
            font-weight: 700;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .credentials-box {
            background-color: #000000;
            border: 1px solid #333333;
            padding: 24px;
            border-radius: 8px;
            margin: 30px 0;
        }

        .label {
            color: #666666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .value {
            color: #ffffff;
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 16px;
            margin-bottom: 16px;
        }

        .footer {
            background-color: #000000;
            padding: 24px;
            text-align: center;
            font-size: 10px;
            color: #444444;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .button {
            display: inline-block;
            background-color: #ffffff;
            color: #000000;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Iris Aerospace</div>
        </div>
        <div class="content">
            <div class="welcome-text">Confirmación de Registro</div>
            <p style="color: #888888; line-height: 1.6;">Bienvenido a Iris Aerospace. Tu cuenta ha sido dada de alta en
                el sistema central de operaciones. A continuación se detallan tus credenciales de acceso provisionales:
            </p>

            <div class="credentials-box">
                <div class="label">Tripulante / Cliente</div>
                <div class="value">{{ $user->name }}</div>

                <div class="label">Email de Acceso</div>
                <div class="value">{{ $user->email }}</div>

                <div class="label">Contraseña de Seguridad</div>
                <div class="value" style="color: #a855f7; font-weight: 800; font-size: 20px;">{{ $password }}</div>
            </div>

            <p style="color: #555555; font-size: 11px; font-style: italic;">Por favor, por motivos de seguridad, te
                recomendamos cambiar tu contraseña tras tu primer inicio de sesión.</p>

            <center>
                <a href="{{ config('app.url') }}/login" class="button">Acceder al Sistema</a>
            </center>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} IRIS AEROSPACE &middot; ADVANCED LOGISTICS DIVISION<br>
            ESTE ES UN MENSAJE AUTOMATIZADO. NO RESPONDER.
        </div>
    </div>
</body>

</html>