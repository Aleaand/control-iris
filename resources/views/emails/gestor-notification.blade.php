@extends('layouts.email', ['title' => 'Iris Aerospace - Actualización de Expediente'])

@section('content')
    <div class="title">Comunicación de su Gestor</div>
    <div class="subtitle">Expediente Estelar - Iris Aerospace</div>

    <div style="color: #e2e8f0; line-height: 1.8; font-size: 15px;">
        {!! $content !!}
    </div>

    @if(isset($zoom_link) && $zoom_link)
        <div style="text-align: center; margin-top: 40px; margin-bottom: 20px;">
            <a href="{{ $zoom_link }}" class="cta-button">Entrar a la Sala Jitsi</a>
        </div>
        <p style="text-align: center; font-size: 10px; color: #475569; font-family: 'JetBrains Mono', monospace;">
            Link directo: {{ $zoom_link }}
        </p>
    @endif

    <div style="margin-top: 40px; padding-top: 25px; border-top: 1px solid rgba(255, 255, 255, 0.05);">
        <p style="color: #94a3b8; font-size: 12px; font-style: italic;">
            Este mensaje ha sido generado por el terminal de su gestor asignado. Para responder o realizar consultas adicionales, por favor acceda a su portal privado en Iris Aerospace.
        </p>
    </div>
@endsection
