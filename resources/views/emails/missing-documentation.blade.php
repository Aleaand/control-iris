@extends('layouts.email', ['title' => 'Documentación Faltante - Iris Aerospace'])

@section('content')
    <div class="title">Actualización de Requisitos</div>
    <div class="subtitle">Trámite de Pasaporte Estelar</div>

    <p style="color: #888888; line-height: 1.6;">
        Estimado cliente, para procesar el **Pasaporte Estelar** del pasajero **{{ $passengerName }}**, nuestro departamento de seguridad ha identificado que se requiere información adicional. Sin esta información, la Agencia Espacial no podrá emitir la autorización de vuelo.
    </p>

    <div class="box">
        <div class="field-label">Documentación Pendiente</div>
        <ul style="color: #ffffff; font-size: 14px; margin: 0; padding-left: 20px;">
            @foreach($missingDocs as $doc)
                <li style="margin-bottom: 8px;">{{ $option ?? $doc }}</li>
            @endforeach
        </ul>
    </div>

    @if($notes)
        <div class="field-label">Observaciones de su Gestor</div>
        <p style="color: #ffffff; font-size: 14px; background: rgba(255,255,255,0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);">
            {{ $notes }}
        </p>
    @endif

    <p style="color: #888888; line-height: 1.6; font-size: 12px; margin-top: 30px;">
        Por favor, responda a este correo adjuntando los documentos solicitados o póngase en contacto con su Gestor para completar el trámite.
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Ver mi Panel</a>
    </div>
@endsection
