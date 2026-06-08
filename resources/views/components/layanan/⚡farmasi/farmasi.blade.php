<div class="py-6 px-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="mb-6">
            <div class="flex items-center gap-2">
                <flux:heading size="xl" class="font-extrabold tracking-tight">TRANSAKSI DEPO FARMASI</flux:heading>
                <flux:badge color="emerald" size="md">Layanan Apotek</flux:badge>
            </div>
            <flux:subheading class="mt-1 font-medium">Monitoring resep elektronik (e-resep) masuk, pembuatan obat racikan, dan penyerahan obat.</flux:subheading>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Waktu Masuk</flux:table.column>
                    <flux:table.column>No. RM / Pasien</flux:table.column>
                    <flux:table.column>Klinik Asal</flux:table.column>
                    <flux:table.column>Tipe Resep</flux:table.column>
                    <flux:table.column>Detail Resep Obat</flux:table.column>
                    <flux:table.column>Aturan Pakai</flux:table.column>
                    <flux:table.column>Catatan</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($prescriptions as $p)
                        <flux:table.row :key="$p->id">
                            <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $p->created_at->format('d-m-Y H:i') }}</flux:table.cell>
                            <flux:table.cell>
                                <div class="font-semibold text-zinc-900 dark:text-white">{{ $p->medicalRecord->pasien->nama_pasien ?? '-' }}</div>
                                <div class="text-xs text-zinc-500 font-mono mt-0.5">{{ $p->medicalRecord->pasien->no_rekam_medis ?? '-' }}</div>
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold text-sm uppercase">
                                Poli {{ $p->medicalRecord->poliklinik_type ?? 'Umum' }}
                            </flux:table.cell>
                            <flux:table.cell>
                                <flux:badge color="{{ $p->type === 'racikan' ? 'purple' : 'zinc' }}" size="sm">
                                    {{ $p->type === 'racikan' ? 'Racikan' : 'Non-Racikan' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">
                                @if ($p->type === 'racikan')
                                    <div class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $p->nama_racikan }} ({{ $p->metodeRacik->nama_metode_racik ?? 'Compounding' }} - {{ $p->jumlah_kemasan }} Bks)</div>
                                    <ul class="list-disc pl-4 mt-1 space-y-0.5 text-zinc-500">
                                        @foreach ($p->items as $item)
                                            <li>{{ $item->masterObat->nama_obat }} <span class="text-[10px] font-mono">({{ $item->jumlah }} {{ $item->satuan }})</span></li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="font-semibold text-zinc-850 dark:text-zinc-150">{{ $p->items[0]->masterObat->nama_obat ?? '-' }}</div>
                                    <div class="text-[10px] text-zinc-500 font-mono mt-0.5">Jumlah: {{ $p->items[0]->jumlah ?? 0 }} {{ $p->items[0]->satuan ?? '' }}</div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="font-medium font-mono text-xs text-zinc-800 dark:text-zinc-200">{{ $p->aturan_pakai }}</flux:table.cell>
                            <flux:table.cell>{{ $p->catatan ?: '-' }}</flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="7" class="text-center text-zinc-500 py-12">
                                <flux:icon.beaker class="w-10 h-10 mx-auto text-zinc-400 mb-2" />
                                <div class="text-sm font-semibold text-zinc-400">Belum ada resep obat masuk hari ini</div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>
</div>
