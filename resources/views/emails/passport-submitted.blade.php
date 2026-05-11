@extends('layouts.email', ['title' => 'Trámite en Curso - Iris Aerospace'])

@section('content')
    <div class="title">Trámite en Curso</div>
    <div class="subtitle">Validación de Pasaporte Estelar IRIS</div>

    <p style="color: #888888; line-height: 1.6;">
        Estimado cliente, le informamos que el proceso de tramitación del **Pasaporte Estelar** para **{{ $passengerName }}** ha sido iniciado formalmente ante la Agencia Espacial.
    </p>

    <div class="box" style="border-left-color: #fbbf24;">
        <div class="field-label">Estado Actual</div>
        <div class="field-value" style="color: #fbbf24;">EN REVISIÓN / VALIDACIÓN</div>
        
        <p style="color: #888888; font-size: 11px; margin-top: 10px;">
            Actualmente, los datos biométricos y la documentación proporcionada están siendo analizados por las autoridades. Este proceso suele demorar entre 24 y 48 horas.
        </p>
    </div>

    <p style="color: #888888; line-height: 1.6;">
        Recibirá una nueva notificación en cuanto el documento sea emitido y validado para el vuelo. No es necesaria ninguna acción adicional por su parte en este momento.
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Seguir mi Trámite</a>
    </div>
@endsection
