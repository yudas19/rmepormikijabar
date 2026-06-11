<div class="py-6 px-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">WORKSPACE ANALIS LAB</flux:heading>
                    <flux:badge color="purple" size="md">
                        LAB-{{ str_pad($labOrder->id, 5, '0', STR_PAD_LEFT) }}
                    </flux:badge>
                    @if ($isFinalized)
                    <flux:badge color="green" size="md">Selesai / Terfinalisasi</flux:badge>
                    @elseif ($labOrder->status === 'processing')
                    <flux:badge color="blue" size="md">Sedang Diproses</flux:badge>
                    @else
                    <flux:badge color="amber" size="md">Menunggu Analis</flux:badge>
                    @endif
                </div>
                <flux:subheading class="mt-1">
                    Input hasil pemeriksaan laboratorium untuk pasien
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $labOrder->medicalRecord?->pendaftaran?->pasien?->nama_pasien ?? '-' }}</span>
                </flux:subheading>
            </div>
            <div>
                <a href="{{ route('layanan.laboratorium') }}">
                    <flux:button variant="ghost" icon="arrow-left" size="sm">Kembali ke Antrian</flux:button>
                </a>
            </div>
        </div>
    </div>

    {{-- Patient & Order Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Patient Info --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
            <flux:heading size="md" class="font-bold mb-4">Informasi Pasien</flux:heading>
            @php $pasien = $labOrder->medicalRecord?->pendaftaran?->pasien; @endphp
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-500 font-semibold">Nama Pasien</span>
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $pasien?->nama_pasien ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 font-semibold">No. Rekam Medis</span>
                    <span class="font-mono font-bold text-zinc-800 dark:text-zinc-200">{{ $pasien?->no_rekam_medis ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 font-semibold">Asal Poli</span>
                    <flux:badge color="zinc" size="sm">{{ $labOrder->medicalRecord?->pendaftaran?->poli?->nama_poli ?? '-' }}</flux:badge>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 font-semibold">Dokter Perujuk</span>
                    <span class="text-zinc-800 dark:text-zinc-200">{{ $labOrder->requester?->nama_petugas ? 'dr. ' . $labOrder->requester->nama_petugas : '-' }}</span>
                </div>
                @if ($labOrder->clinical_notes)
                <div class="pt-2 border-t border-zinc-100 dark:border-zinc-800">
                    <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Catatan Klinis Dokter</span>
                    <p class="mt-1 text-sm text-zinc-700 dark:text-zinc-300 bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-800/40 rounded-lg p-3">{{ $labOrder->clinical_notes }}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Analis Info & Summary --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
            <flux:heading size="md" class="font-bold mb-4">Informasi Analis & Tagihan</flux:heading>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Petugas Analis</span>
                    <div class="flex items-center gap-2">
                        <flux:icon.user-circle class="w-4 h-4 text-purple-500" />
                        <span class="font-bold text-purple-800 dark:text-purple-300">{{ $analisisNama }}</span>
                    </div>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Tanggal Order</span>
                    <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ $labOrder->created_at->format('d-m-Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-zinc-500 font-semibold">Jumlah Tes</span>
                    <flux:badge color="purple" size="sm">{{ count($resultRows) }} pemeriksaan</flux:badge>
                </div>
                <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800 flex justify-between items-center">
                    <span class="font-bold text-zinc-700 dark:text-zinc-200">Total Tagihan</span>
                    <span class="text-2xl font-extrabold font-mono text-purple-800 dark:text-purple-300">
                        Rp {{ number_format($labOrder->total_tariff, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Result Input Table --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        <div class="flex items-center gap-2 p-6 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.beaker class="w-5 h-5 text-purple-500" />
            <flux:heading size="lg" class="font-bold">Input Nilai Hasil Pemeriksaan</flux:heading>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-950/40 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Nama Pemeriksaan</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Nilai Rujukan</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Tarif</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider w-52">Nilai Hasil <span class="text-red-400 font-normal">(Isi di sini)</span></th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Abnormal?</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($resultRows as $idx => $row)
                    <tr wire:key="result-{{ $idx }}" class="{{ $row['is_abnormal'] ? 'bg-red-50/50 dark:bg-red-950/10' : '' }}">
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $row['test_name'] }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-zinc-600 dark:text-zinc-300">
                            {{ $row['normal_range'] ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-zinc-500">
                            {{ $row['unit'] ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-bold font-mono text-purple-700 dark:text-purple-400 text-xs">
                            Rp {{ number_format($row['tariff'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3">
                            @if (! $isFinalized)
                            <flux:input
                                wire:model="resultRows.{{ $idx }}.result_value"
                                placeholder="Masukkan nilai..."
                                size="sm"
                                class="w-full"
                            />
                            @else
                            <span class="{{ $row['is_abnormal'] ? 'text-red-600 dark:text-red-400 font-bold' : 'text-green-700 dark:text-green-400 font-bold' }} font-mono">
                                {{ $row['result_value'] ?: '—' }}
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if (! $isFinalized)
                            <flux:checkbox wire:model="resultRows.{{ $idx }}.is_abnormal" />
                            @else
                            @if ($row['is_abnormal'])
                            <flux:badge color="red" size="sm">Abnormal</flux:badge>
                            @else
                            <flux:badge color="green" size="sm">Normal</flux:badge>
                            @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Action Bar --}}
    @if (! $isFinalized)
    <div class="flex flex-col sm:flex-row justify-end gap-3 bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
        <div class="flex-1 flex items-center gap-2 text-xs text-zinc-500">
            <flux:icon.information-circle class="w-4 h-4" />
            <span>Draft menyimpan progress tanpa mengunci hasil. Finalisasi akan mengunci form dan menandai order sebagai Selesai.</span>
        </div>
        <div class="flex gap-3">
            <flux:button variant="filled" icon="document" wire:click="saveDraft" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="saveDraft">Simpan sebagai Draft</span>
                <span wire:loading wire:target="saveDraft">Menyimpan...</span>
            </flux:button>
            <flux:button variant="primary" icon="check-circle" wire:click="finalize" wire:loading.attr="disabled"
                wire:confirm="Finalisasi hasil lab tidak dapat dibatalkan. Lanjutkan?">
                <span wire:loading.remove wire:target="finalize">Finalisasi Hasil Lab</span>
                <span wire:loading wire:target="finalize">Memfinalisasi...</span>
            </flux:button>
        </div>
    </div>
    @else
    <div class="flex items-center justify-center gap-3 bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/40 p-6 rounded-xl">
        <flux:icon.check-badge class="w-8 h-8 text-green-600" />
        <div>
            <div class="font-bold text-green-800 dark:text-green-300">Hasil Lab Telah Difinalisasi</div>
            <div class="text-sm text-green-600 dark:text-green-400">Pemeriksaan ini telah selesai dan tidak dapat diubah lagi.</div>
        </div>
    </div>
    @endif
</div>
