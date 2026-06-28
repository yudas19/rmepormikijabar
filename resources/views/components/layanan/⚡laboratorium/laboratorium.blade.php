<div class="py-6 px-6 bg-slate-50 dark:bg-slate-950 min-h-screen space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 pb-5 border-b border-slate-100 dark:border-slate-800/60">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <flux:heading size="xl" class="font-black tracking-tight text-slate-900 dark:text-white">ANTRIAN LABORATORIUM</flux:heading>
                    <flux:badge color="teal" size="md" class="font-bold px-2.5 py-0.5 rounded-md shadow-xs">Layanan Penunjang</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium text-slate-500 dark:text-slate-400 text-sm">Monitoring permintaan tes lab, status pemeriksaan, dan input hasil analisis.</flux:subheading>
            </div>
            
            {{-- Filters Simetris --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto items-stretch sm:items-end flex-wrap">
                <flux:input type="date" wire:model.live="filterStartDate" label="Mulai" class="w-full sm:w-auto" />
                <flux:input type="date" wire:model.live="filterEndDate" label="Sampai" class="w-full sm:w-auto" />
                <flux:select wire:model.live="statusFilter" label="Status" class="w-full sm:w-36">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="pending">Menunggu</flux:select.option>
                    <flux:select.option value="processing">Proses</flux:select.option>
                    <flux:select.option value="completed">Selesai</flux:select.option>
                </flux:select>
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari pasien / No. RM..." icon="magnifying-glass" class="w-full sm:w-56" />
            </div>
        </div>
    </div>

    {{-- Orders Table Rapi & Simetris --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-xl shadow-sm overflow-hidden p-1">
        <flux:table>
            <flux:table.columns class="bg-slate-50 dark:bg-slate-950/40 text-slate-500 font-bold">
                <flux:table.column class="w-28 text-center">No. Order</flux:table.column>
                <flux:table.column>Identitas Pasien</flux:table.column>
                <flux:table.column class="w-36">Asal Poli</flux:table.column>
                <flux:table.column>Dokter Perujuk</flux:table.column>
                <flux:table.column>Tes Dipesan</flux:table.column>
                <flux:table.column class="w-32">Total Tarif</flux:table.column>
                <flux:table.column class="w-28 text-center">Status</flux:table.column>
                <flux:table.column class="text-right pr-4">Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($orders as $order)
                @php
                    $pasien = $order->medicalRecord?->pendaftaran?->pasien;
                    $poli = $order->medicalRecord?->pendaftaran?->poli;
                    $statusColor = match($order->status) {
                        'pending' => 'amber',
                        'processing' => 'cyan',
                        'completed' => 'emerald',
                        default => 'slate',
                    };
                    $statusLabel = match($order->status) {
                        'pending' => 'Menunggu',
                        'processing' => 'Proses',
                        'completed' => 'Selesai',
                        default => ucfirst($order->status),
                    };
                @endphp
                <flux:table.row wire:key="order-{{ $order->id }}" class="hover:bg-slate-50/60 dark:hover:bg-slate-950/20 transition duration-150">
                    
                    {{-- No. Order Badge (Teal Medis) --}}
                    <flux:table.cell class="text-center">
                        <flux:badge color="teal" size="md" class="font-mono text-xs font-black px-2.5 py-1 bg-teal-50 dark:bg-teal-950/50 border border-teal-200/50 dark:border-teal-800/30 text-teal-700 dark:text-teal-400 rounded-lg shadow-2xs">
                            LAB-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                        </flux:badge>
                    </flux:table.cell>
                    
                    {{-- Patient Details --}}
                    <flux:table.cell>
                        <div class="font-bold text-slate-900 dark:text-white tracking-tight">{{ $pasien?->nama_pasien ?? '-' }}</div>
                        <div class="text-xs text-slate-400 font-mono mt-0.5 tracking-wider">{{ $pasien?->no_rekam_medis ?? '-' }}</div>
                        <div class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $order->created_at->format('d-m-Y H:i') }}</div>
                    </flux:table.cell>
                    
                    {{-- Asal Poli --}}
                    <flux:table.cell>
                        <flux:badge color="slate" size="sm" class="font-medium px-2 rounded-md">{{ $poli?->nama_poli ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    
                    {{-- Dokter --}}
                    <flux:table.cell class="text-sm font-medium text-slate-600 dark:text-slate-400">
                        {{ $order->requester?->nama_petugas ? 'dr. ' . $order->requester->nama_petugas : '-' }}
                    </flux:table.cell>
                    
                    {{-- Tes Dipesan --}}
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @foreach ($order->results->take(3) as $result)
                            <flux:badge color="teal" size="sm" variant="outline" class="text-[10px] font-semibold bg-white dark:bg-slate-950">{{ $result->test_name_snapshot }}</flux:badge>
                            @endforeach
                            @if ($order->results->count() > 3)
                            <flux:badge color="slate" size="sm" class="text-[10px]">+{{ $order->results->count() - 3 }} lagi</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    
                    {{-- Tarif --}}
                    <flux:table.cell class="font-bold font-mono text-sm text-slate-800 dark:text-slate-300">
                        Rp {{ number_format($order->total_tariff, 0, ',', '.') }}
                    </flux:table.cell>
                    
                    {{-- Status --}}
                    <flux:table.cell class="text-center">
                        <flux:badge color="{{ $statusColor }}" size="sm" class="font-black px-2.5 rounded-full shadow-2xs">{{ $statusLabel }}</flux:badge>
                    </flux:table.cell>
                    
                    {{-- Aksi --}}
                    <flux:table.cell class="text-right pr-2">
                        <div class="inline-flex items-center justify-end w-full">
                            @if ($order->status === 'completed')
                            <flux:button
                                variant="ghost"
                                size="sm"
                                icon="eye"
                                class="hover:bg-slate-100 rounded-xl font-bold text-slate-600 dark:text-slate-400"
                                href="{{ route('lab.hasil', $order->id) }}"
                            >Lihat Hasil</flux:button>
                            @else
                            <flux:button
                                size="sm"
                                icon="beaker"
                                class="bg-teal-600 hover:bg-teal-500 text-white font-black px-4 rounded-xl shadow-xs cursor-pointer transition duration-150"
                                href="{{ route('lab.hasil', $order->id) }}"
                            >Input Hasil</flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-16 bg-slate-50/30 dark:bg-slate-950/10">
                        <flux:icon.beaker class="w-12 h-12 mx-auto text-slate-300 dark:text-slate-700 mb-3" />
                        <div class="text-base font-bold text-slate-400 dark:text-slate-500">Belum ada permintaan laboratorium.</div>
                        <div class="text-xs text-slate-400 mt-1">Permintaan lab akan muncul di sini saat dokter memesan dari form rekam medis.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($orders->hasPages())
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>