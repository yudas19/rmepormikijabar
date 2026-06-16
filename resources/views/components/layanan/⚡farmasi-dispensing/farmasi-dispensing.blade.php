<div class="py-6 px-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">WORKSPACE DISPENSING</flux:heading>
                    <flux:badge color="emerald" size="md">
                        RX-{{ str_pad($prescription->id, 5, '0', STR_PAD_LEFT) }}
                    </flux:badge>
                    @if ($isFinalized)
                    <flux:badge color="green" size="md">Obat Diserahkan</flux:badge>
                    @else
                    <flux:badge color="amber" size="md">Menunggu Dispensing</flux:badge>
                    @endif
                </div>
                <flux:subheading class="mt-1">
                    Racik dan serahkan obat untuk pasien
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $prescription->medicalRecord?->pendaftaran?->pasien?->nama_pasien ?? '-' }}</span>
                </flux:subheading>
            </div>
            <div class="flex items-center gap-2">
                <flux:button variant="filled" icon="printer" size="sm" onclick="window.open('{{ route('print.resep', $prescription->id) }}', '_blank')">Cetak Resep</flux:button>
                <a href="{{ route('layanan.farmasi') }}">
                    <flux:button variant="ghost" icon="arrow-left" size="sm">Kembali ke Antrian</flux:button>
                </a>
            </div>
        </div>
    </div>

    {{-- Patient + Pharmacist Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Patient Info --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
            <flux:heading size="md" class="font-bold mb-4">Informasi Pasien & Resep</flux:heading>
            @php
                $pasien = $prescription->medicalRecord?->pendaftaran?->pasien;
                $poli = $prescription->medicalRecord?->pendaftaran?->poli;
                $dokter = $prescription->medicalRecord?->pendaftaran?->dokter;
            @endphp
            <div class="space-y-3 text-sm">
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Nama Pasien</span><span class="font-bold text-zinc-900 dark:text-white">{{ $pasien?->nama_pasien ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">No. Rekam Medis</span><span class="font-mono font-bold">{{ $pasien?->no_rekam_medis ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Asal Poli</span><flux:badge color="zinc" size="sm">{{ $poli?->nama_poli ?? '-' }}</flux:badge></div>
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Dokter Penulis Resep</span><span>{{ $dokter?->nama_petugas ?? '-' }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Tipe Resep</span>
                    <flux:badge color="{{ $prescription->type === 'racikan' ? 'purple' : 'zinc' }}" size="sm">{{ $prescription->type === 'racikan' ? 'Racikan' : 'Non-Racikan' }}</flux:badge>
                </div>
                @if ($prescription->type === 'racikan')
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Nama Racikan</span><span class="font-bold">{{ $prescription->nama_racikan }}</span></div>
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Metode</span><span>{{ $prescription->metodeRacik?->nama_metode_racik ?? '-' }} ({{ $prescription->jumlah_kemasan }} bks)</span></div>
                @endif
                <div class="flex justify-between"><span class="text-zinc-500 font-semibold">Aturan Pakai (Dokter)</span><span class="font-mono font-bold text-blue-700 dark:text-blue-400">{{ $prescription->aturan_pakai }}</span></div>
                @if ($prescription->catatan)
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Catatan Dokter</span>
                    <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 rounded-lg p-3">{{ $prescription->catatan }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Pharmacist Info & Summary --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
            <flux:heading size="md" class="font-bold mb-4">Informasi Apoteker & Tagihan</flux:heading>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Petugas Farmasi</span>
                    <div class="flex items-center gap-2">
                        <flux:icon.user-circle class="w-4 h-4 text-emerald-500" />
                        <span class="font-bold text-emerald-800 dark:text-emerald-300">{{ $apotekerNama }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Tanggal Resep</span>
                    <span class="font-mono">{{ $prescription->created_at->format('d-m-Y H:i') }}</span>
                </div>
                @if ($isFinalized && $prescription->dispensed_at)
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Diserahkan Pada</span>
                    <span class="font-mono text-green-700 dark:text-green-400">{{ $prescription->dispensed_at->format('d-m-Y H:i') }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Jumlah Item</span>
                    <flux:badge color="emerald" size="sm">{{ count($dispensingRows) }} obat</flux:badge>
                </div>
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <span class="font-bold text-zinc-700 dark:text-zinc-200">Grand Total</span>
                    <span class="text-2xl font-extrabold font-mono text-emerald-800 dark:text-emerald-300">
                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Dispensing Table --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 p-6 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.beaker class="w-5 h-5 text-emerald-500" />
            <flux:heading size="lg" class="font-bold">Dispensing Obat — Permintaan Dokter vs Penyerahan Aktual</flux:heading>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-950/40 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th colspan="3" class="px-4 py-2 text-center font-bold text-blue-700 dark:text-blue-400 text-xs uppercase tracking-wider border-r border-zinc-200 dark:border-zinc-700 bg-blue-50/50 dark:bg-blue-950/20">Permintaan Dokter (Read-Only)</th>
                        <th colspan="5" class="px-4 py-2 text-center font-bold text-emerald-700 dark:text-emerald-400 text-xs uppercase tracking-wider bg-emerald-50/50 dark:bg-emerald-950/20">Penyerahan Apoteker (Editable)</th>
                    </tr>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-blue-50/30 dark:bg-blue-950/10">Nama Obat</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-blue-50/30 dark:bg-blue-950/10">Qty</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-blue-50/30 dark:bg-blue-950/10 border-r border-zinc-200 dark:border-zinc-700">Satuan</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-emerald-50/30 dark:bg-emerald-950/10">Obat Diserahkan</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-emerald-50/30 dark:bg-emerald-950/10 w-24">Qty</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-emerald-50/30 dark:bg-emerald-950/10 w-40">Aturan Pakai</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-emerald-50/30 dark:bg-emerald-950/10">Stok</th>
                        <th class="px-4 py-3 text-right font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase bg-emerald-50/30 dark:bg-emerald-950/10">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($dispensingRows as $idx => $row)
                    <tr wire:key="disp-{{ $idx }}">
                        {{-- Doctor Request (Read-Only) --}}
                        <td class="px-4 py-3 font-semibold text-blue-800 dark:text-blue-300 bg-blue-50/20 dark:bg-blue-950/5">{{ $row['requested_obat_name'] }}</td>
                        <td class="px-4 py-3 font-mono text-blue-700 dark:text-blue-400 bg-blue-50/20 dark:bg-blue-950/5">{{ $row['requested_qty'] }}</td>
                        <td class="px-4 py-3 text-zinc-500 bg-blue-50/20 dark:bg-blue-950/5 border-r border-zinc-200 dark:border-zinc-700">{{ $row['requested_satuan'] }}</td>

                        {{-- Pharmacist Actual (Editable) --}}
                        <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/5">
                            @if (! $isFinalized)
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-emerald-800 dark:text-emerald-300">{{ $row['dispensed_obat_name'] }}</span>
                                @if ($editingRowIndex === $idx)
                                {{-- Inline substitution search --}}
                                <div class="relative">
                                    <flux:input wire:model.live="drugSubQuery" placeholder="Ganti obat..." size="sm" class="w-40" autofocus />
                                    @if (count($drugSubResults) > 0)
                                    <div class="absolute z-30 top-full mt-1 left-0 w-64 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl overflow-hidden">
                                        <div class="max-h-48 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                                            @foreach ($drugSubResults as $sub)
                                            <button type="button" wire:click="selectSubstitute({{ $sub['id'] }})" class="w-full text-left px-3 py-2 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors text-xs">
                                                <div class="font-semibold">{{ $sub['nama_obat'] }}</div>
                                                <div class="text-zinc-400 flex justify-between">
                                                    <span>Stok: {{ $sub['stok_saat_ini'] }}</span>
                                                    <span class="font-mono">Rp {{ number_format($sub['harga_jual'], 0, ',', '.') }}</span>
                                                </div>
                                            </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                    <button type="button" wire:click="cancelSubstitution" class="absolute -top-1 -right-5 text-zinc-400 hover:text-red-500">✕</button>
                                </div>
                                @else
                                <button type="button" wire:click="openDrugSubstitution({{ $idx }})" class="text-[10px] text-emerald-600 hover:text-emerald-800 underline">Ganti</button>
                                @endif
                            </div>
                            @else
                            <span class="font-semibold text-emerald-800 dark:text-emerald-300">{{ $row['dispensed_obat_name'] }}</span>
                            @if ($row['dispensed_obat_name'] !== $row['requested_obat_name'])
                            <flux:badge color="amber" size="sm" class="ml-1 text-[9px]">Substitusi</flux:badge>
                            @endif
                            @endif
                        </td>
                        <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/5">
                            @if (! $isFinalized)
                            <flux:input type="number" wire:model.live="dispensingRows.{{ $idx }}.dispensed_qty" size="sm" min="0" class="w-20" />
                            @else
                            <span class="font-mono font-bold">{{ $row['dispensed_qty'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/5">
                            @if (! $isFinalized)
                            <flux:input wire:model="dispensingRows.{{ $idx }}.dispensed_signa" size="sm" placeholder="Aturan..." class="w-36" />
                            @else
                            <span class="font-mono text-xs">{{ $row['dispensed_signa'] }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/5">
                            @php
                                $stokColor = $row['stok_available'] <= 0 ? 'red' : ($row['stok_available'] <= 100 ? 'amber' : 'green');
                            @endphp
                            <flux:badge color="{{ $stokColor }}" size="sm" class="font-mono">{{ $row['stok_available'] }}</flux:badge>
                        </td>
                        <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/5 text-right font-bold font-mono text-emerald-700 dark:text-emerald-400">
                            Rp {{ number_format($row['subtotal'], 0, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-zinc-50 dark:bg-zinc-950/40 border-t-2 border-zinc-300 dark:border-zinc-600">
                    <tr>
                        <td colspan="7" class="px-4 py-4 text-right font-bold text-zinc-700 dark:text-zinc-200 text-sm">Grand Total Obat</td>
                        <td class="px-4 py-4 text-right">
                            <span class="text-xl font-extrabold font-mono text-emerald-800 dark:text-emerald-300">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Action Bar --}}
    @if (! $isFinalized)
    <div class="flex flex-col sm:flex-row justify-end gap-3 bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
        <div class="flex-1 flex items-center gap-2 text-xs text-zinc-500">
            <flux:icon.information-circle class="w-4 h-4" />
            <span>"Simpan Perubahan" menyimpan editing tanpa mengurangi stok. "Finalisasi" akan mengurangi stok obat dan menandai resep selesai.</span>
        </div>
        <div class="flex gap-3">
            <flux:button variant="filled" icon="document" wire:click="saveDraft" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="saveDraft">Simpan Perubahan</span>
                <span wire:loading wire:target="saveDraft">Menyimpan...</span>
            </flux:button>
            <flux:button variant="primary" icon="check-circle" wire:click="finalize" wire:loading.attr="disabled"
                wire:confirm="Finalisasi akan mengurangi stok obat secara permanen. Lanjutkan?">
                <span wire:loading.remove wire:target="finalize">Finalisasi & Serahkan Obat</span>
                <span wire:loading wire:target="finalize">Memfinalisasi...</span>
            </flux:button>
        </div>
    </div>
    @else
    <div class="flex items-center justify-center gap-3 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/40 p-6 rounded-xl">
        <flux:icon.check-badge class="w-8 h-8 text-green-600" />
        <div>
            <div class="font-bold text-green-800 dark:text-green-300">Obat Telah Diserahkan</div>
            <div class="text-sm text-green-600 dark:text-green-400">Dispensing selesai oleh {{ $apotekerNama }} pada {{ $prescription->dispensed_at?->format('d-m-Y H:i') }}.</div>
        </div>
    </div>
    @endif
</div>
