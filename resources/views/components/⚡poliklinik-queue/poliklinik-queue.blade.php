<div class="py-6 px-6 bg-slate-50 dark:bg-slate-950 min-h-screen space-y-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 pb-5 border-b border-slate-100 dark:border-slate-800/60">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <flux:heading size="xl" class="font-black tracking-tight text-slate-900 dark:text-white">{{ $title }}</flux:heading>
                    <flux:badge color="teal" size="md" class="font-bold px-2.5 py-0.5 rounded-md shadow-xs">Antrean Kunjungan</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium text-slate-500 dark:text-slate-400 text-sm">Kelola antrean kunjungan pasien dan pemeriksaan klinis medis secara real-time.</flux:subheading>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-stretch sm:items-end">
                <flux:select wire:model.live="selectedPoliId" size="sm" label="Poliklinik" class="w-full sm:w-52">
                    @foreach ($polis as $p)
                        <flux:select.option value="{{ $p->id }}">{{ $p->nama_poli }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input type="date" wire:model.live="filterStartDate" size="sm" label="Mulai" class="w-full sm:w-auto" />
                <flux:input type="date" wire:model.live="filterEndDate" size="sm" label="Sampai" class="w-full sm:w-auto" />
            </div>
        </div>

        <div class="border border-slate-200 dark:border-slate-800/80 rounded-xl overflow-hidden bg-white dark:bg-slate-900 p-1">
            <flux:table>
                <flux:table.columns class="bg-slate-50 dark:bg-slate-950/40 text-slate-500 font-bold">
                    <flux:table.column class="w-24 text-center">No. Antrean</flux:table.column>
                    <flux:table.column>No. RM / Pasien</flux:table.column>
                    <flux:table.column>Identitas / Umur</flux:table.column>
                    <flux:table.column>Dokter Pemeriksa</flux:table.column>
                    <flux:table.column class="w-28 text-center">Cara Bayar</flux:table.column>
                    <flux:table.column class="w-24 text-center">Waktu Registrasi</flux:table.column>
                    <flux:table.column class="w-32 text-center">Status</flux:table.column>
                    <flux:table.column class="text-right pr-4">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($queues as $q)
                        @php
                            $isSelesai = in_array($q->status, ['completed', 'completed_all']);
                            $rowClass = $isSelesai 
                                ? 'bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/20 dark:hover:bg-emerald-900/30 border-l-4 border-emerald-500 cursor-pointer transition duration-150'
                                : 'hover:bg-slate-100/60 dark:hover:bg-slate-950/20 cursor-pointer transition duration-150';
                        @endphp
                        <flux:table.row :key="$q->id" class="{{ $rowClass }}" wire:click="periksaPasien({{ $q->id }})">
                            
                            <flux:table.cell class="text-center">
                                <flux:badge color="teal" size="md" class="font-mono text-sm font-black px-3 py-1 bg-teal-50 dark:bg-teal-950/50 border border-teal-200/50 dark:border-teal-800/30 text-teal-700 dark:text-teal-400 rounded-lg shadow-2xs">{{ $q->nomor_antrean }}</flux:badge>
                            </flux:table.cell>
                            
                            <flux:table.cell>
                                <div class="font-bold text-slate-900 dark:text-white tracking-tight">{{ $q->pasien->nama_pasien }}</div>
                                <div class="text-xs text-slate-400 font-mono mt-0.5 tracking-wider">{{ $q->pasien->no_rekam_medis }}</div>
                            </flux:table.cell>
                            
                            <flux:table.cell class="text-xs font-medium text-slate-600 dark:text-slate-400">
                                <div>{{ $q->pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                <div class="text-slate-400 font-mono mt-0.5">{{ $q->pasien->tanggal_lahir ? number_format((float) $q->pasien->tanggal_lahir->diffInYears(now()), 1) . ' Thn' : '-' }}</div>
                            </flux:table.cell>

                            <flux:table.cell class="text-sm font-semibold text-slate-700 dark:text-slate-300">
                                <span class="text-slate-400 text-xs font-normal block">dr. DPJP:</span>
                                <span>dr. {{ $q->pendaftaran?->dokter?->nama_petugas ?? '-' }}</span>
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                <flux:badge size="sm" color="{{ ($q->pendaftaran?->cara_bayar ?? 'Umum') === 'BPJS' ? 'emerald' : 'slate' }}" class="font-bold px-2 rounded-md">
                                    {{ $q->pendaftaran?->cara_bayar ?? 'Umum' }}
                                </flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="font-mono text-xs text-center font-bold text-slate-400 dark:text-slate-500">
                                {{ $q->created_at->format('H:i') }}
                            </flux:table.cell>

                            <flux:table.cell class="text-center">
                                @php
                                    $statusColors = [
                                        'waiting' => 'slate',
                                        'anamnesis' => 'orange',
                                        'waiting_doctor' => 'amber',
                                        'examination' => 'teal',
                                        'completed' => 'emerald',
                                    ];
                                    $statusNames = [
                                        'waiting' => 'Menunggu',
                                        'anamnesis' => 'Anamnesis',
                                        'waiting_doctor' => 'Menunggu Dokter',
                                        'examination' => 'Pemeriksaan',
                                        'completed' => 'Selesai',
                                    ];
                                    $color = $statusColors[$q->status] ?? 'slate';
                                    $name = $statusNames[$q->status] ?? $q->status;
                                @endphp
                                <flux:badge color="{{ $color }}" size="sm" class="font-black px-2.5 rounded-full shadow-2xs">{{ $name }}</flux:badge>
                            </flux:table.cell>

                            <flux:table.cell class="text-right pr-2">
                                <div class="inline-flex items-center gap-1.5 justify-end w-full">
                                    @if ($q->status !== 'completed' && $q->status !== 'completed_all')
                                        @php
                                            $sudahDipanggil = in_array($q->status_panggilan, ['memanggil', 'selesai']);
                                        @endphp
                                        @if ($sudahDipanggil)
                                            <flux:button variant="ghost" icon="speaker-wave" size="sm" class="text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/40 rounded-xl" wire:click.stop="panggilAntrean({{ $q->id }})" title="Panggil Ulang Pasien">
                                                Panggil Ulang
                                            </flux:button>
                                        @else
                                            <flux:button variant="ghost" icon="speaker-wave" size="sm" class="text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl" wire:click.stop="panggilAntrean({{ $q->id }})" title="Panggil Pasien">
                                                Panggil
                                            </flux:button>
                                        @endif
                                    @endif

                                    @if ($q->status === 'completed' || $q->status === 'completed_all')
                                        <flux:button variant="ghost" size="sm" class="hover:bg-slate-100 rounded-xl font-bold" href="{{ route('medical-record.examine', ['poliklinik' => $q->poliklinik_type, 'encounter_id' => $q->encounter_id]) }}" wire:navigate x-on:click.stop="">
                                            Detail
                                        </flux:button>
                                    @else
                                        <flux:button size="sm" class="bg-teal-600 hover:bg-teal-500 text-white font-black px-4 rounded-xl shadow-xs cursor-pointer transition duration-150" href="{{ route('medical-record.examine', ['poliklinik' => $q->poliklinik_type, 'encounter_id' => $q->encounter_id]) }}" wire:navigate x-on:click.stop="">
                                            Periksa
                                        </flux:button>
                                    @endif
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="8" class="text-center text-slate-400 py-16 bg-slate-50/30 dark:bg-slate-950/10">
                                <flux:icon.user-minus class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" />
                                <div class="text-base font-bold text-slate-400 dark:text-slate-500">Belum ada antrean kunjungan pasien hari ini.</div>
                                <div class="text-xs text-slate-400 mt-1">Ganti filter poliklinik atau tanggal jika diperlukan.</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>