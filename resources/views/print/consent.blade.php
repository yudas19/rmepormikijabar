<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pernyataan Persetujuan (Consent)</title>
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
            margin-bottom: 25px;
            text-decoration: underline;
        }
        .section-title {
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 12px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        td {
            padding: 4px 0;
            vertical-align: top;
        }
        td.label {
            width: 30%;
        }
        td.colon {
            width: 3%;
        }
        .content {
            margin-bottom: 20px;
            text-align: justify;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signatures td {
            text-align: center;
            width: 50%;
        }
        .signature-space {
            height: 80px;
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
    <div class="header" style="position: relative;">
        @if (isset($profile) && $profile->logo_path)
            <img src="{{ public_path('storage/' . $profile->logo_path) }}" alt="Logo" style="position: absolute; left: 0; top: 0; height: 60px; width: 60px; object-fit: contain;">
        @endif
        <h2>{{ isset($profile) && $profile->nama_faskes ? $profile->nama_faskes : 'Klinik EMR Pintar Jabar' }}</h2>
        <p>{{ isset($profile) && $profile->alamat ? $profile->alamat : 'Jl. Raya Perekaman Medis No. 45, Bandung, Jawa Barat' }} | Telp: {{ isset($profile) && $profile->no_telp ? $profile->no_telp : '(022) 123456' }} | Email: {{ isset($profile) && $profile->email ? $profile->email : 'info@emrpintar.id' }}</p>
    </div>

    <div class="title">
        @if($consent->jenis_persetujuan === 'general_consent')
            Pernyataan Persetujuan Umum (General Consent)
        @else
            Persetujuan Tindakan Medis (Informed Consent)
        @endif
    </div>

    <div class="content">
        Yang bertanda tangan di bawah ini:
    </div>

    <table>
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td><strong>{{ $consent->nama_penanggung_jawab }}</strong></td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="colon">:</td>
            <td>{{ $consent->nik_penanggung_jawab ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Hubungan dengan Pasien</td>
            <td class="colon">:</td>
            <td>{{ ucfirst(str_replace('_', ' ', $consent->hubungan_penanggung_jawab)) }}</td>
        </tr>
    </table>

    <div class="content">
        Menyatakan dengan sadar dan tanpa paksaan untuk <strong>{{ $consent->pernyataan === 'setuju' ? 'MENYETUJUI' : 'MENOLAK' }}</strong> tindakan medis atau pengelolaan data medis terhadap pasien berikut:
    </div>

    <div class="section-title">Data Pasien</div>
    <table>
        <tr>
            <td class="label">No. Rekam Medis</td>
            <td class="colon">:</td>
            <td><strong>{{ $consent->pendaftaran->pasien->no_rekam_medis ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">Nama Pasien</td>
            <td class="colon">:</td>
            <td>{{ $consent->pendaftaran->pasien->nama_pasien ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK Pasien</td>
            <td class="colon">:</td>
            <td>{{ $consent->pendaftaran->pasien->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td>{{ ($consent->pendaftaran->pasien->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lahir</td>
            <td class="colon">:</td>
            <td>{{ isset($consent->pendaftaran->pasien->tanggal_lahir) ? $consent->pendaftaran->pasien->tanggal_lahir->format('d-m-Y') : '-' }}</td>
        </tr>
    </table>

    @if($consent->jenis_persetujuan === 'informed_consent_tindakan')
        <div class="section-title">Detail Tindakan Medis</div>
        <table>
            <tr>
                <td class="label">Tindakan Medis</td>
                <td class="colon">:</td>
                <td><strong>{{ $consent->nama_tindakan_medis ?? '-' }}</strong></td>
            </tr>
        </table>
    @endif

    <div class="content" style="margin-top: 20px;">
        @if($consent->jenis_persetujuan === 'general_consent')
            Persetujuan ini mencakup izin untuk pemeriksaan fisik awal, diagnosis rutin, pemeriksaan penunjang, perawatan umum medis, serta hak pelepasan informasi kesehatan sesuai dengan regulasi yang berlaku di Indonesia (SatuSehat Kemenkes).
        @else
            Saya membenarkan bahwa saya telah diberikan penjelasan lengkap mengenai diagnosis, tindakan medis yang diusulkan, manfaat, risiko, serta alternatif tindakan yang ada. Saya memahami penjelasan tersebut sepenuhnya.
        @endif
    </div>

    <table class="signatures">
        <tr>
            <td>
                <p>Petugas / Saksi Klinik</p>
                <div class="signature-space"></div>
                <p><strong>{{ $consent->petugas->nama_petugas ?? '-' }}</strong></p>
            </td>
            <td>
                <p>Yang Membuat Pernyataan</p>
                <div class="signature-space"></div>
                <p><strong>{{ $consent->nama_penanggung_jawab }}</strong></p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh EMR Pintar Jabar pada {{ date('d-m-Y H:i:s') }}. Lembar cetak ini sah dan diarsipkan secara digital.
    </div>
</body>
</html>
