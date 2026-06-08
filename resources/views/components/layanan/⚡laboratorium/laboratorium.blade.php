<div class="py-6 px-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="mb-6">
            <div class="flex items-center gap-2">
                <flux:heading size="xl" class="font-extrabold tracking-tight">TRANSAKSI LABORATORIUM</flux:heading>
                <flux:badge color="purple" size="md">Layanan Penunjang</flux:badge>
            </div>
            <flux:subheading class="mt-1 font-medium">Monitoring permintaan tes lab, status sample, dan hasil laboratorium pasien.</flux:subheading>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>No. Permintaan</flux:table.column>
                    <flux:table.column>No. RM / Pasien</flux:table.column>
                    <flux:table.column>Dokter Perujuk</flux:table.column>
                    <flux:table.column>Waktu Permintaan</flux:table.column>
                    <flux:table.column>Status Lab</flux:table.column>
                    <flux:table.column>Hasil Tes</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($requests as $r)
                        <flux:table.row :key="$r->id">
                            <flux:table.cell class="font-mono text-xs font-semibold">{{ $r->no_permintaan ?? 'LAB-' . sprintf('%05d', $r->id) }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $r->nama_pasien }}</div>
                                <div class="text-xs text-zinc-500 font-mono mt-0.5">{{ $r->no_rekam_medis }}</div>
                            </flux:table.cell>
                            <flux:table.cell>dr. {{ $r->nama_dokter }}</flux:table.cell>
                            <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $r->created_at->format('d-m-Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $r->status_permintaan === 'selesai' ? 'green' : ($r->status_permintaan === 'proses' ? 'blue' : 'zinc') }}" size="sm">
                                    {{ ucfirst($r->status_permintaan ?? 'menunggu') }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs font-mono max-w-xs truncate">
                                {{ $r->catatan_hasil ?: 'Menunggu hasil pemeriksaan...' }}
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500 py-12">
                                <flux:icon.beaker class="w-10 h-10 mx-auto text-zinc-400 mb-2" />
                                <div class="text-sm font-semibold text-zinc-400">Belum ada permintaan laboratorium hari ini</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
