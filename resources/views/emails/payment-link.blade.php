@extends('layouts.email', ['title' => 'Enlace de Pago - Iris Aerospace'])

@section('content')
    <div class="title">¡Todo listo para su misión!</div>
    <div class="subtitle">Confirmación de Reserva #{{ $locator }}</div>

    <p style="color: #888888; line-height: 1.6;">
        Estamos a un paso de confirmar su reserva con Iris Aerospace. Para asegurar la logística de su servicio, por favor
        proceda al pago mediante nuestro terminal seguro de Stripe. O ingrese a su cuenta y en reservas tramite su pago.
    </p>

    <div class="box">
        <div class="field-label">Localizador de Reserva</div>
        <div class="field-value">#{{ $locator }}</div>

        <div class="field-label">Importe Total</div>
        <div class="field-value" style="color: #10b981; font-size: 24px;">{{ number_format($amount, 2, ',', '.') }} €</div>
    </div>

    <div style="text-align: center; margin-top: 20px;">
        <a href="{{ $paymentUrl }}" class="cta-button"
            style="background-color: #10b981; box-shadow: 0 10px 20px rgba(16, 185, 129, 0.2);">Pagar con Stripe</a>
    </div>

    <p
        style="color: #475569; font-size: 11px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 20px;">
        <strong>Información:</strong> Este enlace de pago tiene un vencimiento de 7 días. Si tiene alguna duda sobre los
        detalles de su reserva, contacte con su Gestor asignado.
    </p>
@endsection