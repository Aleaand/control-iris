@extends('layouts.email', ['title' => 'Pasaporte Estelar Aprobado - Iris Aerospace'])

@section('content')
    <div class="title">¡Autorización Concedida!</div>
    <div class="subtitle">Pasaporte Estelar IRIS Emitido</div>

    <p style="color: #888888; line-height: 1.6;">
        Estimado cliente, nos complace informarle que la Agencia Espacial ha emitido oficialmente el **Pasaporte Estelar IRIS** para **{{ $passengerName }}**.
    </p>

    <div class="box" style="border-left-color: #10b981;">
        <div class="field-label">Estado Actual</div>
        <div class="field-value" style="color: #10b981;">EMITIDO Y VÁLIDO</div>

        <div class="field-label">Resumen de Seguridad</div>
        <ul style="color: #ffffff; font-size: 14px; margin: 0; padding-left: 20px;">
            <li>Aptitud Física: <strong style="color: #10b981;">Validada</strong></li>
            <li>Análisis OFAC: <strong style="color: #10b981;">Limpio</strong></li>
            <li>Permiso de Vuelo: <strong style="color: #10b981;">Autorizado</strong></li>
        </ul>
    </div>

    <p style="color: #888888; line-height: 1.6;">
        Este documento es su credencial diplomática y de seguridad para todas las misiones operadas por Iris Aerospace. Ha sido validado con éxito tras el análisis biométrico y de antecedentes.
    </p>

    <div style="background: rgba(16, 185, 129, 0.05); padding: 20px; border-radius: 12px; border: 1px solid rgba(16, 185, 129, 0.2); margin-top: 30px;">
        <p style="color: #10b981; font-weight: bold; font-size: 11px; text-transform: uppercase; margin-top: 0;">Entrega del Documento Físico</p>
        <p style="color: #888888; font-size: 11px; margin-bottom: 0;">
            Su pasaporte estelar original le será entregado personalmente al momento de su llegada a las instalaciones de Iris Aerospace. Si prefiere recibirlo con antelación, contacte con su Gestor.
        </p>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Acceder al Control</a>
    </div>
@endsection
