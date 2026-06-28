<div class="py-6 px-6 bg-slate-50 dark:bg-slate-950 min-h-screen space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 pb-5 border-b border-slate-100 dark:border-slate-800/60">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <flux:heading size="xl" class="font-black tracking-tight text-slate-900 dark:text-white">ANTRIAN DEPO FARMASI</flux:heading>
                    <flux:badge color="teal" size="md" class="font-bold px-2.5 py-0.5 rounded-md shadow-xs">Layanan Apotek</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium text-slate-500 dark:text-slate-400 text-sm">Dispensing resep elektronik, penyerahan obat, dan monitoring stok.</flux:subheading>
            </div>
            
            {{-- Filters & Action Button --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-stretch sm:items-end flex-wrap">
                <flux:input type="date" wire:model.live="filterStartDate" label="Mulai" class="w-full sm:w-auto" />
                <flux:input type="date" wire:model.live="filterEndDate" label="Sampai" class="w-full sm:w-auto" />
                <flux:select wire:model.live="statusFilter" label="Status" class="w-full sm:w-40">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="waiting">Menunggu Obat</flux:select.option>
                    <flux:select.option value="dispensed">Selesai</flux:select.option>
                </flux:select>
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari pasien / No. RM..." icon="magnifying-glass" class="w-full sm:w-52" />
                
                <a href="{{ route('farmasi.stok') }}" class="w-full sm:w-auto mt-2 sm:mt-0">
                    <flux:button variant="filled" icon="archive-box" size="sm" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl h-9">
                        Manajemen Stok
                    </flux:button>
                </a>
            </div>
        </div>
    </div>

    {{-- Prescriptions Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-xl shadow-sm overflow-hidden p-1">
        <flux:table>
            <flux:table.columns class="bg-slate-50 dark:bg-slate-950/40 text-slate-500 font-bold">
                <flux:table.column class="w-24 text-center">Waktu Masuk</flux:table.column>
                <flux:table.column class="w-52">Identitas Pasien</flux:table.column>
                <flux:table.column class="w-32">Asal Poli</flux:table.column>
                <flux:table.column class="w-28 text-center">Tipe Resep</flux:table.column>
                <flux:table.column>Item Resep Obat & Aturan Pakai</flux:table.column>
                <flux:table.column class="w-28 text-center">Status</flux:table.column>
                <flux:table.column class="text-right pr-4">Aksi</flux:table.column>
            </flux:table.columns>
            
            <flux:table.rows>
                @forelse ($prescriptions as $p)
                @php
                    $pasien = $p->pendaftaran?->pasien;
                    $poli = $p->pendaftaran?->poli;
                @endphp
                <flux:table.row wire:key="rx-{{ $p->id }}" class="hover:bg-slate-50/60 dark:hover:bg-slate-950/20 transition duration-150 align-top">
                    
                    {{-- Waktu Masuk --}}
                    <flux:table.cell class="font-mono text-xs text-center font-bold text-slate-400 dark:text-slate-500 pt-4">
                        <div class="tracking-tight">{{ $p->created_at->format('H:i') }}</div>
                        <div class="text-[10px] font-normal text-slate-400 mt-0.5">{{ $p->created_at->format('d-m-Y') }}</div>
                    </flux:table.cell>
                    
                    {{-- Patient Info --}}
                    <flux:table.cell class="pt-4">
                        <div class="font-bold text-slate-900 dark:text-white tracking-tight">{{ $pasien?->nama_pasien ?? '-' }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5 tracking-wider">{{ $pasien?->no_rekam_medis ?? '-' }}</div>
                    </flux:table.cell>
                    
                    {{-- Asal Poli --}}
                    <flux:table.cell class="pt-4">
                        <flux:badge color="slate" size="sm" class="font-medium px-2 rounded-md">{{ $poli?->nama_poli ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    
                    {{-- Tipe Resep --}}
                    <flux:table.cell class="text-center pt-4">
                        <div class="flex flex-col gap-1 items-center justify-center">
                            @foreach ($p->medicalRecord->prescriptions->pluck('type')->unique() as $t)
                                <flux:badge color="{{ $t === 'racikan' ? 'cyan' : 'slate' }}" size="sm" class="font-bold rounded-md px-2 shadow-2xs">
                                    {{ $t === 'racikan' ? 'Racikan' : 'Non-Racikan' }}
                                </flux:badge>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    
                    {{-- Detail Resep & Aturan Pakai Gabungan (Simetris) --}}
                    <flux:table.cell class="text-xs max-w-md pt-3">
                        <div class="divide-y divide-slate-100 dark:divide-slate-800/60 space-y-2.5">
                            @foreach ($p->medicalRecord->prescriptions as $presc)
                                <div class="flex justify-between items-start gap-4 pt-2.5 first:pt-0">
                                    <div class="flex-1">
                                        @if ($presc->type === 'racikan')
                                            <div>
                                                <div class="font-bold text-slate-800 dark:text-slate-200 bg-slate-100 dark:bg-slate-800/60 px-2 py-0.5 rounded w-max mb-1">
                                                    💊 {{ $presc->nama_racikan }} <span class="text-[10px] font-normal text-slate-400">({{ $presc->metodeRacik?->nama_metode_racik ?? '-' }} - {{ $presc->jumlah_kemasan }} Bks)</span>
                                                </div>
                                                <ul class="list-disc pl-4 space-y-0.5 text-slate-500 dark:text-slate-400">
                                                    @foreach ($presc->items as $item)
                                                        <li>{{ $item->requestedObat?->nama_obat ?? '-' }} <span class="text-[10px] font-mono font-bold text-teal-600 dark:text-teal-400">({{ $item->requested_qty }} {{ $item->satuan }})</span></li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @else
                                            <div>
                                                <div class="font-bold text-slate-800 dark:text-slate-200">{{ $presc->items->first()?->requestedObat?->nama_obat ?? '-' }}</div>
                                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">Jumlah: <span class="font-bold text-teal-600 dark:text-teal-400">{{ $presc->items->first()?->requested_qty ?? 0 }} {{ $presc->items->first()?->satuan ?? '' }}</span></div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Kolom Aturan Pakai Sejajar Item --}}
                                    <div class="w-36 flex-shrink-0 text-right">
                                        <span class="inline-block px-2 py-1 font-mono text-[11px] font-bold bg-teal-50/50 dark:bg-teal-950/20 text-teal-700 dark:text-teal-400 rounded border border-teal-200/30">
                                            {{ $presc->aturan_pakai ?: '-' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </flux:table.cell>
                    
                    {{-- Status Badge --}}
                    <flux:table.cell class="text-center pt-4">
                        @php
                            $statusColor = match($p->dispensing_status) {
                                'waiting' => 'amber',
                                'dispensed' => 'emerald',
                                default => 'slate',
                            };
                        @endphp
                        <flux:badge color="{{ $statusColor }}" size="sm" class="font-black px-2.5 rounded-full shadow-2xs">
                            {{ $p->dispensing_status_label }}
                        </flux:badge>
                    </flux:table.cell>
                    
                    {{-- Aksi --}}
                    <flux:table.cell class="text-right pr-2 pt-3.5">
                        <div class="inline-flex items-center justify-end w-full">
                            @if ($p->dispensing_status === 'dispensed')
                            <flux:button
                                variant="ghost"
                                size="sm"
                                icon="eye"
                                class="hover:bg-slate-100 rounded-xl font-bold text-slate-600 dark:text-slate-400"
                                href="{{ route('farmasi.dispensing', $p->id) }}"
                            >Lihat</flux:button>
                            @else
                            <flux:button
                                size="sm"
                                icon="beaker"
                                class="bg-teal-600 hover:bg-teal-500 text-white font-black px-4 rounded-xl shadow-xs cursor-pointer transition duration-150"
                                href="{{ route('farmasi.dispensing', $p->id) }}"
                            >Racik / Serahkan</flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center py-16 bg-slate-50/30 dark:bg-slate-950/10">
                        <flux:icon.beaker class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" />
                        <div class="text-base font-bold text-slate-400 dark:text-slate-500">Belum ada resep obat masuk.</div>
                        <div class="text-xs text-slate-400 mt-1">Resep akan muncul secara otomatis saat dokter menyelesaikan pemeriksaan di poliklinik.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($prescriptions->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>
</div>