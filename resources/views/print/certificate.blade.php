<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Keterangan Dokter</title>
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
        .content {
            margin-top: 15px;
            text-align: justify;
        }
        table.fields {
            width: 100%;
            margin: 15px 0;
        }
        table.fields td {
            padding: 3px 0;
        }
        table.fields td.label {
            width: 30%;
        }
        table.fields td.colon {
            width: 3%;
        }
        .detail-box {
            border: 1px solid #ccc;
            padding: 10px;
            background-color: #f9f9f9;
            margin: 15px 0;
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

    <div class="title">
        @if($certificate->jenis_surat === 'sehat')
            Surat Keterangan Sehat
        @elseif($certificate->jenis_surat === 'sakit')
            Surat Keterangan Sakit (Sick Leave)
        @else
            Surat Keterangan Bebas Narkoba
        @endif
    </div>
    
    <div style="text-align: center; margin-top: -10px; margin-bottom: 25px; font-weight: bold;">
        Nomor Surat: {{ $certificate->no_surat }}
    </div>

    <div class="content">
        Yang bertanda tangan di bawah ini menerangkan bahwa:
    </div>

    <table class="fields">
        <tr>
            <td class="label">Nama Lengkap</td>
            <td class="colon">:</td>
            <td><strong>{{ $certificate->pasien->nama_pasien ?? '-' }}</strong></td>
        </tr>
        <tr>
            <td class="label">No. Rekam Medis</td>
            <td class="colon">:</td>
            <td>{{ $certificate->pasien->no_rekam_medis ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">NIK Pasien</td>
            <td class="colon">:</td>
            <td>{{ $certificate->pasien->nik ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Jenis Kelamin</td>
            <td class="colon">:</td>
            <td>{{ ($certificate->pasien->jenis_kelamin ?? '') === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
        </tr>
        <tr>
            <td class="label">Tanggal Lahir</td>
            <td class="colon">:</td>
            <td>{{ isset($certificate->pasien->tanggal_lahir) ? $certificate->pasien->tanggal_lahir->format('d-m-Y') : '-' }}</td>
        </tr>
    </table>

    <div class="content">
        @if($certificate->jenis_surat === 'sehat')
            Telah diperiksa kesehatan badannya pada hari ini dan dinyatakan dalam keadaan <strong>SEHAT / BAIK</strong> dengan rincian fisik sebagai berikut:
            <div class="detail-box">
                <table>
                    <tr>
                        <td style="width: 40%;">Tinggi Badan</td>
                        <td style="width: 5%;">:</td>
                        <td>{{ $certificate->konten_surat['tinggi_badan'] ?? '-' }} cm</td>
                    </tr>
                    <tr>
                        <td>Berat Badan</td>
                        <td>:</td>
                        <td>{{ $certificate->konten_surat['berat_badan'] ?? '-' }} kg</td>
                    </tr>
                    <tr>
                        <td>Tekanan Darah</td>
                        <td>:</td>
                        <td>{{ $certificate->konten_surat['tekanan_darah'] ?? '-' }} mmHg</td>
                    </tr>
                    <tr>
                        <td>Buta Warna</td>
                        <td>:</td>
                        <td>{{ ($certificate->konten_surat['buta_warna'] ?? 'tidak') === 'ya' ? 'YA' : 'TIDAK' }}</td>
                    </tr>
                </table>
            </div>
            @if(!empty($certificate->konten_surat['catatan']))
                <p><strong>Catatan Dokter:</strong> {{ $certificate->konten_surat['catatan'] }}</p>
            @endif
        @elseif($certificate->jenis_surat === 'sakit')
            Dinyatakan sedang sakit dan memerlukan istirahat selama <strong>{{ \Carbon\Carbon::parse($certificate->konten_surat['tanggal_mulai'])->diffInDays(\Carbon\Carbon::parse($certificate->konten_surat['tanggal_selesai'])) + 1 }} hari</strong>, terhitung sejak tanggal:
            <div class="detail-box" style="text-align: center; font-size: 14px;">
                <strong>{{ \Carbon\Carbon::parse($certificate->konten_surat['tanggal_mulai'])->format('d-m-Y') }}</strong> s/d 
                <strong>{{ \Carbon\Carbon::parse($certificate->konten_surat['tanggal_selesai'])->format('d-m-Y') }}</strong>
            </div>
            @if(!empty($certificate->konten_surat['diagnosa']))
                <p><strong>Diagnosis:</strong> {{ $certificate->konten_surat['diagnosa'] }}</p>
            @endif
            Harap yang berkepentingan maklum adanya.
        @else
            Telah menjalani pemeriksaan skrining narkoba dan zat adiktif lainnya untuk keperluan <strong>{{ $certificate->konten_surat['keperluan'] ?? '-' }}</strong>. Berdasarkan pemeriksaan laboratorium urin hari ini, diperoleh hasil:
            <div class="detail-box">
                <strong>{{ $certificate->konten_surat['hasil_tes'] ?? 'Negatif untuk seluruh parameter uji.' }}</strong>
            </div>
            Demikian surat keterangan ini diberikan agar dapat digunakan sebagaimana mestinya.
        @endif
    </div>

    <table class="signatures">
        <tr>
            <td>
                <p>Bandung, {{ $certificate->created_at->format('d-m-Y') }}</p>
                <p>Dokter Pemeriksa,</p>
                <div style="display: inline-block; text-align: center; margin: 10px 0; padding-right: 20px;">
                    <div style="display: inline-block; padding: 4px; border: 1px solid #ddd; background: #fff; line-height: 0; text-align: center;">
                        {!! App\Services\QrCodeService::generateSvg(url('/verify-document/' . encrypt('cert-' . $certificate->id)), 70) !!}
                    </div>
                    <span style="font-size: 8px; color: #666; display: block; margin-top: 4px; line-height: 1.2; text-align: center;">Tanda Tangan Elektronik<br>Validitas dapat dicek dengan memindai QR Code ini.</span>
                </div>
                <p><strong>{{ $certificate->dokter->nama_petugas ?? '-' }}</strong></p>
                <p style="font-size: 10px; color: #666; margin-top: -10px;">SIP: {{ $certificate->dokter->nomor_sip ?? '-' }}</p>
            </td>
        </tr>
    </table>

    <div class="footer">
        Surat Keterangan ini dikeluarkan secara digital oleh EMR Pintar Jabar. Dicetak pada {{ date('d-m-Y H:i:s') }}.
    </div>
</body>
</html>
