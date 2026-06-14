<div class="py-6 px-6 space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">ANTRIAN LABORATORIUM</flux:heading>
                    <flux:badge color="purple" size="md">Layanan Penunjang</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">Monitoring permintaan tes lab, status pemeriksaan, dan input hasil analisis.</flux:subheading>
            </div>
            <div class="flex gap-3 flex-wrap">
                <flux:input type="date" wire:model.live="filterDate" class="min-w-40" />
                <flux:select wire:model.live="statusFilter" class="min-w-40">
                    <flux:select.option value="">Semua Status</flux:select.option>
                    <flux:select.option value="pending">Menunggu</flux:select.option>
                    <flux:select.option value="processing">Proses</flux:select.option>
                    <flux:select.option value="completed">Selesai</flux:select.option>
                </flux:select>
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari pasien / No. RM..." icon="magnifying-glass" class="min-w-52" />
            </div>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>No. Order</flux:table.column>
                <flux:table.column>Pasien</flux:table.column>
                <flux:table.column>Asal Poli</flux:table.column>
                <flux:table.column>Dokter Perujuk</flux:table.column>
                <flux:table.column>Tes Dipesan</flux:table.column>
                <flux:table.column>Total Tarif</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Aksi</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($orders as $order)
                @php
                    $pasien = $order->medicalRecord?->pendaftaran?->pasien;
                    $poli = $order->medicalRecord?->pendaftaran?->poli;
                    $statusColor = match($order->status) {
                        'pending' => 'amber',
                        'processing' => 'blue',
                        'completed' => 'green',
                        default => 'zinc',
                    };
                    $statusLabel = match($order->status) {
                        'pending' => 'Menunggu',
                        'processing' => 'Proses',
                        'completed' => 'Selesai',
                        default => ucfirst($order->status),
                    };
                @endphp
                <flux:table.row wire:key="order-{{ $order->id }}">
                    <flux:table.cell class="font-mono font-bold text-purple-700 dark:text-purple-400">
                        LAB-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-semibold text-zinc-900 dark:text-white">{{ $pasien?->nama_pasien ?? '-' }}</div>
                        <div class="text-xs text-zinc-500 font-mono mt-0.5">{{ $pasien?->no_rekam_medis ?? '-' }}</div>
                        <div class="text-xs text-zinc-400 mt-0.5">{{ $order->created_at->format('d-m-Y H:i') }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $poli?->nama_poli ?? '-' }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">
                        {{ $order->requester?->nama_petugas ? 'dr. ' . $order->requester->nama_petugas : '-' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex flex-wrap gap-1 max-w-xs">
                            @foreach ($order->results->take(3) as $result)
                            <flux:badge color="purple" size="sm" class="text-[10px]">{{ $result->test_name_snapshot }}</flux:badge>
                            @endforeach
                            @if ($order->results->count() > 3)
                            <flux:badge color="zinc" size="sm" class="text-[10px]">+{{ $order->results->count() - 3 }} lagi</flux:badge>
                            @endif
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="font-bold font-mono text-purple-800 dark:text-purple-300">
                        Rp {{ number_format($order->total_tariff, 0, ',', '.') }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $statusColor }}" size="sm" class="font-semibold">{{ $statusLabel }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($order->status === 'completed')
                        <flux:button
                            variant="ghost"
                            size="sm"
                            icon="eye"
                            href="{{ route('lab.hasil', $order->id) }}"
                        >Lihat Hasil</flux:button>
                        @else
                        <flux:button
                            variant="primary"
                            size="sm"
                            icon="beaker"
                            href="{{ route('lab.hasil', $order->id) }}"
                        >Input Hasil</flux:button>
                        @endif
                    </flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-16">
                        <flux:icon.beaker class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                        <div class="text-sm font-semibold text-zinc-400">Belum ada permintaan laboratorium</div>
                        <div class="text-xs text-zinc-300 dark:text-zinc-600 mt-1">Permintaan lab akan muncul di sini saat dokter memesan dari form rekam medis.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($orders->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $orders->links() }}
        </div>
        @endif
    </div>
</div>
