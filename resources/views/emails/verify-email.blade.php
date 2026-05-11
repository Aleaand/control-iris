@extends('layouts.email', ['title' => 'Verificación de Identidad - Iris Aerospace'])

@section('content')
    <div class="title">Confirmación de Acceso</div>
    <div class="subtitle">Protocolo de Registro - Iris Aerospace</div>

    <p style="color: #888888; line-height: 1.6;">
        ¡Gracias por unirte a Iris Aerospace! Para completar tu registro y activar tu acceso a la terminal de control, necesitamos verificar tu identidad digital.
    </p>

    <div class="box" style="border-left-color: #10b981;">
        <p style="color: #ffffff; font-size: 13px; margin: 0;">
            <strong>Siguiente Paso:</strong> Al hacer clic en el botón inferior, confirmarás tu identidad y serás redirigido automáticamente a tu centro de control privado.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ $url }}" class="cta-button" style="background-color: #10b981; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);">Verificar Cuenta</a>
    </div>

    <p style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        Si no has creado esta cuenta, puedes ignorar este mensaje. Si tienes problemas con el botón, copia y pega este enlace:<br>
        <span style="color: #10b981; word-break: break-all;">{{ $url }}</span>
    </p>
@endsection