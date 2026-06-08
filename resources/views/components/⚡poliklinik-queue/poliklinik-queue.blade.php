<div class="py-6 px-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="mb-6">
            <div class="flex items-center gap-2">
                <flux:heading size="xl" class="font-extrabold tracking-tight">{{ $title }}</flux:heading>
                <flux:badge color="blue" size="md">Antrean Hari Ini</flux:badge>
            </div>
            <flux:subheading class="mt-1 font-medium">Kelola antrean kunjungan pasien dan pemeriksaan klinis medis secara real-time.</flux:subheading>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>No. Antrean</flux:table.column>
                    <flux:table.column>No. RM / Pasien</flux:table.column>
                    <flux:table.column>Jenis Kelamin / Umur</flux:table.column>
                    <flux:table.column>Dokter</flux:table.column>
                    <flux:table.column>Cara Bayar</flux:table.column>
                    <flux:table.column>Waktu Daftar</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($queues as $q)
                        <flux:table.row :key="$q->id">
                            <!-- Queue Number Badge -->
                            <flux:table.cell>
                                <flux:badge color="zinc" size="md" class="font-mono text-sm font-bold">{{ $q->nomor_antrean }}</flux:badge>
                            </flux:table.cell>
                            
                            <!-- Patient RM & Name -->
                            <flux:table.cell>
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $q->pasien->nama_pasien }}</div>
                                <div class="text-xs text-zinc-500 font-mono mt-0.5">{{ $q->pasien->no_rekam_medis }}</div>
                            </flux:table.cell>
                            
                            <!-- Gender & Age -->
                            <flux:table.cell class="text-xs">
                                <div>{{ $q->pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                <div class="text-zinc-500 mt-0.5">{{ $q->pasien->tanggal_lahir ? $q->pasien->tanggal_lahir->diffInYears(now()) . ' Tahun' : '-' }}</div>
                            </flux:table.cell>

                            <!-- Doctor -->
                            <flux:table.cell class="text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                dr. {{ $q->pendaftaran->dokter->nama_petugas ?? '-' }}
                            </flux:table.cell>

                            <!-- Payment Method -->
                            <flux:table.cell>
                                <flux:badge size="sm" color="{{ $q->pendaftaran->cara_bayar === 'BPJS' ? 'indigo' : 'zinc' }}">
                                    {{ $q->pendaftaran->cara_bayar ?? 'Umum' }}
                                </flux:badge>
                            </flux:table.cell>

                            <!-- Registered Time -->
                            <flux:table.cell class="font-mono text-xs text-zinc-500">
                                {{ $q->created_at->format('H:i') }}
                            </flux:table.cell>

                            <!-- Status Badge -->
                            <flux:table.cell>
                                @php
                                    $statusColors = [
                                        'waiting' => 'zinc',
                                        'anamnesis' => 'orange',
                                        'waiting_doctor' => 'yellow',
                                        'examination' => 'blue',
                                        'completed' => 'green',
                                    ];
                                    $statusNames = [
                                        'waiting' => 'Menunggu',
                                        'anamnesis' => 'Anamnesis',
                                        'waiting_doctor' => 'Menunggu Dokter',
                                        'examination' => 'Pemeriksaan',
                                        'completed' => 'Selesai',
                                    ];
                                    $color = $statusColors[$q->status] ?? 'zinc';
                                    $name = $statusNames[$q->status] ?? $q->status;
                                @endphp
                                <flux:badge color="{{ $color }}" size="sm" class="font-semibold">{{ $name }}</flux:badge>
                            </flux:table.cell>

                            <!-- Actions -->
                            <flux:table.cell>
                                @if ($q->status === 'completed')
                                    <flux:button variant="ghost" size="sm" href="{{ route('medical-record.examine', ['poliklinik' => $this->poliklinik, 'encounter_id' => $q->encounter_id]) }}" wire:navigate>
                                        Detail
                                    </flux:button>
                                @else
                                    <flux:button variant="primary" size="sm" href="{{ route('medical-record.examine', ['poliklinik' => $this->poliklinik, 'encounter_id' => $q->encounter_id]) }}" wire:navigate>
                                        Periksa
                                    </flux:button>
                                @endif
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center text-zinc-500 py-12">
                                <flux:icon.user-minus class="w-10 h-10 mx-auto text-zinc-400 mb-2" />
                                <div class="text-sm font-semibold text-zinc-400">Belum ada antrean untuk hari ini</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
