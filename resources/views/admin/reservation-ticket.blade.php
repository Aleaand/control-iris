<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRIS — Ticket de Reserva #{{ substr($res->id_locator, 0, 8) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=DM+Sans:wght@300;400;500&family=JetBrains+Mono:wght@400;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --cream: #f5f2ec;
            --ink: #0e0c0a;
            --violet: #3d1f8a;
            --violet-mid: #6b3fa0;
            --violet-light: #c4b0e8;
            --gold: #b8955a;
            --divider: rgba(14, 12, 10, 0.1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #1a1625;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem 8rem;
        }

        .ticket-shell {
            width: 100%;
            max-width: 860px;
            position: relative;
        }

        /* ── Main ticket card ── */
        .ticket {
            background: var(--cream);
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255, 255, 255, 0.06),
                0 40px 100px rgba(0, 0, 0, 0.55),
                0 8px 30px rgba(61, 31, 138, 0.25);
            position: relative;
        }

        /* Perforated tear line */
        .tear-line {
            position: relative;
            display: flex;
            align-items: center;
        }

        .tear-line::before,
        .tear-line::after {
            content: '';
            display: block;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #1a1625;
            flex-shrink: 0;
            position: relative;
            z-index: 2;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .tear-dots {
            flex: 1;
            border-top: 2px dashed rgba(14, 12, 10, 0.15);
            margin: 0 -1px;
        }

        /* ── Header strip ── */
        .ticket-header {
            background: var(--ink);
            padding: 2rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Subtle star field */
        .ticket-header::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(1px 1px at 15% 30%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 35% 70%, rgba(255, 255, 255, 0.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 55% 20%, rgba(255, 255, 255, 0.5) 0%, transparent 100%),
                radial-gradient(1px 1px at 75% 60%, rgba(255, 255, 255, 0.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 88% 40%, rgba(255, 255, 255, 0.4) 0%, transparent 100%),
                radial-gradient(1.5px 1.5px at 25% 85%, rgba(255, 255, 255, 0.25) 0%, transparent 100%),
                radial-gradient(1px 1px at 65% 45%, rgba(255, 255, 255, 0.35) 0%, transparent 100%),
                radial-gradient(1px 1px at 92% 75%, rgba(255, 255, 255, 0.3) 0%, transparent 100%);
            pointer-events: none;
        }

        /* Violet glow in header */
        .ticket-header::after {
            content: '';
            position: absolute;
            right: -80px;
            top: -80px;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(107, 63, 160, 0.35) 0%, transparent 70%);
            pointer-events: none;
        }

        .header-logo-wrap {
            position: relative;
            z-index: 1;
        }

        .header-logo-wrap img {
            height: 40px;
            filter: invert(1) brightness(10);
        }

        .header-tagline {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            letter-spacing: 0.22em;
            color: rgba(255, 255, 255, 0.35);
            text-transform: uppercase;
            margin-top: 6px;
        }

        .header-locator {
            position: relative;
            z-index: 1;
            text-align: right;
        }

        .locator-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.2em;
            color: var(--violet-light);
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .locator-code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.6rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.12em;
            line-height: 1;
        }

        .locator-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 8px;
            background: rgba(107, 63, 160, 0.35);
            border: 1px solid rgba(196, 176, 232, 0.2);
            border-radius: 4px;
            padding: 3px 8px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.15em;
            color: var(--violet-light);
            text-transform: uppercase;
        }

        /* ── Flight strip ── */
        .flight-strip {
            background: var(--violet);
            padding: 1.25rem 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .flight-endpoint {
            text-align: center;
        }

        .flight-code {
            font-family: 'Playfair Display', serif;
            font-size: 2.5rem;
            font-weight: 900;
            color: #fff;
            line-height: 1;
            letter-spacing: -0.02em;
        }

        .flight-city {
            font-size: 9px;
            letter-spacing: 0.18em;
            color: var(--violet-light);
            text-transform: uppercase;
            margin-top: 4px;
            font-family: 'JetBrains Mono', monospace;
        }

        .flight-arrow {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }

        .flight-line {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .flight-line-inner {
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.25);
            position: relative;
        }

        .flight-line-inner::after {
            content: '▶';
            position: absolute;
            right: -6px;
            top: -7px;
            font-size: 8px;
            color: rgba(255, 255, 255, 0.4);
        }

        .flight-num {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.15em;
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
        }

        /* ── Body: two columns ── */
        .ticket-body {
            display: grid;
            grid-template-columns: 1fr 280px;
            gap: 0;
        }

        .ticket-main {
            padding: 2rem 2.5rem;
            border-right: 1px dashed var(--divider);
        }

        .ticket-stub {
            padding: 2rem;
            background: rgba(14, 12, 10, 0.02);
        }

        /* ── Section headers ── */
        .section-label {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.22em;
            color: rgba(14, 12, 10, 0.35);
            text-transform: uppercase;
            border-bottom: 1px solid var(--divider);
            padding-bottom: 8px;
            margin-bottom: 1.25rem;
        }

        /* ── Passenger ── */
        .passenger-name {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 900;
            color: var(--ink);
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .passenger-email {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: rgba(14, 12, 10, 0.4);
            margin-top: 4px;
        }

        .badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            padding: 3px 8px;
            border-radius: 3px;
            font-weight: 700;
        }

        .badge-violet {
            background: rgba(61, 31, 138, 0.08);
            color: var(--violet);
            border: 1px solid rgba(61, 31, 138, 0.15);
        }

        .badge-emerald {
            background: rgba(5, 150, 105, 0.07);
            color: #065f46;
            border: 1px solid rgba(5, 150, 105, 0.15);
        }

        /* ── Flight details grid ── */
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem 2rem;
            margin-top: 0;
        }

        .detail-item {}

        .detail-key {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5px;
            letter-spacing: 0.2em;
            color: rgba(14, 12, 10, 0.3);
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .detail-val {
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 500;
            color: var(--ink);
            line-height: 1.2;
        }

        .detail-val-accent {
            color: var(--violet);
            font-weight: 700;
        }

        .detail-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: rgba(14, 12, 10, 0.35);
            margin-top: 2px;
        }

        /* ── Logistics ── */
        .logistics-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 10px 0;
            border-bottom: 1px solid var(--divider);
        }

        .logistics-item:last-child {
            border-bottom: none;
        }

        .logistics-icon {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            border: 1px solid var(--divider);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: #fff;
        }

        .logistics-icon svg {
            width: 14px;
            height: 14px;
        }

        .logistics-name {
            font-size: 12px;
            font-weight: 500;
            color: var(--ink);
        }

        .logistics-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 9px;
            color: rgba(14, 12, 10, 0.38);
            margin-top: 2px;
        }

        /* ── Stub right column ── */
        .qr-wrap {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            display: inline-flex;
            border: 1px solid var(--divider);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .qr-placeholder {
            width: 120px;
            height: 120px;
            background: var(--cream);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* QR corner marks */
        .qr-placeholder::before,
        .qr-placeholder::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2.5px solid var(--ink);
        }

        .qr-placeholder::before {
            top: 8px;
            left: 8px;
            border-right: none;
            border-bottom: none;
        }

        .qr-placeholder::after {
            bottom: 8px;
            right: 8px;
            border-left: none;
            border-top: none;
        }

        .qr-inner-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 7.5px;
            color: rgba(14, 12, 10, 0.3);
            text-align: center;
            letter-spacing: 0.05em;
            line-height: 1.5;
            padding: 0 10px;
            text-transform: uppercase;
        }

        .qr-hint {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            color: rgba(14, 12, 10, 0.28);
            letter-spacing: 0.12em;
            text-transform: uppercase;
            line-height: 1.6;
            margin-top: 12px;
        }

        /* ── Financial summary ── */
        .fin-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            gap: 8px;
        }

        .fin-label {
            font-size: 11px;
            color: rgba(14, 12, 10, 0.42);
        }

        .fin-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: var(--ink);
        }

        .fin-discount {
            color: #065f46;
        }

        .fin-total-label {
            font-family: 'Playfair Display', serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--ink);
        }

        .fin-total-val {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--violet);
            letter-spacing: -0.02em;
        }

        .payment-confirmed {
            margin-top: 10px;
            background: rgba(5, 150, 105, 0.07);
            border: 1px solid rgba(5, 150, 105, 0.18);
            border-radius: 6px;
            padding: 6px 10px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #065f46;
            text-align: center;
            font-weight: 700;
        }

        /* ── Footer ── */
        .ticket-footer {
            background: var(--ink);
            padding: 12px 2.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .footer-text {
            font-family: 'JetBrains Mono', monospace;
            font-size: 8px;
            letter-spacing: 0.18em;
            color: rgba(255, 255, 255, 0.22);
            text-transform: uppercase;
        }

        .footer-dots {
            display: flex;
            gap: 4px;
        }

        .footer-dot {
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.12);
        }

        .footer-dot:nth-child(2) {
            background: rgba(107, 63, 160, 0.5);
        }

        /* ── Action buttons ── */
        .actions {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            display: flex;
            gap: 10px;
            z-index: 100;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--cream);
            color: var(--ink);
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            padding: 14px 24px;
            border-radius: 40px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
            transition: transform .15s, box-shadow .15s;
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.45);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.07);
            color: rgba(255, 255, 255, 0.65);
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            font-weight: 700;
            padding: 14px 20px;
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: background .15s;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        /* ── Responsive ── */
        @media (max-width: 700px) {
            .ticket-body {
                grid-template-columns: 1fr;
            }

            .ticket-main {
                border-right: none;
                border-bottom: 1px dashed var(--divider);
            }

            .ticket-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .header-locator {
                text-align: left;
            }

            .flight-code {
                font-size: 1.8rem;
            }
        }

        /* ── Print ── */
        @media print {
            .actions {
                display: none;
            }

            body {
                background: #fff;
                padding: 0;
            }

            .ticket {
                box-shadow: none;
                border-radius: 0;
            }
        }
    </style>
</head>

<body>
    <div class="ticket-shell">
        <div class="ticket">

            {{-- ── Header ── --}}
            <div class="ticket-header">
                <div class="header-logo-wrap">
                    <img src="/assets/logo_iris.png" alt="IRIS">
                    <div class="header-tagline">Iris Aerospace</div>
                </div>
                <div class="header-locator">
                    <div class="locator-label">Localizador de Reserva</div>
                    <div class="locator-code">{{ strtoupper($res->id_locator) }}</div>
                    <div class="mt-1">
                        <span class="locator-badge">
                            <svg width="6" height="6" viewBox="0 0 8 8" fill="none">
                                <circle cx="4" cy="4" r="3" fill="currentColor" opacity="0.6" />
                            </svg>
                            Documento Oficial
                        </span>
                    </div>
                </div>
            </div>

            {{-- ── Flight strip ── --}}
            <div class="flight-strip">
                <div class="flight-endpoint">
                    <div class="flight-code">{{ $res->spaceFlight?->flight_code }}</div>
                    <div class="flight-city">{{ $res->spaceFlight?->origin?->name }} —
                        {{ $res->spaceFlight?->destination?->name }}</div>
                </div>
                <div class="flight-arrow">
                    <div class="flight-line">
                        <div class="flight-line-inner"></div>
                    </div>
                    <div class="flight-num">Vuelo {{ $res->spaceFlight?->flight_code }} &nbsp;·&nbsp;
                        {{ $res->spaceFlight?->departure_date?->format('d M Y') }}
                    </div>
                </div>
                <div class="flight-endpoint" style="text-align:right">
                    <div class="flight-code">
                        {{ strtoupper(substr($res->spaceFlight?->destination?->name ?? 'DST', 0, 3)) }}
                    </div>
                    <div class="flight-city">{{ $res->spaceFlight?->destination?->name }}</div>
                </div>
            </div>

            {{-- ── Body ── --}}
            <div class="ticket-body">

                {{-- Main column --}}
                <div class="ticket-main">

                    {{-- Pasajeros --}}
                    <div class="section-label">Pasajero</div>
                    <div class="passenger-name">{{ $res->user?->name }}</div>
                    <div class="passenger-email">{{ $res->user?->email }}</div>
                    <div class="badge-row">
                        <span class="badge badge-violet">ID #{{ $res->user?->id }}</span>
                        @if($res->discount_applied)
                            <span class="badge badge-emerald">✦ Certified Space Traveler</span>
                        @endif
                    </div>

                    {{-- Detalles vuelo --}}
                    <div class="section-label" style="margin-top:1.75rem">Detalles del Vuelo</div>
                    <div class="details-grid">
                        <div class="detail-item">
                            <div class="detail-key">Origen</div>
                            <div class="detail-val">Base de Lanzamiento</div>
                            <div class="detail-sub">{{ $res->spaceFlight?->origin?->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-key">Destino</div>
                            <div class="detail-val">Base de Aterrizaje</div>
                            <div class="detail-sub">{{ $res->spaceFlight?->destination?->name }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-key">Fecha de Salida</div>
                            <div class="detail-val">{{ $res->spaceFlight?->departure_date?->format('d M Y') }}</div>
                            <div class="detail-sub">Vuelo · {{ $res->spaceFlight?->flight_code }}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-key">Cabina / Asiento</div>
                            <div class="detail-val detail-val-accent">{{ strtoupper($res->seat_type) }}</div>
                            <div class="detail-sub">SEAT: {{ $res->seat_number ?: 'TBA' }}</div>
                        </div>
                    </div>

                    {{-- Logística --}}
                    @if($res->logistics)
                        <div class="section-label" style="margin-top:1.75rem">Servicios Incluidos</div>
                        <div>
                            @if($res->logistics->hotel)
                                <div class="logistics-item">
                                    <div class="logistics-icon" style="color:#db2777">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="logistics-name">{{ $res->logistics->hotel->name }}</div>
                                        <div class="logistics-sub">{{ $res->logistics->hotel_nights }} noches incluidas</div>
                                    </div>
                                </div>
                            @endif

                            @if($res->logistics->terrestrialFlight)
                                <div class="logistics-item">
                                    <div class="logistics-icon" style="color:#d97706">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="logistics-name">Vuelo Terrestre</div>
                                        <div class="logistics-sub">
                                            {{ $res->logistics->terrestrialFlight->originLocation->name }} →
                                            {{ $res->logistics->terrestrialFlight->destinationLocation->name }}
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($res->logistics->training_included)
                                <div class="logistics-item">
                                    <div class="logistics-icon" style="color:#059669">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="logistics-name">Iris Training Complete</div>
                                        <div class="logistics-sub">Programa de entrenamiento certificado</div>
                                    </div>
                                </div>
                            @endif

                            @if($res->logistics->vip_transfer_included)
                                <div class="logistics-item">
                                    <div class="logistics-icon" style="color:#b45309">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="logistics-name">Transfer VIP Iris Experience</div>
                                        <div class="logistics-sub">Traslado exclusivo incluido</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                </div>

                {{-- Stub column --}}
                <div class="ticket-stub">

                    {{-- QR --}}
                    <div class="section-label">Código de Embarque</div>
                    <div class="qr-wrap">
                        <div class="qr-placeholder">
                            <div class="qr-inner-text">QR generado<br>en mostrador<br>Iris</div>
                        </div>
                    </div>
                    <div class="qr-hint">Escanee en el<br>mostrador Iris<br>Aerospace</div>

                    {{-- Divider --}}
                    <div style="margin: 1.5rem 0; border-top: 1px dashed var(--divider)"></div>

                    {{-- Financiero --}}
                    <div class="section-label">Resumen Financiero</div>
                    @php $snapshot = $res->price_snapshot ?? []; @endphp

                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <div class="fin-row">
                            <span class="fin-label">Base fare & logística</span>
                            <span
                                class="fin-val">${{ number_format($snapshot['subtotal'] ?? $res->total_price, 2, ',', '.') }}</span>
                        </div>
                        @if($res->discount_applied)
                            <div class="fin-row">
                                <span class="fin-label fin-discount">S.T.C. Descuento (10%)</span>
                                <span
                                    class="fin-val fin-discount">−${{ number_format($snapshot['discount_amount'] ?? 0, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        <div style="border-top: 1px solid var(--divider); padding-top:10px; margin-top:4px;">
                            <div class="fin-row">
                                <span class="fin-total-label">Total Pagado</span>
                                <span class="fin-total-val">${{ number_format($res->total_price, 2, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    @if($res->payment_status === 'paid')
                        <div class="payment-confirmed">✓ &nbsp; Pago Confirmado</div>
                    @endif

                </div>
            </div>

            {{-- ── Tear line ── --}}
            <div class="tear-line">
                <div class="tear-dots"></div>
            </div>

            {{-- ── Footer ── --}}
            <div class="ticket-footer">
                <div class="footer-text">IRIS Aerospace · Terminal 1, Spaceport America · iris.space</div>
                <div class="footer-dots">
                    <div class="footer-dot"></div>
                    <div class="footer-dot"></div>
                    <div class="footer-dot"></div>
                </div>
            </div>

        </div>
    </div>

    {{-- Actions --}}
    <div class="actions no-print">
        <button class="btn-print" onclick="window.print()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2m8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Descargar
        </button>
        <button class="btn-back" onclick="window.location.href='/admin/reservations'">
            Cerrar
        </button>
    </div>

</body>

</html>