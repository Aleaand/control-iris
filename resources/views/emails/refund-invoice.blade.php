@extends('layouts.email', ['title' => 'Confirmación de Reembolso - Iris Aerospace'])

@section('content')
    <div class="title">Confirmación de Reembolso</div>
    <div class="subtitle">Transacción de Crédito Operativo</div>

    <p style="color: #888888; line-height: 1.6;">
        Hola **{{ $reservation->user->name }}**, le informamos que se ha procesado correctamente el reembolso solicitado para su reserva.
    </p>

    <div class="box" style="border-left-color: #0ea5e9;">
        <div class="field-label">Importe Reembolsado</div>
        <div class="field-value" style="color: #ffffff; font-size: 28px; font-family: 'JetBrains Mono', monospace;">
            {{ number_format($refundRequest->refund_amount, 2, ',', '.') }} €
        </div>

        <div class="field-label">Detalles de la Reserva</div>
        <table style="width: 100%; font-size: 12px; color: #888888; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px 0;">Localizador:</td>
                <td style="text-align: right; color: #ffffff;">{{ $reservation->id_locator }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;">Fecha:</td>
                <td style="text-align: right; color: #ffffff;">{{ now()->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 5px 0;">Método:</td>
                <td style="text-align: right; color: #ffffff;">Stripe (Tarjeta)</td>
            </tr>
        </table>
    </div>

    <p style="background: rgba(14, 165, 233, 0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(14, 165, 233, 0.2); color: #888888; font-size: 11px; font-style: italic;">
        El importe ha sido enviado a su método de pago original. Dependiendo de su entidad bancaria, los fondos pueden tardar entre 5 y 10 días hábiles en verse reflejados en su cuenta.
    </p>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ config('app.url') }}/dashboard" class="cta-button">Ver mi Panel</a>
    </div>
@endsection
