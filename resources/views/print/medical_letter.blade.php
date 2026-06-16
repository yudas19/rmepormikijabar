<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Keterangan Dokter - {{ $letter->nomor_surat }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 14px;
            color: #000;
            line-height: 1.6;
            margin: 0;
            padding: 30px;
            background-color: #fff;
        }
        
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f5;
            padding: 12px 24px;
            border-bottom: 1px solid #e4e4e7;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .btn-print {
            background-color: #0f172a;
            color: #fff;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            font-family: sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .btn-print:hover {
            background-color: #1e293b;
        }

        .back-link {
            color: #64748b;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 14px;
        }

        .back-link:hover {
            color: #334155;
            text-decoration: underline;
        }

        /* Letter Container */
        .letter-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        /* Kop Surat (Header) */
        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
        }

        .kop-logo {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-right: 20px;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-text h1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 5px 0;
            letter-spacing: 0.5px;
        }

        .kop-text p {
            margin: 2px 0;
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .double-line {
            border-top: 4px double #000;
            margin-bottom: 25px;
            height: 0;
        }

        /* Letter Info */
        .letter-title {
            text-align: center;
            margin-bottom: 25px;
        }

        .letter-title h2 {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 5px 0;
        }

        .letter-title p {
            margin: 0;
            font-size: 14px;
        }

        /* Content styling */
        .letter-content {
            margin-bottom: 30px;
            text-align: justify;
        }

        .patient-table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
        }

        .patient-table td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .patient-table td.label {
            width: 30%;
        }

        .patient-table td.colon {
            width: 3%;
        }

        .details-box {
            border: 1px solid #000;
            padding: 15px;
            margin: 20px 0;
            background-color: #fafafa;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 6px 4px;
        }

        /* Sign-off */
        .sign-off-container {
            margin-top: 40px;
            display: flex;
            justify-content: flex-end;
        }

        .sign-off {
            width: 250px;
            text-align: center;
        }

        .signature-space {
            height: 90px;
        }

        /* Print media rules */
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 12pt;
            }

            .no-print-bar {
                display: none;
            }

            .letter-container {
                max-width: 100%;
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <a href="javascript:window.close();" class="back-link">← Tutup Halaman</a>
        <button onclick="window.print();" class="btn-print">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="vertical-align: middle;">
                <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
            </svg>
            Cetak / Print
        </button>
    </div>

    <div class="letter-container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            @if ($profile && $profile->logo_path)
                <img class="kop-logo" src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo">
            @else
                <!-- Fallback logo placeholder if no logo -->
                <div class="kop-logo" style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; font-family: Arial;">[Logo Faskes]</div>
            @endif
            <div class="kop-text">
                <h1>{{ $profile->nama_faskes ?? 'Klinik Medis' }}</h1>
                <p>{{ $profile->alamat ?? '-' }}</p>
                <p>Telp: {{ $profile->no_telp ?? '-' }} | Email: {{ $profile->email ?? '-' }}</p>
            </div>
        </div>
        
        <div class="double-line"></div>

        <!-- Judul Surat -->
        <div class="letter-title">
            @if ($letter->jenis_surat === 'surat_sakit')
                <h2>SURAT KETERANGAN SAKIT</h2>
            @else
                <h2>SURAT KETERANGAN SEHAT</h2>
            @endif
            <p>Nomor: {{ $letter->nomor_surat }}</p>
        </div>

        <!-- Pembuka -->
        <div class="letter-content">
            <p>Yang bertanda tangan di bawah ini, Dokter Pemeriksa pada <strong>{{ $profile->nama_faskes ?? 'Klinik Medis' }}</strong> menerangkan dengan sebenarnya bahwa:</p>
            
            <table class="patient-table">
                <tr>
                    <td class="label">Nama Pasien</td>
                    <td class="colon">:</td>
                    <td><strong>{{ $letter->pasien->nama_pasien ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">No. Rekam Medis</td>
                    <td class="colon">:</td>
                    <td>{{ $letter->pasien->no_rekam_medis ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">NIK</td>
                    <td class="colon">:</td>
                    <td>{{ $letter->pasien->nik ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Jenis Kelamin</td>
                    <td class="colon">:</td>
                    <td>{{ ($letter->pasien->jenis_kelamin ?? '') === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                </tr>
                <tr>
                    <td class="label">Umur / Tanggal Lahir</td>
                    <td class="colon">:</td>
                    <td>
                        {{ $letter->pasien->tanggal_lahir ? $letter->pasien->tanggal_lahir->diffInYears(\Carbon\Carbon::now()) . ' Tahun' : '-' }}
                        ({{ $letter->pasien->tanggal_lahir ? $letter->pasien->tanggal_lahir->format('d-m-Y') : '-' }})
                    </td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="colon">:</td>
                    <td>{{ $letter->pasien->alamat ?? '-' }}</td>
                </tr>
            </table>

            @if ($letter->jenis_surat === 'surat_sakit')
                <!-- Surat Sakit Specific Content -->
                <p>Berdasarkan hasil pemeriksaan medis yang telah dilakukan, pasien tersebut dalam keadaan sakit dan memerlukan istirahat selama <strong>{{ $letter->meta_data['jumlah_hari'] ?? 0 }}</strong> hari, terhitung dari tanggal <strong>{{ \Carbon\Carbon::parse($letter->meta_data['dari_tanggal'])->translatedFormat('d F Y') }}</strong> sampai dengan tanggal <strong>{{ \Carbon\Carbon::parse($letter->meta_data['sampai_tanggal'])->translatedFormat('d F Y') }}</strong>.</p>
                @if (!empty($letter->meta_data['alasan']))
                    <p><strong>Alasan / Keterangan:</strong> {{ $letter->meta_data['alasan'] }}</p>
                @endif
                <p>Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.</p>
            @else
                <!-- Surat Sehat Specific Content -->
                <p>Berdasarkan hasil pemeriksaan fisik dan medis yang telah dilakukan pada hari ini, pasien tersebut dinyatakan dalam keadaan <strong>{{ $letter->meta_data['kesimpulan'] ?? 'SEHAT' }}</strong>. Rincian pemeriksaan fisik adalah sebagai berikut:</p>
                
                <div class="details-box">
                    <table class="details-table">
                        <tr>
                            <td style="width: 35%;">Tinggi Badan</td>
                            <td style="width: 5%;">:</td>
                            <td>{{ $letter->meta_data['tinggi_badan'] ?? '-' }} cm</td>
                        </tr>
                        <tr>
                            <td>Berat Badan</td>
                            <td>:</td>
                            <td>{{ $letter->meta_data['berat_badan'] ?? '-' }} kg</td>
                        </tr>
                        <tr>
                            <td>Golongan Darah</td>
                            <td>:</td>
                            <td>{{ $letter->meta_data['golongan_darah'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Buta Warna</td>
                            <td>:</td>
                            <td>{{ $letter->meta_data['buta_warna'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tekanan Darah</td>
                            <td>:</td>
                            <td>{{ $letter->meta_data['tekanan_darah'] ?? '-' }} mmHg</td>
                        </tr>
                    </table>
                </div>
                <p>Demikian surat keterangan sehat ini dibuat untuk dapat dipergunakan sesuai keperluannya.</p>
            @endif
        </div>

        <!-- Penutup (Sign-off) -->
        <div class="sign-off-container">
            <div class="sign-off">
                <p>{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                <p>Dokter Pemeriksa,</p>
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 10px 0;">
                    <div style="display: inline-block; padding: 4px; border: 1px solid #ddd; background: #fff; line-height: 0;">
                        {!! App\Services\QrCodeService::generateSvg(url('/verify-document/' . encrypt('letter-' . $letter->id)), 70) !!}
                    </div>
                    <span style="font-size: 8px; color: #666; display: block; margin-top: 4px; line-height: 1.2;">Tanda Tangan Elektronik<br>Validitas dapat dicek dengan memindai QR Code ini.</span>
                </div>
                <p><strong>dr. {{ $letter->dokter->nama_petugas ?? '-' }}</strong></p>
                <p style="font-size: 11px; margin-top: -5px;">SIP: {{ $letter->dokter->nomor_sip ?? '-' }}</p>
                @if (!empty($letter->dokter->ihs_number_practitioner))
                    <p style="font-size: 10px; margin-top: -10px; color: #555;">IHS: {{ $letter->dokter->ihs_number_practitioner }}</p>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Trigger print dialog immediately on load if not in history
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
