<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Rujukan (Referral Letter)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 3px 0 0 0;
            font-size: 11px;
            color: #666;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            margin-bottom: 15px;
            text-decoration: underline;
        }
        .meta-info {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-info td {
            padding: 2px 0;
        }
        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            border: 1px solid #ccc;
        }
        .patient-table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
        }
        .patient-table td.label {
            font-weight: bold;
            background-color: #f9f9f9;
            width: 25%;
        }
        .content {
            margin-top: 15px;
            text-align: justify;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            text-align: right;
            padding-right: 50px;
        }
        .signature-space {
            height: 70px;
        }
        .footer {
            position: absolute;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Klinik EMR Pintar Jabar</h2>
        <p>Jl. Raya Perekaman Medis No. 45, Bandung, Jawa Barat | Telp: (022) 123456 | Email: info@emrpintar.id</p>
    </div>

    <div class="title">SURAT RUJUKAN PASIEN OUTSOURCING / EKSTERNAL</div>
    <div style="text-align: center; margin-top: -10px; margin-bottom: 25px; font-weight: bold;">
        Nomor Surat: {{ $referral->no_surat }}
    </div>

    <table class="meta-info">
        <tr>
            <td style="width: 15%;">Kepada Yth.</td>
            <td style="width: 3%;">:</td>
            <td><strong>Sejawat Dokter di {{ $referral->faskes_tujuan }}</strong></td>
        </tr>
        <tr>
            <td>Di tempat</td>
            <td></td>
            <td></td>
        </tr>
    </table>

    <div class="content">
        Dengan hormat,<br>
        Bersama ini kami kirimkan pasien kami dengan keterangan klinis sebagai berikut:
    </div>

    <table class="patient-table">
        <tr>
            <td class="label">Nama Pasien</td>
            <td><strong>{{ $referral->pendaftaran->pasien->nama_pasien ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">No. Rekam Medis</td>
            <td>{{ $referral->pendaftaran->pasien->no_rekam_medis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK Pasien</td>
            <td>{{ $referral->pendaftaran->pasien->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td>{{ ($referral->pendaftaran->pasien->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lahir</td>
            <td>{{ isset($referral->pendaftaran->pasien->tanggal_lahir) ? $referral->pendaftaran->pasien->tanggal_lahir->format('d-m-Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Diagnosis Klinis</td>
            <td><strong>{{ $referral->diagnosa }}</strong></td>
        </tr>
    </table>

    <div class="content">
        <strong>Catatan / Terapi Awal:</strong><br>
        {{ $referral->catatan ?? 'Tidak ada catatan khusus.' }}
    </div>

    <div class="content">
        Mohon pemeriksaan penanganan lebih lanjut dan pengelolaan medis di fasilitas kesehatan Sejawat. Atas kerja sama rekan sejawat kami ucapkan terima kasih.
    </div>

    <table class="signatures">
        <tr>
            <td>
                <p>Bandung, {{ \Carbon\Carbon::parse($referral->tanggal_rujukan)->format('d-m-Y') }}</p>
                <p>Dokter Pengirim,</p>
                <div class="signature-space"></div>
                <p><strong>{{ $referral->dokter->nama_petugas ?? '-' }}</strong></p>
                <p style="font-size: 10px; color: #666; margin-top: -10px;">SIP: {{ $referral->dokter->nomor_sip ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Surat rujukan ini dikeluarkan secara digital melalui EMR Pintar Jabar. Dicetak pada {{ date('d-m-Y H:i:s') }}.
    </div>
</body>
</html>
