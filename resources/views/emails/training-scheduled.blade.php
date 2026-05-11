@extends('layouts.email', ['title' => 'Sesiones Programadas - IRIS Training'])

@section('content')
    <div class="title">Recordatorio de Entrenamiento</div>
    <div class="subtitle">Programa IRIS Training - Vuelo Espacial</div>

    <p style="color: #888888; line-height: 1.6;">
        Estimado/a **{{ $passengerName }}**, le confirmamos que su plan de formación técnica ha sido programado con éxito.
    </p>

    <div class="box">
        <div class="field-label">Tipo de Entrenamiento</div>
        <div class="field-value">{{ $isRenewal ? 'Renovación de Certificado (1 hora)' : 'Entrenamiento Inicial (3 horas totales)' }}</div>
        
        <div class="field-label">Próximas Sesiones</div>
        @foreach($sessions as $session)
            <div style="background: rgba(255,255,255,0.05); padding: 12px; border-radius: 8px; margin-bottom: 10px; border: 1px solid rgba(255,255,255,0.05);">
                <p style="color: #ffffff; font-size: 13px; margin: 0;">
                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($session['date'])->format('d/m/Y \a \l\a\s H:i') }}<br>
                    <strong>Duración:</strong> {{ $session['hours'] }} {{ $session['hours'] > 1 ? 'horas' : 'hora' }}
                </p>
            </div>
        @endforeach
    </div>

    @if($hasTransfer)
        <div style="background: rgba(16, 185, 129, 0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(16, 185, 129, 0.2); margin-bottom: 25px;">
            <p style="color: #10b981; font-weight: bold; font-size: 11px; text-transform: uppercase; margin-top: 0;">🚌 Traslado Incluido</p>
            <p style="color: #888888; font-size: 11px; margin-bottom: 0;">
                Se ha coordinado automáticamente su traslado al Centro de Entrenamiento. Su gestor le confirmará los detalles de la recogida.
            </p>
        </div>
    @endif

    <div class="box" style="border-left-color: #94a3b8;">
        <div class="field-label">Requisitos para la Sesión</div>
        <ul style="color: #888888; font-size: 12px; margin: 0; padding-left: 20px;">
            <li>Documento de identidad original (DNI/Pasaporte)</li>
            <li>Ropa cómoda para simulaciones físicas</li>
            <li>Presentarse 15 minutos antes de la hora</li>
        </ul>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Ver mi Expediente</a>
    </div>
@endsection
