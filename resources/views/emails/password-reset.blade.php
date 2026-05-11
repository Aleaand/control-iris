@extends('layouts.email', ['title' => 'Restablecimiento de Contraseña - Iris Aerospace'])

@section('content')
    <div class="title">Restablecimiento de Credenciales</div>
    <div class="subtitle">Control de Acceso Privado</div>

    <p style="color: #888888; line-height: 1.6;">
        Has recibido este mensaje porque se ha solicitado un restablecimiento de contraseña para tu cuenta vinculada a este protocolo de comunicación de Iris Aerospace.
    </p>

    <div class="box" style="border-left-color: #f59e0b;">
        <p style="color: #ffffff; font-size: 13px; margin: 0;">
            <strong>Aviso de Seguridad:</strong> Este enlace de recuperación expirará en 60 minutos. Si no has solicitado este cambio, ignora este mensaje o contacta con soporte técnico.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $url }}" class="cta-button" style="background-color: #f59e0b; box-shadow: 0 10px 20px rgba(245, 158, 11, 0.2);">Restablecer Contraseña</a>
    </div>

    <p style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        Si tienes problemas para restablecer tu contraseña, copia y pega el siguiente enlace en tu navegador:<br>
        <span style="color: #0ea5e9; word-break: break-all;">{{ $url }}</span>
    </p>
@endsection