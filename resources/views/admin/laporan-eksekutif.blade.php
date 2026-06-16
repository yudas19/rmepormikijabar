<x-layouts::app :title="'Laporan Eksekutif'">
    <!-- Load Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Print styling using @media print */
        @media print {
            /* Hide the entire standard layout, sidebar, filters, and print action bar */
            body * {
                visibility: hidden;
            }

            /* Make only the print area visible */
            #print-area, #print-area * {
                visibility: visible;
            }

            /* Position the print area perfectly at the top-left */
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
                background-color: #white;
                color: #000;
            }

            /* Prevent layout boxes from breaking awkwardly */
            .no-break {
                page-break-inside: avoid;
            }

            /* Ensure double-underlines render beautifully on paper */
            .double-line {
                border-top: 4px double #000 !important;
                margin-top: 8px !important;
                margin-bottom: 20px !important;
                height: 0;
            }
        }
    </style>

    <div class="space-y-6 p-6 font-jakarta bg-zinc-50/50 dark:bg-zinc-950 min-h-screen">
        
        {{-- HEADER & ACTIONS BAR (Hidden when printing) --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 shadow-xs">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight text-zinc-950 dark:text-white">LAPORAN EKSEKUTIF DAN ANALISIS</flux:heading>
                    <flux:badge color="indigo" size="md">Admin Panel</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium text-zinc-500 dark:text-zinc-400">Ringkasan keuangan klinik dan tren diagnosis penyakit ICD-10.</flux:subheading>
            </div>
            
            <div class="flex gap-2 w-full md:w-auto">
                <flux:button variant="primary" icon="printer" onclick="window.print()" class="w-full md:w-auto">
                    Cetak Laporan / Export PDF
                </flux:button>
            </div>
        </div>

        {{-- DATE FILTERS CARD (Hidden when printing) --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl p-6 shadow-xs">
            <form method="GET" action="{{ route('admin.laporan-eksekutif') }}" class="flex flex-col sm:flex-row items-end gap-4">
                <div class="flex-grow grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <flux:input type="date" name="start_date" value="{{ $startDate }}" label="Mulai Tanggal" required size="sm" />
                    <flux:input type="date" name="end_date" value="{{ $endDate }}" label="Selesai Tanggal" required size="sm" />
                </div>
                <div class="flex gap-2 w-full sm:w-auto">
                    <flux:button type="submit" variant="filled" size="sm" class="w-full sm:w-auto">Terapkan Filter</flux:button>
                    @if(request('start_date') || request('end_date'))
                        <a href="{{ route('admin.laporan-eksekutif') }}" class="w-full sm:w-auto">
                            <flux:button type="button" variant="ghost" size="sm" class="w-full">Reset</flux:button>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- ROW 1: FINANCIAL CARD SUMMARY METRICS --}}
        <div class="grid gap-6 md:grid-cols-3">
            {{-- Card A: Total Revenue --}}
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-emerald-500/10"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">Paid Invoices</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">Total Pendapatan (Revenue)</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Total pembayaran terbayar (Paid)</p>
                </div>
            </div>

            {{-- Card B: Total Cash (Tunai) --}}
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-amber-500/10"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-400">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5M3.75 20.25h16.5M3.75 12h16.5m-15 0h15" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-amber-500/10 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">Cash</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">Total Tunai (Cash)</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">
                        Rp {{ number_format($totalCash, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Pembayaran metode kasir tunai</p>
                </div>
            </div>

            {{-- Card C: Total Non-Cash (QRIS & Transfer) --}}
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-indigo-500/10"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-400">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-indigo-500/10 px-2.5 py-0.5 text-xs font-semibold text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400">Digital</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">Total Non-Tunai (QRIS/Transfer)</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">
                        Rp {{ number_format($totalNonCash, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Gabungan QRIS dan Transfer Bank</p>
                </div>
            </div>
        </div>

        {{-- ROW 2: TWO-COLUMN BREAKDOWN LAYOUT --}}
        <div class="grid gap-6 lg:grid-cols-2">
            {{-- Left Column: Revenue Breakdown Table --}}
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-2 mb-6 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <svg class="h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v5.25c0 .621-.504 1.125-1.125 1.125h-2.25A1.125 1.125 0 013 18.375v-5.25zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-9.75zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v14.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-white">Rincian Pendapatan per Metode Pembayaran</flux:heading>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-zinc-50 dark:bg-zinc-950/40 border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase">Metode</th>
                                <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase">Transaksi</th>
                                <th class="px-4 py-3 text-right font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase">Sub-Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @forelse($paymentBreakdown as $pay)
                            <tr>
                                <td class="px-4 py-3 font-semibold text-zinc-900 dark:text-white capitalize">
                                    {{ $pay->payment_method }}
                                </td>
                                <td class="px-4 py-3 text-center font-mono text-zinc-600 dark:text-zinc-300">
                                    {{ $pay->transaction_count }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-zinc-900 dark:text-white">
                                    Rp {{ number_format($pay->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-4 py-12 text-center text-zinc-400 italic">
                                    Belum ada data transaksi keuangan pada periode ini.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Right Column: Top 10 Diseases / ICD-10 Table --}}
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-xs dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-2 mb-6 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <flux:heading size="lg" class="font-bold text-zinc-900 dark:text-white">Top 10 Tren Kasus Penyakit (ICD-10)</flux:heading>
                </div>

                <div class="space-y-4">
                    @forelse($topDiseases as $index => $disease)
                    <div class="flex items-center justify-between gap-4 p-3 rounded-xl hover:bg-zinc-50 dark:hover:bg-zinc-800/40 transition-colors">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-zinc-100 text-xs font-bold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">
                                {{ $index + 1 }}
                            </span>
                            <flux:badge color="indigo" class="font-mono text-xs">{{ $disease->icd10_code }}</flux:badge>
                            <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200 truncate">
                                {{ $disease->icd10_name }}
                            </span>
                        </div>
                        <div class="font-mono text-sm font-bold text-zinc-900 dark:text-white shrink-0">
                            {{ $disease->cases_count }} kasus
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12 text-zinc-400 italic text-sm">
                        Belum ada tren diagnosis penyakit pada periode ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ═════════════════════════════════════════════════════════ --}}
        {{-- PRINT CONTAINER (Only visible when browser printing, Ctrl+P) --}}
        {{-- ═════════════════════════════════════════════════════════ --}}
        <div id="print-area" class="hidden">
            <!-- Kop Surat -->
            <div style="display: flex; align-items: center; justify-content: flex-start; margin-bottom: 15px;">
                @if ($profile && $profile->logo_path)
                    <img src="{{ asset('storage/' . $profile->logo_path) }}" alt="Logo" style="width: 80px; height: 80px; object-fit: contain; margin-right: 20px;">
                @else
                    <div style="width: 80px; height: 80px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; font-size: 10px; margin-right: 20px;">[Logo]</div>
                @endif
                <div style="flex-grow: 1; text-align: center; font-family: Arial, sans-serif;">
                    <h1 style="font-size: 20px; font-weight: bold; margin: 0; text-transform: uppercase;">{{ $profile->nama_faskes ?? 'Klinik Medis' }}</h1>
                    <p style="font-size: 12px; margin: 4px 0 0 0;">{{ $profile->alamat ?? '-' }}</p>
                    <p style="font-size: 11px; margin: 2px 0 0 0;">Telp: {{ $profile->no_telp ?? '-' }} | Email: {{ $profile->email ?? '-' }}</p>
                </div>
            </div>

            <div class="double-line" style="border-top: 4px double #000; margin: 10px 0 25px 0; height: 0;"></div>

            <!-- Title -->
            <div style="text-align: center; margin-bottom: 30px; font-family: 'Times New Roman', Times, serif;">
                <h2 style="font-size: 18px; font-weight: bold; text-decoration: underline; margin: 0; text-transform: uppercase;">LAPORAN EKSEKUTIF KLINIK</h2>
                <p style="font-size: 12px; margin: 5px 0 0 0;">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</p>
            </div>

            <!-- Summary Table -->
            <div class="no-break" style="margin-bottom: 30px; font-family: Arial, sans-serif;">
                <h3 style="font-size: 14px; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 15px;">I. RINGKASAN KEUANGAN (REVENUE SUMMARY)</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td style="padding: 8px 4px; font-weight: bold;">TOTAL PENDAPATAN (REVENUE)</td>
                        <td style="padding: 8px 4px; text-align: right; font-weight: bold; font-family: monospace;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td style="padding: 8px 4px;">Metode Tunai (Cash)</td>
                        <td style="padding: 8px 4px; text-align: right; font-family: monospace;">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #ccc;">
                        <td style="padding: 8px 4px;">Metode Non-Tunai (QRIS / Transfer)</td>
                        <td style="padding: 8px 4px; text-align: right; font-family: monospace;">Rp {{ number_format($totalNonCash, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <!-- Breakdown Table -->
            <div class="no-break" style="margin-bottom: 30px; font-family: Arial, sans-serif;">
                <h3 style="font-size: 14px; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 15px;">II. KEUANGAN PER METODE PEMBAYARAN</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #000; background-color: #f5f5f5;">
                            <th style="padding: 8px 4px; text-align: left;">Metode Pembayaran</th>
                            <th style="padding: 8px 4px; text-align: center;">Jumlah Transaksi</th>
                            <th style="padding: 8px 4px; text-align: right;">Total Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentBreakdown as $pay)
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 8px 4px; text-transform: capitalize; font-weight: bold;">{{ $pay->payment_method }}</td>
                            <td style="padding: 8px 4px; text-align: center;">{{ $pay->transaction_count }}</td>
                            <td style="padding: 8px 4px; text-align: right; font-family: monospace; font-weight: bold;">Rp {{ number_format($pay->total_amount, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" style="padding: 8px 4px; text-align: center;">Tidak ada rincian data keuangan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Disease Table -->
            <div class="no-break" style="margin-bottom: 30px; font-family: Arial, sans-serif;">
                <h3 style="font-size: 14px; font-weight: bold; border-bottom: 1px solid #000; padding-bottom: 5px; margin-bottom: 15px;">III. TOP 10 TREN KASUS PENYAKIT (ICD-10)</h3>
                <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                    <thead>
                        <tr style="border-bottom: 2px solid #000; background-color: #f5f5f5;">
                            <th style="padding: 8px 4px; text-align: left; width: 10%;">No.</th>
                            <th style="padding: 8px 4px; text-align: left; width: 20%;">Kode ICD-10</th>
                            <th style="padding: 8px 4px; text-align: left;">Nama Penyakit (Diagnosis)</th>
                            <th style="padding: 8px 4px; text-align: right; width: 20%;">Total Kasus</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDiseases as $index => $disease)
                        <tr style="border-bottom: 1px solid #ccc;">
                            <td style="padding: 8px 4px;">{{ $index + 1 }}</td>
                            <td style="padding: 8px 4px; font-family: monospace; font-weight: bold;">{{ $disease->icd10_code }}</td>
                            <td style="padding: 8px 4px;">{{ $disease->icd10_name }}</td>
                            <td style="padding: 8px 4px; text-align: right; font-weight: bold;">{{ $disease->cases_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="padding: 8px 4px; text-align: center;">Tidak ada data tren kasus penyakit.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Signature footer -->
            <div style="margin-top: 50px; display: flex; justify-content: flex-end; font-family: Arial, sans-serif; font-size: 12px;">
                <div style="width: 250px; text-align: center;">
                    <p>Dibuat Pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
                    <p>Mengetahui,</p>
                    <p style="margin-top: 5px; font-weight: bold;">Administrator Klinik</p>
                    <div style="height: 70px;"></div>
                    <hr style="border: 0.5px solid #000; width: 80%; margin: 0 auto;" />
                    <p style="margin-top: 5px; font-size: 10px; color: #555;">NIP / Kode Petugas</p>
                </div>
            </div>
        </div>

    </div>
</x-layouts::app>
