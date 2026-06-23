<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resep Obat - RX-{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12px;
            color: #000;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        
        .no-print-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f4f5;
            padding: 10px 20px;
            border-bottom: 1px solid #e4e4e7;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .btn-print {
            background-color: #0f172a;
            color: #fff;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            font-family: sans-serif;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link {
            color: #64748b;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 12px;
        }

        .back-link:hover {
            color: #334155;
            text-decoration: underline;
        }

        .letter-container {
            max-width: 650px;
            margin: 0 auto;
            background: #fff;
            padding: 10px;
        }

        /* Kop Surat (Header) */
        .kop-surat {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        .kop-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-right: 15px;
        }

        .kop-text {
            flex-grow: 1;
            text-align: center;
        }

        .kop-text h1 {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 20px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0 0 3px 0;
        }

        .kop-text p {
            margin: 1px 0;
            font-size: 10px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .double-line {
            border-top: 3px double #000;
            margin-bottom: 15px;
            height: 0;
        }

        .letter-title {
            text-align: center;
            margin-bottom: 15px;
        }

        .letter-title h2 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin: 0 0 3px 0;
        }

        .letter-title p {
            margin: 0;
            font-size: 11px;
            font-family: monospace;
        }

        .patient-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .patient-info td {
            padding: 3px 0;
            font-size: 11px;
            vertical-align: top;
        }

        .prescription-title-section {
            font-size: 22px;
            font-weight: bold;
            font-family: 'Times New Roman', serif;
            margin: 15px 0 5px 0;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .prescription-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .prescription-table th {
            border-bottom: 2px solid #000;
            text-align: left;
            padding: 5px;
            font-size: 11px;
            font-weight: bold;
        }

        .prescription-table td {
            border-bottom: 1px solid #eee;
            padding: 8px 5px;
            font-size: 12px;
            vertical-align: top;
        }

        .sign-off-container {
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }

        .sign-off {
            width: 200px;
            text-align: center;
        }

        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 11pt;
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
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
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
                <div class="kop-logo" style="width: 60px; height: 60px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 8px; font-family: Arial;">[Logo Faskes]</div>
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
            <h2>SALINAN RESEP (APOGRAPH)</h2>
            <p>No. Resep: RX-{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>

        <!-- Info Pasien -->
        <table class="patient-info">
            <tr>
                <td style="width: 15%; font-weight: bold;">Dokter Penulis</td>
                <td style="width: 2%;">:</td>
                <td style="width: 43%;">dr. {{ $prescription->medicalRecord->dokter->nama_petugas ?? '-' }}</td>
                
                <td style="width: 15%; font-weight: bold;">Tanggal Resep</td>
                <td style="width: 2%;">:</td>
                <td style="width: 23%;">{{ $prescription->created_at->format('d-m-Y H:i') }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Nama Pasien</td>
                <td>:</td>
                <td><strong>{{ $prescription->medicalRecord->pasien->nama_pasien ?? '-' }}</strong></td>
                
                <td style="font-weight: bold;">No. RM</td>
                <td>:</td>
                <td style="font-family: monospace;">{{ $prescription->medicalRecord->pasien->no_rekam_medis ?? '-' }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Umur / JK</td>
                <td>:</td>
                <td>
                    {{ $prescription->medicalRecord->pasien->tanggal_lahir ? number_format((float) $prescription->medicalRecord->pasien->tanggal_lahir->diffInYears(now()), 1) . ' Tahun' : '-' }}
                    / {{ ($prescription->medicalRecord->pasien->jenis_kelamin ?? '') === 'L' ? 'Laki-Laki' : 'Perempuan' }}
                </td>
                
                <td style="font-weight: bold;">Status Resep</td>
                <td>:</td>
                <td>{{ $prescription->dispensing_status_label }}</td>
            </tr>
        </table>

        <!-- R/ Prescription Section -->
        <div class="prescription-title-section">R/</div>

        <table class="prescription-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Nama Obat / Deskripsi Racikan</th>
                    <th style="width: 20%;">Jumlah</th>
                    <th style="width: 30%;">Aturan Pakai (Signa)</th>
                </tr>
            </thead>
            <tbody>
                @if ($prescription->type === 'racikan')
                    <tr>
                        <td>
                            <strong>{{ $prescription->nama_racikan }} (Racikan)</strong>
                            <div style="font-size: 10px; color: #555; margin-top: 4px; padding-left: 10px;">
                                Metode: {{ $prescription->metodeRacik->nama_metode_racik ?? '-' }} ({{ $prescription->jumlah_kemasan }} Kemasan)
                                <ul style="margin: 2px 0 0 0; padding-left: 15px;">
                                    @foreach ($prescription->items as $item)
                                        <li>
                                            {{ $item->requestedObat->nama_obat ?? '-' }} 
                                            @if($item->dispensedObat && $item->dispensedObat->id !== $item->requestedObat->id)
                                                (Diserahkan: {{ $item->dispensedObat->nama_obat }})
                                            @endif
                                            - {{ $item->dispensed_qty ?? $item->requested_qty }} {{ $item->satuan }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </td>
                        <td>{{ $prescription->jumlah_kemasan }} Kemasan</td>
                        <td style="font-family: monospace;">{{ $prescription->aturan_pakai }}</td>
                    </tr>
                @else
                    @foreach ($prescription->items as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->requestedObat->nama_obat ?? '-' }}</strong>
                                @if($item->dispensedObat && $item->dispensedObat->id !== $item->requestedObat->id)
                                    <div style="font-size: 10px; color: #555;">Diserahkan: {{ $item->dispensedObat->nama_obat }} (Substitusi)</div>
                                @endif
                            </td>
                            <td>{{ $item->dispensed_qty ?? $item->requested_qty }} {{ $item->satuan }}</td>
                            <td style="font-family: monospace;">{{ $item->dispensed_signa ?? $item->requested_signa ?? $prescription->aturan_pakai }}</td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>

        @if($prescription->catatan)
            <div style="margin-top: 10px; font-size: 11px; font-style: italic; background-color: #fafafa; padding: 5px; border-left: 2px solid #ccc;">
                <strong>Catatan Dokter:</strong> {{ $prescription->catatan }}
            </div>
        @endif

        <!-- Penutup (Sign-off) & TTE QR Code -->
        <div class="sign-off-container">
            <div class="sign-off">
                <p>Bandung, {{ $prescription->created_at->format('d-m-Y') }}</p>
                <p>Dokter Pemeriksa,</p>
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; margin: 8px 0;">
                    <div style="display: inline-block; padding: 4px; border: 1px solid #ddd; background: #fff; line-height: 0;">
                        {!! App\Services\QrCodeService::generateSvg(url('/verify-document/' . encrypt('prescription-' . $prescription->id)), 65) !!}
                    </div>
                    <span style="font-size: 8px; color: #666; display: block; margin-top: 3px; line-height: 1.2;">Tanda Tangan Elektronik<br>Validitas dapat dicek dengan memindai QR Code ini.</span>
                </div>
                <p><strong>dr. {{ $prescription->medicalRecord->dokter->nama_petugas ?? '-' }}</strong></p>
                <p style="font-size: 10px; margin-top: -5px; color: #666;">SIP: {{ $prescription->medicalRecord->dokter->nomor_sip ?? '-' }}</p>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
