<x-layouts::app :title="'Clinical Workspace - ' . $patient->nama_pasien">
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 items-start p-6">

        <!-- Left Column: Form (70%) -->
        <div class="lg:col-span-7 space-y-6">
            @if (in_array($poliklinik, ['umum', 'gigi', 'kia']))
            @livewire('⚡medical-record.poli-umum', ['record' => $record])
            @else
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 text-center text-zinc-500">
                Workspace untuk Poliklinik ini belum tersedia.
            </div>
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
                            <div class="font-mono font-semibold text-zinc-900 dark:text-white mt-0.5">{{ $patient->no_rekam_medis }}</div>
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
                                {{ $patient->tanggal_lahir ? $patient->tanggal_lahir->diffInYears(now()) . ' Thn' : '-' }}
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
                        <div class="text-xs text-zinc-500">dr. {{ $history->pendaftaran->dokter->nama_petugas ?? '-' }}</div>

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
                    </div>
                    @empty
                    <div class="text-center py-4 text-xs text-zinc-400 italic">Tidak ada riwayat medis sebelumnya.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</x-layouts::app>