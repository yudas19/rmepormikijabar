<x-layouts::app :title="__('SatuSehat Dashboard')">
    <div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ errorModalOpen: false, currentErrorLog: '' }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-zinc-900 dark:text-white tracking-tight flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 5h10a2 2 0 012 2v10a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2z"></path>
                        </svg>
                    </span>
                    SatuSehat Bridging Monitor
                </h1>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Validasi data dan buffer antrean kunjungan sebelum dikirim ke ekosistem SatuSehat Kemenkes (FHIR Standard).
                </p>
            </div>

            <!-- Date Range Filter & Batch Send -->
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" action="{{ route('admin.satusehat-dashboard') }}" class="flex items-center gap-2">
                    <label for="date-from" class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Mulai</label>
                    <input 
                        type="date" 
                        id="date-from" 
                        name="date_from" 
                        value="{{ $dateFrom }}" 
                        class="px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                    />
                    <label for="date-to" class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Sampai</label>
                    <input 
                        type="date" 
                        id="date-to" 
                        name="date_to" 
                        value="{{ $dateTo }}" 
                        class="px-3 py-2 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all shadow-sm"
                    />
                    <button 
                        type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] shadow-sm transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        Filter
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.satusehat-dashboard.dispatch-all-ready') }}">
                    @csrf
                    <input type="hidden" name="date_from" value="{{ $dateFrom }}" />
                    <input type="hidden" name="date_to" value="{{ $dateTo }}" />
                    @if($counts['ready'] > 0)
                        <button 
                            type="submit" 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 active:scale-[0.98] shadow-md shadow-amber-500/20 hover:shadow-lg hover:shadow-amber-500/30 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Kirim Semua yang Valid ({{ $counts['ready'] }})
                        </button>
                    @else
                        <button 
                            type="button" 
                            disabled 
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Kirim Semua yang Valid (0)
                        </button>
                    @endif
                </form>
            </div>
        </div>

        <!-- Alerts -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200 dark:border-emerald-800/40 text-emerald-800 dark:text-emerald-300 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-lg bg-rose-50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-800/40 text-rose-800 dark:text-rose-300 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-lg bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 text-amber-800 dark:text-amber-300 text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        <!-- Stats Overview -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            <!-- Total -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Total Kunjungan</span>
                <span class="text-2xl font-bold text-zinc-900 dark:text-white mt-2">{{ $counts['total'] }}</span>
            </div>

            <!-- Incomplete -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between border-l-4 border-l-zinc-400">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Belum Lengkap</span>
                <span class="text-2xl font-bold text-zinc-700 dark:text-zinc-300 mt-2">{{ $counts['incomplete'] }}</span>
            </div>

            <!-- Ready -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between border-l-4 border-l-amber-500">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Siap Kirim</span>
                <span class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-2">{{ $counts['ready'] }}</span>
            </div>

            <!-- Sent -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between border-l-4 border-l-emerald-500">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Terkirim</span>
                <span class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-2">{{ $counts['sent'] }}</span>
            </div>

            <!-- Failed -->
            <div class="p-4 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-all flex flex-col justify-between border-l-4 border-l-rose-500">
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Gagal Kirim</span>
                <span class="text-2xl font-bold text-rose-600 dark:text-rose-400 mt-2">{{ $counts['failed'] }}</span>
            </div>
        </div>

        <!-- Datatable Queue -->
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 text-xs font-bold uppercase tracking-wider text-zinc-500">
                            <th class="px-6 py-4">No. RM</th>
                            <th class="px-6 py-4">Nama Pasien</th>
                            <th class="px-6 py-4">Poliklinik / Dokter</th>
                            <th class="px-6 py-4">Status SatuSehat</th>
                            <th class="px-6 py-4">Keterangan / Missing Components</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                        @forelse($records as $index => $record)
                            @php
                                $valResult = $record->evaluateSatusehatValidation();
                            @endphp
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                <!-- No. RM -->
                                <td class="px-6 py-4 font-mono font-semibold text-zinc-900 dark:text-zinc-300">
                                    {{ $record->pasien?->no_rekam_medis ?? '-' }}
                                </td>

                                <!-- Nama Pasien -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-zinc-800 dark:text-zinc-100">
                                        {{ $record->pasien?->nama_pasien ?? '-' }}
                                    </div>
                                    <div class="text-xs text-zinc-400 font-mono mt-0.5">
                                        NIK: {{ $record->pasien?->nik ?? '-' }}
                                    </div>
                                </td>

                                <!-- Poliklinik / Dokter -->
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-200">
                                        {{ $record->poli?->nama_poli ?? '-' }}
                                    </div>
                                    <div class="text-xs text-zinc-500 mt-0.5">
                                        dr. {{ $record->dokter?->nama_petugas ?? '-' }}
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($record->satusehat_status === 'sent')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Sent
                                        </span>
                                    @elseif($record->satusehat_status === 'ready')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                                            Ready
                                        </span>
                                    @elseif($record->satusehat_status === 'failed')
                                        <button 
                                            type="button" 
                                            @click="currentErrorLog = `{{ $record->satusehat_error_log }}`; errorModalOpen = true"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-400 hover:bg-rose-200 transition duration-150 text-left cursor-pointer"
                                            title="Click to view error log"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Failed 
                                            <svg class="w-3.5 h-3.5 ml-0.5 text-rose-500 dark:text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-zinc-400"></span>
                                            Incomplete
                                        </span>
                                    @endif
                                </td>

                                <!-- Missing Components -->
                                <td class="px-6 py-4">
                                    @if($record->satusehat_status === 'sent')
                                        <div class="text-xs text-zinc-500 space-y-1 font-mono">
                                            <div>EncID: <span class="text-zinc-600 dark:text-zinc-400 font-semibold">{{ Str::after($record->satusehat_encounter_id, '/') }}</span></div>
                                            <div>CondID: <span class="text-zinc-600 dark:text-zinc-400 font-semibold">{{ Str::after($record->satusehat_condition_id, '/') }}</span></div>
                                        </div>
                                    @elseif(!empty($valResult['missing']))
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($valResult['missing'] as $missingItem)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-rose-50 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400 border border-rose-100 dark:border-rose-900/30">
                                                    {{ $missingItem }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Semua data valid
                                        </span>
                                    @endif
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <form method="POST" action="{{ route('admin.satusehat-dashboard.dispatch', ['record' => $record->id]) }}">
                                        @csrf
                                        @if(in_array($record->satusehat_status, ['ready', 'failed']))
                                            <button 
                                                type="submit" 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 hover:shadow-sm active:scale-[0.98] transition-all cursor-pointer"
                                            >
                                                Kirim SatuSehat
                                            </button>
                                        @else
                                            <button 
                                                type="button" 
                                                disabled 
                                                class="px-3 py-1.5 rounded-lg text-xs font-bold text-zinc-400 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 cursor-not-allowed"
                                                title="Selesaikan missing components sebelum mengirim data"
                                            >
                                                Kirim SatuSehat
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-zinc-500">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <svg class="w-8 h-8 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4.5m16 0h-1.5m-15 0H5"></path>
                                        </svg>
                                        <div class="text-sm font-semibold">Tidak Ada Rekam Medis Kunjungan</div>
                                        <div class="text-xs text-zinc-400">Tidak ada rekam medis terdaftar untuk periode {{ \Carbon\Carbon::parse($dateFrom)->format('d-m-Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d-m-Y') }}</div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Popover Error Log Modal -->
        <template x-if="errorModalOpen">
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" role="dialog" aria-modal="true">
                <!-- Backdrop -->
                <div 
                    class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
                    @click="errorModalOpen = false"
                ></div>

                <!-- Modal Content Container -->
                <div class="relative w-full max-w-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-xl overflow-hidden z-10 max-h-[85vh] flex flex-col">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2 text-rose-600 dark:text-rose-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <h3 class="font-extrabold text-zinc-900 dark:text-white text-lg">SatuSehat API Error Response Log</h3>
                        </div>
                        <button 
                            type="button" 
                            @click="errorModalOpen = false" 
                            class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-5 overflow-y-auto flex-1">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400 mb-4 leading-relaxed">
                            Di bawah ini adalah salinan tanggapan kesalahan (error response) API yang diterima dari server SatuSehat (Kemenkes) untuk analisis debugging lebih lanjut.
                        </p>
                        <div class="relative">
                            <pre class="bg-zinc-950 text-emerald-400 p-4 rounded-xl font-mono text-xs overflow-x-auto select-all max-h-[40vh] border border-zinc-800 shadow-inner" x-text="currentErrorLog || 'No error log response available.'"></pre>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                        <button 
                            type="button" 
                            @click="errorModalOpen = false" 
                            class="px-4 py-2 bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 text-sm font-semibold rounded-lg shadow-sm transition-all cursor-pointer"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-layouts::app>
