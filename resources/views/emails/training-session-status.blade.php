@extends('layouts.email', ['title' => 'Actualización: Sesión de IRIS Training'])

@section('content')
    <div class="title">Actualización de Sesión</div>
    <div class="subtitle">Programa IRIS Training</div>

    <p style="color: #888888; line-height: 1.6;">
        Estimado/a **{{ $passengerName }}**, le informamos de una actualización en el estado de su sesión de entrenamiento técnico.
    </p>

    <div class="box" style="border-left-color: {{ $newStatus === 'Ausente' ? '#f43f5e' : '#0ea5e9' }};">
        <div class="field-label">Sesión</div>
        <div class="field-value">{{ \Carbon\Carbon::parse($sessionDate)->format('d/m/Y \a \l\a\s H:i') }}</div>

        <div class="field-label">Nuevo Estado</div>
        <div class="field-value">
            <span class="status-badge {{ $newStatus === 'Ausente' ? 'status-rose' : 'status-amber' }}">
                {{ $newStatus === 'Ausente' ? 'Cancelada / Ausencia' : ($newStatus === 'Programada' ? 'Reprogramada / Activa' : $newStatus) }}
            </span>
        </div>
    </div>

    @if($newStatus === 'Ausente')
        <p style="color: #888888; line-height: 1.6;">
            La sesión ha sido marcada como cancelada o se ha registrado su ausencia. Por favor, póngase en contacto con su gestor para reprogramar las horas de entrenamiento necesarias para habilitar su vuelo.
        </p>
    @else
        <p style="color: #888888; line-height: 1.6;">
            La sesión se encuentra ahora en estado **{{ $newStatus }}**. Por favor, revise los detalles con su gestor si tiene alguna duda sobre su formación.
        </p>
    @endif

    @if($reason)
        <div class="field-label" style="margin-top: 20px;">Nota del gestor</div>
        <p style="color: #ffffff; font-size: 13px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px;">
            {{ $reason }}
        </p>
    @endif

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Ver mi Calendario</a>
    </div>
@endsection
