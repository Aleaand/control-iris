<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iris Aerospace - Notificación de Misión</title>
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
            border-bottom: 1px solid {{ $actionType === 'created' ? '#a855f7' : '#f59e0b' }};
        }

        .logo-img {
            height: 50px;
            margin-bottom: 15px;
        }

        .system-status {
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            color: {{ $actionType === 'created' ? '#a855f7' : '#f59e0b' }};
            text-transform: uppercase;
            letter-spacing: 0.3em;
        }

        .content {
            padding: 40px;
        }

        .title {
            font-size: 22px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.02em;
            margin-bottom: 5px;
            color: #ffffff;
        }

        .subtitle {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 30px;
        }

        .info-box {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            margin: 20px 0;
        }

        .task-detail {
            margin-bottom: 15px;
        }

        .detail-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: block;
            margin-bottom: 4px;
        }

        .detail-value {
            font-size: 14px;
            color: #e2e8f0;
            font-weight: 700;
        }

        .priority-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            @php
                $colors = [
                    'baja' => ['bg' => 'rgba(14, 165, 233, 0.1)', 'text' => '#0ea5e9'],
                    'media' => ['bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#f59e0b'],
                    'alta' => ['bg' => 'rgba(249, 115, 22, 0.1)', 'text' => '#f97316'],
                    'urgente' => ['bg' => 'rgba(244, 63, 94, 0.1)', 'text' => '#f43f5e'],
                ];
                $c = $colors[$task->priority] ?? $colors['media'];
            @endphp
            background-color: {{ $c['bg'] }};
            color: {{ $c['text'] }};
        }

        .cta-button {
            display: inline-block;
            background-color: #a855f7;
            color: #ffffff !important;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 10px;
            font-weight: 900;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-top: 20px;
            box-shadow: 0 10px 20px rgba(168, 85, 247, 0.2);
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
                <div class="system-status">● {{ $actionType === 'created' ? 'NUEVA ASIGNACIÓN' : 'ACTUALIZACIÓN DE MISIÓN' }} — IRIS AEROSPACE</div>
            </div>

            <div class="content">
                <h1 class="title">Hola, {{ $user->name }}</h1>
                <div class="subtitle">Control de Misiones Operativas</div>

                <p style="color: #94a3b8; font-size: 14px; line-height: 1.6; margin-bottom: 25px;">
                    El administrador te ha {{ $actionType === 'created' ? 'asignado una nueva' : 'modificado una' }} tarea con prioridad <span style="font-weight: 900; color: #ffffff;">{{ strtoupper($task->priority) }}</span>.
                </p>

                <div class="info-box">
                    <div class="task-detail">
                        <span class="detail-label">Título de la Misión</span>
                        <span class="detail-value">{{ $task->title }}</span>
                    </div>

                    <div class="task-detail">
                        <span class="detail-label">Prioridad Actual</span>
                        <span class="priority-badge">{{ $task->priority }}</span>
                    </div>

                    <div class="task-detail" style="margin-bottom: 0;">
                        <span class="detail-label">Tipo de Tarea</span>
                        <span class="detail-value" style="font-size: 12px;">{{ strtoupper($task->type) }}</span>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 35px;">
                    <a href="{{ url('/gestor/tasks') }}" class="cta-button">Revisar mi Control de Tareas</a>
                </div>

                <p style="color: #475569; font-size: 10px; margin-top: 40px; text-align: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
                    Este es un correo automático del sistema Iris Aerospace Mission Control.<br>
                    Por favor, no respondas a este mensaje.
                </p>
            </div>

            <div class="footer">
                <p>&copy; {{ date('Y') }} IRIS AEROSPACE &middot; DIVISIÓN DE OPERACIONES</p>
            </div>
        </div>
    </div>
</body>

</html>
