@extends('layouts.email', ['title' => 'Bienvenido al Sistema - Iris Aerospace'])

@section('content')
    <div class="title">Bienvenido/a, {{ $user->name }}</div>
    <div class="subtitle">Tu cuenta de Gestor ha sido creada</div>

    <p style="color: #888888; line-height: 1.6;">
        El administrador del sistema ha creado una cuenta de Gestor para ti en **Iris Aerospace Mission Control**. Para completar tu incorporación, necesitas establecer tu contraseña de acceso.
    </p>

    <div class="box">
        <p style="color: #ffffff; font-size: 13px; margin: 0;">
            <strong>Siguiente Paso:</strong> Haz clic en el botón inferior para configurar tu contraseña personal. El enlace es válido durante <strong>60 minutos</strong> por protocolos de seguridad.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $url }}" class="cta-button">Configurar mi Contraseña</a>
    </div>

    <p style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        Si tienes problemas con el botón, copia y pega el siguiente enlace en tu navegador:<br>
        <span style="color: #0ea5e9; word-break: break-all;">{{ $url }}</span>
    </p>
@endsection
