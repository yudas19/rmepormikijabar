<div class="py-6 px-6 space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">ANTRIAN DEPO FARMASI</flux:heading>
                    <flux:badge color="emerald" size="md">Layanan Apotek</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">Dispensing resep elektronik, penyerahan obat, dan monitoring stok.</flux:subheading>
            </div>
            <div class="flex gap-3 flex-wrap">
                <flux:select wire:model.live="statusFilter" class="min-w-40">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="waiting">Menunggu Obat</flux:select.option>
                    <flux:select.option value="dispensed">Selesai</flux:select.option>
                </flux:select>
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari pasien / No. RM..." icon="magnifying-glass" class="min-w-52" />
                <a href="{{ route('farmasi.stok') }}">
                    <flux:button variant="filled" icon="archive-box" size="sm">Manajemen Stok & Opname</flux:button>
                </a>
            </div>
        </div>
    </div>

    {{-- Prescriptions Table --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Waktu Masuk</flux:table.column>
                <flux:table.column>Pasien</flux:table.column>
                <flux:table.column>Asal Poli</flux:table.column>
                <flux:table.column>Tipe Resep</flux:table.column>
                <flux:table.column>Detail Obat</flux:table.column>
                <flux:table.column>Aturan Pakai</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($prescriptions as $p)
                @php
                    $pasien = $p->medicalRecord?->pendaftaran?->pasien;
                    $poli = $p->medicalRecord?->pendaftaran?->poli;
                @endphp
                <flux:table.row wire:key="rx-{{ $p->id }}">
                    <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $p->created_at->format('d-m-Y H:i') }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $pasien?->nama_pasien ?? '-' }}</div>
                        <div class="text-xs text-zinc-500 font-mono mt-0.5">{{ $pasien?->no_rekam_medis ?? '-' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $poli?->nama_poli ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $p->type === 'racikan' ? 'purple' : 'zinc' }}" size="sm">
                            {{ $p->type === 'racikan' ? 'Racikan' : 'Non-Racikan' }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-xs max-w-xs">
                        @if ($p->type === 'racikan')
                        <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $p->nama_racikan }} ({{ $p->metodeRacik?->nama_metode_racik ?? '-' }} - {{ $p->jumlah_kemasan }} Bks)</div>
                        <ul class="list-disc pl-4 mt-1 space-y-0.5 text-zinc-500">
                            @foreach ($p->items as $item)
                            <li>{{ $item->requestedObat?->nama_obat ?? '-' }} <span class="text-[10px] font-mono">({{ $item->requested_qty }} {{ $item->satuan }})</span></li>
                            @endforeach
                        </ul>
                        @else
                        <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $p->items->first()?->requestedObat?->nama_obat ?? '-' }}</div>
                        <div class="text-[10px] text-zinc-500 font-mono mt-0.5">Jumlah: {{ $p->items->first()?->requested_qty ?? 0 }} {{ $p->items->first()?->satuan ?? '' }}</div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="font-medium font-mono text-xs text-zinc-800 dark:text-zinc-200">{{ $p->aturan_pakai }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $p->dispensing_status_color }}" size="sm" class="font-semibold">
                            {{ $p->dispensing_status_label }}
                        </flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($p->dispensing_status === 'dispensed')
                        <flux:button variant="ghost" size="sm" icon="eye" href="{{ route('farmasi.dispensing', $p->id) }}">Lihat</flux:button>
                        @else
                        <flux:button variant="primary" size="sm" icon="beaker" href="{{ route('farmasi.dispensing', $p->id) }}">Racik / Serahkan</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-16">
                        <flux:icon.beaker class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                        <div class="text-sm font-semibold text-zinc-400">Belum ada resep obat masuk</div>
                        <div class="text-xs text-zinc-300 dark:text-zinc-600 mt-1">Resep akan muncul saat dokter menyelesaikan pemeriksaan.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($prescriptions->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>
</div>
