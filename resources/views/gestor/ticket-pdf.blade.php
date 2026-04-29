<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Iris Ticket - {{ strtoupper(substr($res->id_locator, 0, 8)) }}</title>
    <style>
        body { font-family: Helvetica, sans-serif; font-size: 14px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid #10b981; }
        .logo { font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #10b981; margin-bottom: 5px; }
        .subtitle { font-size: 10px; color: #666; letter-spacing: 2px; text-transform: uppercase; }
        .row { width: 100%; clear: both; margin-bottom: 20px; }
        .col { float: left; width: 50%; }
        .label { font-size: 10px; color: #999; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .value { font-size: 16px; font-weight: bold; margin-bottom: 15px; }
        .qr-placeholder { border: 2px dashed #ccc; padding: 20px; text-align: center; margin-top: 40px; }
        .qr-text { font-family: monospace; font-size: 12px; color: #666; word-break: break-all; }
        .footer { text-align: center; margin-top: 60px; font-size: 10px; color: #999; }
        .status-badge { display: inline-block; padding: 5px 10px; background-color: #10b981; color: white; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">IRIS AEROSPACE</div>
        <div class="subtitle">Boarding Pass · Interplanetary Travel</div>
    </div>

    <div style="text-align: right; margin-bottom: 30px;">
        <span class="status-badge">FINAL GO - APPROVED</span>
    </div>

    <div class="row">
        <div class="col">
            <div class="label">Passenger</div>
            <div class="value">{{ $res->passenger?->full_name }}</div>
            
            <div class="label">Origin</div>
            <div class="value">{{ $res->spaceFlight?->origin?->name ?? 'Earth Base' }}</div>
            
            <div class="label">Destination</div>
            <div class="value">{{ $res->spaceFlight?->destination?->name }}</div>
        </div>
        <div class="col">
            <div class="label">Locator ID</div>
            <div class="value" style="color: #10b981;">{{ strtoupper(substr($res->id_locator, 0, 8)) }}</div>
            
            <div class="label">Date & Time</div>
            <div class="value">{{ $res->spaceFlight?->departure_date?->format('d M Y - H:i') }}</div>
            
            <div class="label">Starship / Class</div>
            <div class="value">{{ $res->spaceFlight?->starship?->name }} / {{ strtoupper($res->seat_type) }}</div>
        </div>
    </div>
    
    <div style="clear: both;"></div>

    <div class="row" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
        <div class="col">
            <div class="label">Terrestrial Transfer</div>
            <div class="value" style="font-size: 14px;">
                @if($res->logistics?->terrestrialFlight)
                    {{ $res->logistics->terrestrialFlight->originLocation?->name }} → {{ $res->logistics->terrestrialFlight->destinationLocation?->name }}
                @else
                    Not Included
                @endif
            </div>
        </div>
        <div class="col">
            <div class="label">Accommodation</div>
            <div class="value" style="font-size: 14px;">
                {{ $res->logistics?->hotel?->name ?? 'Not Included' }}
            </div>
        </div>
    </div>
    
    <div style="clear: both;"></div>

    <div class="qr-placeholder">
        <div style="margin-bottom: 10px; font-weight: bold; color: #10b981;">SECURE BOARDING TOKEN</div>
        <div class="qr-text">{{ $qrData }}</div>
        <div style="margin-top: 10px; font-size: 10px; color: #999;">Scan at terminal gate</div>
    </div>

    <div class="footer">
        This document contains sensitive travel information. Keep it secure.<br>
        Iris Aerospace Corporation · {{ date('Y') }}
    </div>
</body>
</html>
