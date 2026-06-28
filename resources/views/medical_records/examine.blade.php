<x-layouts::app :title="'Clinical Workspace - ' . $patient->nama_pasien">
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 items-start p-6">

        <!-- Left Column: Form & Workspace (70%) -->
        <div class="lg:col-span-7 space-y-6">
            @if ($poliklinik === 'gigi')
                @livewire('⚡medical-record.poli-gigi', ['record' => $record])
            @elseif ($poliklinik === 'kia')
                @livewire('⚡medical-record.poli-kia', ['record' => $record])
            @else
                @livewire('⚡medical-record.poli-umum', ['record' => $record])
            @endif
        </div>

        <!-- Right Column: Sidebar Patient Info & Timeline (30%) -->
        <div class="lg:col-span-3 lg:sticky lg:top-6 space-y-6">
            <!-- Patient Info Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm">
                <flux:heading size="lg" class="mb-4">Informasi Pasien</flux:heading>

                <div class="space-y-4 text-sm">
                    <div>
                        <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Nama Pasien</div>
                        <div class="font-bold text-base text-zinc-900 dark:text-white mt-0.5">{{ $patient->nama_pasien }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">No. Rekam Medis</div>
                            <div class="font-mono font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $patient->no_rekam_medis }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">No. BPJS</div>
                            <div class="font-mono mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $patient->no_bpjs ?? '-' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">NIK</div>
                        <div class="font-mono mt-0.5 text-zinc-800 dark:text-zinc-200">{{ $patient->nik }}</div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Jenis Kelamin</div>
                            <div class="mt-0.5">{{ $patient->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Umur / Tgl Lahir</div>
                            <div class="mt-0.5">
                                {{ $patient->tanggal_lahir ? number_format((float) $patient->tanggal_lahir->diffInYears(now()), 1) . ' Thn' : '-' }}
                                <span class="text-xs text-zinc-400">({{ $patient->tanggal_lahir ? $patient->tanggal_lahir->format('d-m-Y') : '-' }})</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">Alamat</div>
                        <div class="text-zinc-700 dark:text-zinc-300 leading-relaxed mt-0.5">{{ $patient->alamat }}</div>
                    </div>

                    <div>
                        <div class="text-xs text-zinc-500 font-semibold uppercase tracking-wider">No. WhatsApp</div>
                        <div class="mt-0.5 font-mono text-zinc-800 dark:text-zinc-200">{{ $patient->no_whatsapp ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Laboratory Results Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:heading size="lg">Hasil Laboratorium</flux:heading>
                </div>

                @php
                    $activeLabOrder = $labOrders->first();
                @endphp

                @if ($activeLabOrder)
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-zinc-50 dark:bg-zinc-950 p-2.5 rounded-lg border border-zinc-200/50 dark:border-zinc-850/40">
                            <span class="text-xs font-semibold text-zinc-600 dark:text-zinc-400">Status Order:</span>
                            @php
                                $statusColor = match($activeLabOrder->status) {
                                    'pending' => 'amber',
                                    'processing' => 'blue',
                                    'completed' => 'green',
                                    default => 'zinc'
                                };
                            @endphp
                            <flux:badge color="{{ $statusColor }}" size="sm" class="font-bold uppercase">{{ $activeLabOrder->status_label }}</flux:badge>
                        </div>

                        <div class="divide-y divide-zinc-100 dark:divide-zinc-800/80">
                            @foreach ($activeLabOrder->results as $result)
                                <div class="py-3 first:pt-0 last:pb-0">
                                    <div class="flex justify-between items-start gap-2">
                                        <span class="font-bold text-sm text-zinc-900 dark:text-white leading-tight">{{ $result->test_name_snapshot }}</span>
                                        @if ($activeLabOrder->status === 'completed')
                                            @if ($result->is_abnormal)
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-300 ring-1 ring-red-500/20">Abnormal</span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 ring-1 ring-emerald-500/20">Normal</span>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="flex justify-between items-center mt-1.5">
                                        <div class="text-xs">
                                            <span class="text-zinc-400">Hasil:</span>
                                            @if ($activeLabOrder->status === 'completed')
                                                <span class="font-mono text-sm font-bold {{ $result->is_abnormal ? 'text-red-600 dark:text-red-400 animate-pulse' : 'text-zinc-900 dark:text-white' }}">
                                                    {{ $result->result_value ?? '-' }}
                                                </span>
                                                <span class="text-[10px] text-zinc-500 font-mono">{{ $result->unit_snapshot }}</span>
                                            @else
                                                <span class="text-zinc-400 italic font-mono text-xs">Menunggu analis</span>
                                            @endif
                                        </div>
                                        <div class="text-[10px] text-zinc-400 font-mono">
                                            Rujukan: {{ $result->normal_range_snapshot ?? '-' }}
                                        </div>
                                    </div>
                                    @if ($activeLabOrder->status === 'completed' && $result->analis)
                                        <div class="mt-1 text-[9px] text-zinc-400 italic text-right">
                                            Analis: {{ $result->analis->nama_petugas }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-6 text-xs text-zinc-400 dark:text-zinc-500 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                        Tidak ada permintaan lab pada kunjungan ini.
                    </div>
                @endif
            </div>

            <!-- Medical History Timeline Card -->
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm">
                <flux:heading size="lg" class="mb-4">Riwayat Kunjungan</flux:heading>

                <div class="relative pl-4 border-l-2 border-zinc-200 dark:border-zinc-700 space-y-6">
                    @forelse ($recentHistory as $history)
                    <div class="relative">
                        <!-- Bullet marker -->
                        <div class="absolute -left-[23px] mt-1 w-3.5 h-3.5 rounded-full bg-zinc-300 dark:bg-zinc-700 border-2 border-white dark:border-zinc-900"></div>

                        <div class="text-xs text-zinc-500 font-mono font-semibold">{{ $history->created_at->format('d-m-Y H:i') }}</div>
                        <div class="font-bold text-sm text-zinc-800 dark:text-zinc-200 uppercase mt-0.5">
                            {{ $history->poliklinik_type === 'umum' ? 'Poli Umum' : ($history->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'Klinik KIA') }}
                        </div>
                        <div class="text-xs text-zinc-500">
                            🩺 {{ $history->dokter->nama_petugas ?? '-' }}
                            @if ($history->dokter?->nomor_sip)
                                <span class="text-[10px] text-zinc-400">(SIP: {{ $history->dokter->nomor_sip }})</span>
                            @endif
                        </div>

                        <!-- Diagnoses List -->
                        <div class="mt-2 flex flex-wrap gap-1">
                            @forelse($history->icd10s as $diag)
                            <flux:badge size="sm" color="zinc" class="text-[10px] font-mono" title="{{ $diag->icd10_name }}">{{ $diag->icd10_code }}</flux:badge>
                            @empty
                            <span class="text-zinc-400 text-xs italic">Tanpa ICD-10</span>
                            @endforelse
                        </div>

                        <div class="mt-2 text-xs text-zinc-600 dark:text-zinc-400 line-clamp-3 leading-relaxed bg-zinc-50 dark:bg-zinc-950 p-2 rounded-md border border-zinc-100 dark:border-zinc-800">
                            <strong>S:</strong> {{ $history->subjective ?? '-' }}<br>
                            <strong>A:</strong> {{ $history->assessment ?? '-' }}
                        </div>

                        @if ($history->perawat)
                            <div class="mt-1.5 text-[9px] text-zinc-400 dark:text-zinc-500 italic">
                                ⚠️ Diinput oleh: {{ $history->perawat->nama_petugas }} pada {{ $history->created_at->format('d-m-Y H:i') }}
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-zinc-400 italic">Tidak ada riwayat medis sebelumnya.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('refresh-page', () => {
                window.location.reload();
            });
        });
    </script>
</x-layouts::app>