<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Pasaporte Estelar IRIS</title>
    <style>
        @page { 
            margin: 0; 
            size: A4 portrait;
        }
        body {
            font-family: 'Helvetica', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #020617;
            color: #ffffff;
            width: 100%;
            height: 100%;
        }
        /* Centrado absoluto para DomPDF usando tablas */
        .wrapper {
            width: 100%;
            height: 100%;
            vertical-align: middle;
            text-align: center;
        }
        .container {
            padding: 0;
            margin: 0;
        }
        .passport-card {
            width: 580px; /* Un poco más estrecho para asegurar márgenes */
            margin: 0 auto;
            background: #0f172a;
            border: 1px solid #1e293b;
            border-radius: 20px;
            padding: 35px;
            position: relative;
            text-align: left; /* Reset text align for content */
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #0ea5e9;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .agency-name {
            font-size: 10px;
            letter-spacing: 4px;
            color: #0ea5e9;
            text-transform: uppercase;
            font-weight: bold;
        }
        .doc-title {
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #f8fafc;
        }
        .content-table {
            width: 100%;
        }
        .photo-cell {
            width: 160px;
            vertical-align: top;
        }
        .photo-box {
            width: 150px;
            height: 190px;
            background-color: #000;
            border: 2px solid #1e293b;
            border-radius: 10px;
            overflow: hidden;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
        }
        .info-cell {
            padding-left: 30px;
            vertical-align: top;
        }
        .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 2px;
            display: block;
        }
        .value {
            font-size: 15px;
            font-weight: bold;
            color: #f1f5f9;
            margin-bottom: 15px;
            display: block;
        }
        .data-table {
            width: 100%;
        }
        .footer-table {
            width: 100%;
            margin-top: 40px;
            border-top: 1px solid #1e293b;
            padding-top: 20px;
        }
        .qr-box {
            width: 80px;
            height: 80px;
            background-color: #ffffff;
            padding: 5px;
            text-align: center;
        }
        .qr-box img {
            width: 70px;
            height: 70px;
        }
        .id-text {
            font-family: 'Courier', monospace;
            font-size: 11px;
            color: #0ea5e9;
            margin-top: 5px;
        }
        .stamp {
            border: 3px solid rgba(16, 185, 129, 0.3);
            color: rgba(16, 185, 129, 0.4);
            padding: 8px 15px;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            position: absolute;
            top: 50%;
            right: 120px;
            transform: rotate(-15deg);
        }
    </style>
</head>
<body>
    <table class="wrapper" cellpadding="0" cellspacing="0">
        <tr>
            <td>
                <div class="passport-card">
                    <table class="header-table">
                        <tr>
                            <td>
                                <div class="agency-name">Iris Aerospace Agency</div>
                                <div class="doc-title">Pasaporte Estelar</div>
                            </td>
                            <td style="text-align: right;">
                                <div style="color: #64748b; font-size: 8px;">ID: IRIS-{{ strtoupper(substr(md5($passenger->id), 0, 8)) }}</div>
                            </td>
                        </tr>
                    </table>

                    <table class="content-table">
                        <tr>
                            <td class="photo-cell">
                                <div class="photo-box">
                                    @if($photoPath && file_exists(storage_path('app/public/' . $photoPath)))
                                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(storage_path('app/public/' . $photoPath))) }}">
                                    @else
                                        <div style="padding-top: 80px; text-align: center; font-size: 8px; color: #334155;">FOTOGRAFÍA BIOMÉTRICA</div>
                                    @endif
                                </div>
                            </td>
                            <td class="info-cell">
                                <table class="data-table">
                                    <tr>
                                        <td colspan="2">
                                            <span class="label">Titular / Passenger</span>
                                            <span class="value">{{ strtoupper($passenger->full_name) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <span class="label">Nacionalidad</span>
                                            <span class="value">{{ $nacionalidad }}</span>
                                        </td>
                                        <td width="50%">
                                            <span class="label">Fecha Nacimiento</span>
                                            <span class="value">{{ $passenger->birth_date?->format('d / M / Y') }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="label">Origen Terrestre</span>
                                            <span class="value">Planeta Tierra</span>
                                        </td>
                                        <td>
                                            <span class="label">Vencimiento Estelar</span>
                                            <span class="value">{{ now()->addYears(10)->format('d / M / Y') }}</span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                    <div class="stamp">Validado</div>

                    <table class="footer-table">
                        <tr>
                            <td>
                                <span class="label">Autoridad Emisora</span>
                                <div style="font-size: 10px; color: #f1f5f9;">AGENCIA ESPACIAL IRIS - SECTOR CONTROL</div>
                            </td>
                            <td style="text-align: right; width: 100px;">
                                <div class="qr-box">
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=IRIS-PASS-{{ $passenger->id }}">
                                </div>
                                <div class="id-text">IRIS-{{ strtoupper(substr(md5($passenger->id), 0, 6)) }}</div>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>