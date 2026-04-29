<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IRIS — Documento de Reserva #{{ strtoupper($res->id_locator) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --iris-violet: #6366f1;
            --iris-cyan: #06b6d4;
            --bg-card: #ffffff;
            --text-dark: #0f172a;
            --text-gray: #475569;
        }

        body {
            font-family: 'Outfit', sans-serif;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            overflow-x: hidden;
            background: #020617;
            position: relative;
        }

        /* ── Breathing Background ── */
        .breathing-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            background: radial-gradient(circle at 50% 50%, #1e1b4b 0%, #020617 100%);
        }

        .breathing-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 50%, rgba(99, 102, 241, 0.15) 0%, transparent 60%);
            animation: breathe 8s ease-in-out infinite;
        }

        @keyframes breathe {

            0%,
            100% {
                transform: scale(1);
                opacity: 0.5;
            }

            50% {
                transform: scale(1.5);
                opacity: 0.8;
            }
        }

        /* ── The Card ── */
        .card {
            width: 950px;
            background: var(--bg-card);
            border-radius: 40px;
            overflow: hidden;
            display: flex;
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.6);
            position: relative;
            animation: slideUp 1s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ── Left Side (Main) ── */
        .card-main {
            flex: 1;
            padding: 3rem;
            position: relative;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
        }

        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-img {
            width: 45px;
            height: 45px;
            object-contain: fit;
        }

        .brand-name {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-dark);
        }

        .doc-id {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-gray);
            background: #f1f5f9;
            padding: 6px 14px;
            border-radius: 100px;
        }

        /* Flight Path */
        .path {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 3.5rem;
        }

        .node {
            text-align: center;
        }

        .node .city-code {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1;
            color: var(--text-dark);
            letter-spacing: -0.04em;
        }

        .node .city-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-gray);
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .path-line {
            flex: 1;
            height: 2px;
            background-image: linear-gradient(to right, #e2e8f0 50%, transparent 50%);
            background-size: 10px 100%;
            margin: 0 2rem;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .path-line::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            background: transparent;
        }

        .spaceship-icon {
            position: absolute;
            color: var(--iris-violet);
            z-index: 2;
            transform: rotate(90deg);
            background: #fff;
            padding: 4px;
            border-radius: 50%;
        }

        /* Details Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2.5rem;
        }

        .item label {
            display: block;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--text-gray);
            margin-bottom: 8px;
        }

        .item value {
            display: block;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* ── Right Side (Stub) ── */
        .card-stub {
            width: 280px;
            background: #f8fafc;
            border-left: 2px dashed #e2e8f0;
            padding: 2rem 1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .qr-placeholder {
            width: 140px;
            height: 140px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            margin-bottom: 2rem;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.02);
        }

        .locator-wrap {
            margin-bottom: 2rem;
        }

        .locator-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--text-gray);
            margin-bottom: 6px;
        }

        .locator-val {
            font-size: 1rem;
            margin-inline: 10px;
            font-weight: 900;
            color: var(--iris-violet);
            letter-spacing: 0.05em;
        }

        .passenger {
            margin-top: auto;
            text-align: left;
            width: 100%;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
        }

        .pass-label {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--text-gray);
            margin-bottom: 4px;
        }

        .pass-name {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-dark);
        }

        /* ── Actions ── */
        .actions {
            position: fixed;
            bottom: 3rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 1.5rem;
            z-index: 1000;
        }

        .btn {
            padding: 16px 36px;
            border-radius: 100px;
            font-weight: 800;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
            cursor: pointer;
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            backdrop-filter: blur(12px);
        }

        .btn-back {
            background: linear-gradient(135deg, var(--iris-violet) 0%, #4338ca 100%);
            color: #fff;
            box-shadow: 0 15px 35px rgba(99, 102, 241, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .btn-back:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 20px 45px rgba(99, 102, 241, 0.6);
            filter: brightness(1.1);
        }

        .btn-print {
            background: rgba(255, 255, 255, 0.95);
            color: #020617;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .btn-print:hover {
            transform: translateY(-5px) scale(1.05);
            background: #fff;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        /* ── Price Table ── */
        .price-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 10px;
            padding: 5px 0;
            border-bottom: 1px dashed #f1f5f9;
            gap: 12px;
        }

        .price-row span:first-child {
            flex: 1;
            line-height: 1.2;
        }

        .price-row span:last-child {
            text-align: right;
            white-space: nowrap;
            font-family: 'Monaco', 'Consolas', monospace;
            letter-spacing: -0.02em;
        }

        .price-row:last-child {
            border-bottom: none;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            body {
                padding: 1rem;
                padding-bottom: 8rem;
                align-items: flex-start;
            }

            .card {
                width: 100%;
                max-width: 600px;
                flex-direction: column;
                border-radius: 30px;
                margin: 0 auto;
            }

            .card-main {
                padding: 2rem;
            }

            .card-stub {
                width: 100%;
                border-left: none;
                border-top: 2px dashed #e2e8f0;
                padding: 2rem;
            }

            .grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
            }

            .path {
                margin: 2rem 0;
            }

            .actions {
                bottom: 1.5rem;
                width: 90%;
                justify-content: center;
            }

            .btn {
                padding: 12px 24px;
                font-size: 12px;
                flex: 1;
                justify-content: center;
            }
        }

        @media (max-width: 640px) {
            .grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .city-code {
                font-size: 2rem;
            }
        }

        @media print {
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .actions, .breathing-bg { display: none !important; }
            body { background: #fff !important; padding: 0 !important; color: #000 !important; }
            .card { 
                box-shadow: none !important; 
                border: 2px solid #000 !important; 
                border-radius: 0 !important; 
                width: 100% !important; 
                flex-direction: row !important;
                margin: 0 !important;
            }
            .card-main { padding: 2rem !important; }
            .card-stub { 
                width: 250px !important; 
                border-left: 2px dashed #000 !important; 
                border-top: none !important;
                background: #fcfcfc !important;
                padding: 1rem !important;
            }
            .bg-white, .bg-slate-50 { background: #fff !important; border: 1px solid #eee !important; }
            .bg-slate-900 { background: #fff !important; border: 2px solid #000 !important; color: #000 !important; padding: 1.5rem !important; }
            .bg-slate-900 * { color: #000 !important; }
            .text-slate-500, .text-slate-400 { color: #555 !important; }
            .spaceship-icon svg { color: #000 !important; }
        }
    </style>
</head>

<body>
    <div class="breathing-bg"></div>

    <div class="card">

        <div class="card-main">
            <div class="header">
                <div class="logo-wrap">
                    <img src="/assets/logo_iris_black.png" class="logo-img" alt="IRIS">
                    <span class="brand-name">IRIS AEROSPACE</span>
                </div>
                <div class="doc-id">RESERVA #{{ str_pad($res->id, 4, '0', STR_PAD_LEFT) }}</div>
            </div>

            <div class="path">
                <div class="node">
                    <div class="city-code">{{ strtoupper(substr($res->spaceFlight?->origin?->name ?? 'ORG', 0, 3)) }}
                    </div>
                    <div class="city-name">{{ $res->spaceFlight?->origin?->name }}</div>
                </div>
                <div class="path-line">
                    <div class="spaceship-icon">
                        <svg style="width:20px; height:20px;" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12,2L4.5,20.29L5.21,21L12,18L18.79,21L19.5,20.29L12,2Z" />
                        </svg>
                    </div>
                </div>
                <div class="node">
                    <div class="city-code" style="color: var(--iris-violet);">
                        {{ strtoupper(substr($res->spaceFlight?->destination?->name ?? 'DST', 0, 3)) }}
                    </div>
                    <div class="city-name">{{ $res->spaceFlight?->destination?->name }}</div>
                </div>
            </div>

            <div class="grid">
                <div class="item">
                    <label>Fecha de Salida</label>
                    <value>{{ $res->spaceFlight?->departure_date?->format('d M, Y') }}</value>
                </div>
                <div class="item">
                    <label>Hora de Embarque</label>
                    <value>{{ $res->spaceFlight?->departure_date?->subMinutes(45)->format('H:i') }}</value>
                </div>
                <div class="item">
                    <label>Clase de Vuelo</label>
                    <value>{{ strtoupper($res->seat_type) }}</value>
                </div>
                <div class="item">
                    <label>Unidad de Transporte</label>
                    <value>{{ $res->spaceFlight?->flight_code }}</value>
                </div>
                <div class="item">
                    <label>Asiento Asignado</label>
                    <value style="color: var(--iris-violet);">{{ $res->seat_number ?: 'A1-B' }}</value>
                </div>
                <div class="item">
                    <label>Estado de Reserva</label>
                    <value style="color: {{ 
                        match($res->status) {
                            'Confirmada', 'Confirmed' => '#10b981',
                            'Pendiente', 'Pending' => '#f59e0b',
                            'Cancelada', 'Cancelled' => '#ef4444',
                            default => '#64748b'
                        }
                    }};">{{ strtoupper($res->status) }}</value>
                </div>
            </div>

            {{-- Administrative Info Section --}}
            <div class="py-10 border-t border-slate-100">
                <div class="mb-8 flex items-center gap-4">
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Información Administrativa</h4>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    {{-- Client Block --}}
                    <div>
                        <h5 class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-tight">Titular de la Reserva (Comprador)</h5>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Nombre Completo</label>
                                <div class="text-[14px] font-bold text-slate-800">{{ $res->user?->name }} {{ $res->user?->primarylastname }} {{ $res->user?->secondarylastname }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Contacto Email</label>
                                    <div class="text-[12px] font-semibold text-slate-600 truncate">{{ $res->user?->email }}</div>
                                </div>
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Teléfono Titular</label>
                                    <div class="text-[12px] font-bold text-slate-800">{{ $res->user?->phone ?: 'No registrado' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Passenger Block --}}
                    <div>
                        <h5 class="text-[10px] font-bold text-slate-400 uppercase mb-4 tracking-tight">Información del Pasajero (Viajero)</h5>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Nombre Completo del Viajero</label>
                                <div class="text-[14px] font-bold text-slate-800">{{ $res->passenger?->full_name ?: $res->user?->name }}</div>
                            </div>
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Identificación (ID)</label>
                                    <div class="text-[12px] font-semibold text-slate-600">{{ $res->passenger?->document_number ?: 'N/A' }}</div>
                                </div>
                                <div>
                                    <label class="block text-[8px] font-black text-slate-400 uppercase mb-1">Teléfono Pasajero</label>
                                    <div class="text-[12px] font-bold text-slate-800">{{ $res->passenger?->phone ?? $res->user?->phone ?? 'No registrado' }}</div>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[8px] font-black text-indigo-400 uppercase mb-1">Pasaporte Espacial Iris</label>
                                    <div class="text-[12px] font-bold text-indigo-600">{{ $res->passenger?->iris_passport_number ?: 'GESTIÓN PENDIENTE' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Compliance & Status --}}
            <div class="py-10 border-t border-slate-100">
                <div class="mb-6 flex items-center gap-4">
                    <h4 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Estado de Cumplimiento
                    </h4>
                    <div class="h-px flex-1 bg-slate-100"></div>
                </div>

                {{-- Critical Alerts --}}
                @if(!$res->passenger?->isFlightReady())
                    <div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-start gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-amber-800 uppercase tracking-tight">Acciones Pendientes
                                Requeridas</p>
                            <p class="text-[10px] text-amber-700 leading-relaxed mt-1">
                                Este pasajero no cumple con todos los requisitos para el despegue. Recuerde que el
                                **Pasaporte Espacial** y el **Iris Training** deben estar verificados al menos 72h antes del
                                lanzamiento para evitar la cancelación automática.
                            </p>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-6">
                    <div
                        class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2.5 h-2.5 rounded-full {{ $res->passenger?->hasValidPassport() ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' }}">
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-800">Pasaporte Espacial</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                    {{ $res->passenger?->iris_passport_number ?: 'Gestión en curso' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[9px] font-black px-2 py-1 rounded bg-white {{ $res->passenger?->hasValidPassport() ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $res->passenger?->hasValidPassport() ? 'VERIFICADO' : 'PENDIENTE' }}
                        </span>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2.5 h-2.5 rounded-full {{ $res->passenger?->hasValidTraining() ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]' }}">
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-800">Iris Training Certificate</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                    {{ $res->passenger?->training_certificate_date ? 'Apto desde ' . $res->passenger->training_certificate_date->format('d/m/Y') : 'Certificación Pendiente' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[9px] font-black px-2 py-1 rounded bg-white {{ $res->passenger?->hasValidTraining() ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $res->passenger?->hasValidTraining() ? 'APTO' : 'PENDIENTE' }}
                        </span>
                    </div>

                    @if($res->logistics?->hotel_id)
                        <div
                            class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-800">Alojamiento (Hotel)</p>
                                    <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                        {{ $res->logistics->hotel->name }} • {{ $res->logistics->hotel_nights }} Noches
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-[9px] font-black px-2 py-1 rounded bg-white text-emerald-600">CONFIRMADO</span>
                        </div>
                    @endif

                    @if($res->logistics?->terrestrial_flight_id)
                        <div
                            class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                            <div class="flex items-center gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]">
                                </div>
                                <div>
                                    <p class="text-[10px] font-bold text-slate-800">Conexión Terrestre</p>
                                    <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                        {{ $res->logistics->terrestrialFlight->originLocation->name }} →
                                        {{ $res->logistics->terrestrialFlight->destinationLocation->name }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-[9px] font-black px-2 py-1 rounded bg-white text-emerald-600">CONFIRMADO</span>
                        </div>
                    @endif

                    <div
                        class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2.5 h-2.5 rounded-full {{ $res->logistics?->refund_insurance_included ? 'bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]' : 'bg-slate-300' }}">
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-800">Seguro de Reembolso</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                    {{ $res->logistics?->refund_insurance_included ? 'Cobertura de Cancelación Activa' : 'Cobertura No Contratada' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[9px] font-black px-2 py-1 rounded bg-white {{ $res->logistics?->refund_insurance_included ? 'text-indigo-600' : 'text-slate-400' }}">
                            {{ $res->logistics?->refund_insurance_included ? 'INCLUIDO' : 'N/A' }}
                        </span>
                    </div>

                    <div
                        class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl transition-all hover:bg-slate-100/80">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-2.5 h-2.5 rounded-full {{ $res->payment_status === 'paid' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : 'bg-rose-500' }}">
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-800">Estado de Pago</p>
                                <p class="text-[9px] text-slate-500 uppercase tracking-tighter">
                                    {{ $res->payment_status === 'paid' ? 'Pagado' : 'Pendiente de Pago' }}
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-[9px] font-black px-2 py-1 rounded bg-white {{ $res->payment_status === 'paid' ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ strtoupper($res->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Cancellation Policy --}}
            <div class="px-12 py-10 bg-slate-900 text-white rounded-[20px]">
                <div class="mb-8 flex items-center gap-4">
                    <h4 class="text-[11px] font-black text-indigo-400 uppercase tracking-[0.2em]">Política de
                        Cancelación y Reembolso</h4>
                    <div class="h-px flex-1 bg-white/10"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 print:grid-cols-1 gap-10">
                    <div class="space-y-6">
                        <section>
                            <h5 class="text-[10px] font-black text-white uppercase mb-2">1. Baremo de Reembolso (Seguro
                                Activo)</h5>
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                • <strong>+30 días:</strong> 90% de reembolso.<br>
                                • <strong>30 a 7 días:</strong> 50% de reembolso.<br>
                                • <strong>7 días a 72h:</strong> 10% de reembolso.<br>
                                • <strong>
                                    < 72h:</strong> Sin derecho a reembolso (0%).
                            </p>
                        </section>
                        <section>
                            <h5 class="text-[10px] font-black text-white uppercase mb-2">2. Cancelación por
                                Incumplimiento</h5>
                            <p class="text-[10px] text-slate-400 leading-relaxed italic">
                                El pasajero debe disponer de Pasaporte e Iris Training 72h antes del despegue. El
                                incumplimiento anulará la plaza automáticamente con retención del 100%.
                            </p>
                        </section>
                        <section>
                            <h5 class="text-[10px] font-black text-white uppercase mb-2">3. Ejecución y Plazos</h5>
                            <p class="text-[10px] text-slate-400">Devolución automática al método original. Plazo: hasta
                                30 días naturales.</p>
                        </section>
                    </div>

                    <div class="space-y-6">
                        <section>
                            <h5 class="text-[10px] font-black text-rose-400 uppercase mb-2">4. Servicios No
                                Reembolsables</h5>
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                Vuelos Terrestres (Conexiones) y Alojamiento Externo (Hoteles) no admiten devolución una
                                vez confirmados.
                            </p>
                        </section>
                        <section>
                            <h5 class="text-[10px] font-black text-white uppercase mb-2">5. Casos Especiales</h5>
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                Iris Training y Gestión de Pasaporte no se reembolsarán si el proceso ya ha sido
                                iniciado, aprobado o realizado.
                            </p>
                        </section>
                        <section class="p-4 border border-indigo-500/30 bg-indigo-500/5 rounded-xl">
                            <h5 class="text-[10px] font-black text-indigo-300 uppercase mb-2">6. Garantía Iris Aerospace
                            </h5>
                            <p class="text-[10px] text-slate-400 leading-relaxed">
                                Reembolso del 100% o reubicación gratuita solo por motivos técnicos de Iris Aerospace o
                                meteorología espacial adversa.
                            </p>
                        </section>
                    </div>
                </div>
            </div>

        </div>

        <div class="card-stub">
            {{-- Unified Header Block --}}
            <div
                class="w-full bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm flex flex-col items-center">
                <div class="qr-placeholder mb-6">
                    <svg class="w-full h-full text-slate-200" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M3 3h8v8H3V3zm2 2v4h4V5H5zm8-2h8v8-h8V3zm2 2v4h4V5h-4zM3 13h8v8H3v-8zm2 2v4h4v-4H5zm13-2h3v2h-3v-2zm-3 0h2v3h-2v-3zm3 3h3v2h-3v-2zm-3 0h2v3h-2v-3zm3 3h3v2h-3v-2z" />
                    </svg>
                </div>

                <div class="locator-wrap">
                    <div class="locator-label">Localizador</div>
                    <div class="locator-val">{{ strtoupper($res->id_locator) }}</div>
                </div>
            </div>

            {{-- Registry & Client Block --}}
            <div class="w-full mt-6 bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm">
                <div class="pass-label">Titular de la Reserva</div>
                <div class="pass-name truncate">{{ $res->user?->name }}</div>
                <div style="font-size: 9px; color: var(--text-gray); margin-top: 4px; text-transform: uppercase; letter-spacing: 0.05em;">Registry: #{{ str_pad($res->user_id, 6, '0', STR_PAD_LEFT) }}</div>
            </div>

            {{-- Detailed Passenger Info --}}
            <div class="w-full mt-6 bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm text-left">
                <div class="pass-label mb-3">Datos del Pasajero</div>
                
                <div class="mb-3">
                    <div style="font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Nombre Completo</div>
                    <div style="font-size: 11px; font-weight: 700; color: #1e293b;">{{ $res->passenger?->full_name ?: $res->user?->name }}</div>
                </div>

                <div class="flex gap-4">
                    <div class="flex-1">
                        <div style="font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Identidad (ID)</div>
                        <div style="font-size: 10px; font-weight: 600; color: #334155;">{{ $res->passenger?->document_number ?: 'N/A' }}</div>
                    </div>
                    <div class="flex-1">
                        <div style="font-size: 8px; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Pasaporte Iris</div>
                        <div style="font-size: 10px; font-weight: 600; color: var(--iris-violet);">{{ $res->passenger?->iris_passport_number ?: 'PENDIENTE' }}</div>
                    </div>
                </div>
            </div>

            {{-- Price Breakdown Block --}}
            <div class="w-full mt-6 bg-white p-6 rounded-[30px] border border-slate-100 shadow-sm">
                <div class="pass-label mb-4">Desglose de Facturación</div>

                <div class="space-y-1 mb-6">
                    @php
                        $snap = $res->price_snapshot;
                    @endphp
                    @if($snap && is_array($snap))
                        {{-- Vuelo Espacial --}}
                        @if(($snap['space_flight_price'] ?? 0) > 0 || ($snap['space'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Vuelo Espacial</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['space_flight_price'] ?? $snap['space'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        {{-- Hotel --}}
                        @if(($snap['hotel_price'] ?? 0) > 0 || ($snap['hotel'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Alojamiento</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['hotel_price'] ?? $snap['hotel'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        {{-- Terrestre --}}
                        @if(($snap['terrestrial_price'] ?? 0) > 0 || ($snap['terrestrial'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Conexión Terrestre</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['terrestrial_price'] ?? $snap['terrestrial'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        {{-- Extras --}}
                        @if(($snap['training_fee'] ?? 0) > 0 || ($snap['training'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Iris Training</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['training_fee'] ?? $snap['training'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        @if(($snap['passport_fee'] ?? 0) > 0 || ($snap['passport'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Gestión Pasaporte</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['passport_fee'] ?? $snap['passport'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        @if(($snap['vip_transfer_fee'] ?? 0) > 0 || ($snap['vip'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Transfer VIP</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['vip_transfer_fee'] ?? $snap['vip'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        @if(($snap['insurance_fee'] ?? 0) > 0 || ($snap['insurance'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-slate-500">Seguro Cancelación</span>
                                <span
                                    class="font-bold text-slate-700">{{ number_format($snap['insurance_fee'] ?? $snap['insurance'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        {{-- Descuentos / Ajustes --}}
                        @if(($snap['discount_amount'] ?? 0) > 0 || ($snap['discount_amt'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-emerald-600 font-bold">DTO Certificación</span>
                                <span
                                    class="font-bold text-emerald-600">-{{ number_format($snap['discount_amount'] ?? $snap['discount_amt'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif

                        @if(($snap['manual_adjustment_amount'] ?? 0) > 0 || ($snap['adj_amount'] ?? 0) > 0)
                            <div class="price-row">
                                <span class="text-indigo-600 font-bold">Ajuste Cortesía</span>
                                <span
                                    class="font-bold text-indigo-600">-{{ number_format($snap['manual_adjustment_amount'] ?? $snap['adj_amount'] ?? 0, 2) }}
                                    €</span>
                            </div>
                        @endif
                    @else
                        <div class="price-row">
                            <span class="text-slate-500">Vuelo Iris</span>
                            <span class="font-bold text-slate-700">{{ number_format($res->total_price, 2) }} €</span>
                        </div>
                    @endif
                </div>

                <div class="pt-4 border-t border-slate-100">
                    <div class="pass-label">Precio Final</div>
                    <div
                        style="font-size: 20px; font-weight: 900; color: var(--iris-violet); font-family: 'Monaco', 'Consolas', monospace; letter-spacing: -0.04em;">
                        {{ number_format($res->total_price, 2) }} €
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="actions">
        <button onclick="window.history.back()" class="btn btn-back">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver
        </button>
        <button onclick="window.print()" class="btn btn-print">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Imprimir
        </button>
    </div>
</body>

</html>