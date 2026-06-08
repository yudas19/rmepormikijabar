<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tiket Antrean - {{ $record->nomor_antrean }}</title>
    <style>
        @page {
            margin: 0;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 58mm; /* Standard 58mm thermal printer width */
            margin: 0;
            padding: 5px 10px;
            font-size: 11px;
            color: #000;
            background: #fff;
            text-align: center;
        }
        .clinic-name {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        .divider {
            border-bottom: 1px dashed #000;
            margin: 5px 0;
        }
        .title {
            font-size: 11px;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .queue-number {
            font-size: 28px;
            font-weight: bold;
            margin: 10px 0;
            display: block;
        }
        .patient-info {
            text-align: left;
            margin-top: 5px;
            line-height: 1.3;
        }
        .patient-info div {
            margin-bottom: 2px;
        }
        .footer {
            margin-top: 10px;
            font-size: 9px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="clinic-name">Klinik RME Pormiki</div>
    <div style="font-size: 9px;">Sistem Rekam Medis Elektronik</div>
    <div class="divider"></div>
    
    <div class="title">TIKET ANTREAN POLIKLINIK</div>
    <div style="font-size: 12px; font-weight: bold; text-transform: uppercase; background: #000; color: #fff; padding: 2px 0;">
        Poli: {{ $record->poliklinik_type === 'umum' ? 'Poli Umum' : ($record->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'Klinik KIA') }}
    </div>

    <span class="queue-number">{{ $record->nomor_antrean }}</span>

    <div class="divider"></div>

    <div class="patient-info">
        <div><strong>No. RM   :</strong> {{ $record->pasien->no_rekam_medis }}</div>
        <div><strong>Nama     :</strong> {{ $record->pasien->nama_pasien }}</div>
        <div><strong>Tgl Lahir:</strong> {{ $record->pasien->tanggal_lahir ? $record->pasien->tanggal_lahir->format('d-m-Y') : '-' }}</div>
        <div><strong>Waktu    :</strong> {{ $record->created_at->format('d-m-Y H:i:s') }}</div>
    </div>

    <div class="divider"></div>
    <div class="footer">
        Harap menunggu antrean Anda.<br>Terima kasih atas kunjungan Anda.
    </div>

    <script>
        window.onload = function() {
            window.print();
            // Automatically close the window after printing or cancel (optional)
            setTimeout(function() {
                window.close();
            }, 1000);
        };
    </script>
</body>
</html>
