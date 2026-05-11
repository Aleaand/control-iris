@extends('layouts.email', ['title' => 'Bienvenido a Iris Aerospace'])

@section('content')
    <div class="title">Confirmación de Registro</div>
    <div class="subtitle">Acceso al Sistema Central</div>

    <p style="color: #888888; line-height: 1.6;">
        Bienvenido a Iris Aerospace. Tu cuenta ha sido dada de alta en el sistema central de operaciones. A continuación se detallan tus credenciales de acceso provisionales para la terminal de control.
    </p>

    <div class="box">
        <div class="field-label">Identificación de Usuario</div>
        <div class="field-value">{{ $user->name }}</div>

        <div class="field-label">Correo Electrónico</div>
        <div class="field-value">{{ $user->email }}</div>

        <div class="field-label">Contraseña Temporal</div>
        <div class="field-value" style="color: #0ea5e9; font-size: 24px; font-family: 'JetBrains Mono', monospace; letter-spacing: 0.1em;">{{ $password }}</div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/login" class="cta-button">Iniciar Sesión</a>
    </div>

    <p style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        <strong>Nota de Seguridad:</strong> Esta contraseña es temporal. El sistema te pedirá que actualices la contraseña una vez inicies tu primera sesión para garantizar la integridad de tu expediente estelar.
    </p>
@endsection