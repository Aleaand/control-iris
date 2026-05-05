<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iris Aerospace - Bienvenida</title>
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

        .dossier-box {
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

        .field-value.password {
            color: #0ea5e9;
            font-size: 24px;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.1em;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('logo_iris.png') }}" alt="Iris Aerospace" class="logo-img">
            <div class="title">Iris Aerospace</div>
        </div>
        <div class="content">
            <div class="welcome-text">Confirmación de Registro</div>
            <p style="color: #888888; line-height: 1.6;">Bienvenido a Iris Aerospace. Tu cuenta ha sido dada de alta en
                el sistema central de operaciones. A continuación se detallan tus credenciales de acceso provisionales:
            </p>

            <div class="dossier-box">
                <div class="field-label">Identificación de Usuario</div>
                <div class="field-value">{{ $user->name }}</div>

                <div class="field-label">Correo Electrónico</div>
                <div class="field-value">{{ $user->email }}</div>

                <div class="field-label">Contraseña Temporal</div>
                <div class="field-value password">{{ $password }}</div>
            </div>

            <div style="text-align: center;">
                <a href="{{ config('app.url') }}/login" class="cta-button">Iniciar Sesión</a>
            </div>

            <p
                style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                <strong>Nota de Seguridad:</strong> Esta contraseña es temporal. El sistema te pedirá que actualices la
                contraseña una vez inicies tu primera sesión.
            </p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} IRIS AEROSPACE &middot; LOGISTICA DE VUELOS ESPACIALES</p>
            <p style="color: #2a2f3b; margin-top: 15px;">Este mensaje es confidencial y generado automáticamente. No
                responder a este correo.</p>
        </div>
    </div>
</body>

</html>