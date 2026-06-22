<div class="py-6 px-6 space-y-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">ARSIP SURAT KETERANGAN</flux:heading>
                    <flux:badge color="indigo" size="md">Dokumen Medis</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">Log riwayat penerbitan Surat Keterangan, Surat Persetujuan (Consent), dan Surat Rujukan.</flux:subheading>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex border-b border-zinc-200 dark:border-zinc-800">
        <button wire:click="$set('activeTab', 'keterangan')" class="px-6 py-3 font-semibold text-sm border-b-2 transition {{ $activeTab === 'keterangan' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            Surat Keterangan
        </button>
        <button wire:click="$set('activeTab', 'persetujuan')" class="px-6 py-3 font-semibold text-sm border-b-2 transition {{ $activeTab === 'persetujuan' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            Surat Persetujuan (Consent)
        </button>
        <button wire:click="$set('activeTab', 'rujukan')" class="px-6 py-3 font-semibold text-sm border-b-2 transition {{ $activeTab === 'rujukan' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400 font-bold' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            Surat Rujukan
        </button>
    </div>

    {{-- Filter & Actions Card --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm p-4 flex flex-wrap gap-4 items-end">
        <div class="flex-grow min-w-56">
            <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari nama pasien atau No. RM..." icon="magnifying-glass" size="sm" />
        </div>
        <div class="min-w-44">
            <flux:input type="date" wire:model.live="dateFilter" size="sm" label="Tanggal Dibuat" />
        </div>
        <div>
            @if ($searchQuery || $dateFilter)
            <flux:button variant="ghost" size="sm" wire:click="resetFilters" icon="arrow-path">Reset Filter</flux:button>
            @endif
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden pl-4">
        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal Dibuat</flux:table.column>
                <flux:table.column>Nomor Surat</flux:table.column>
                <flux:table.column>Nama Pasien</flux:table.column>
                @if ($activeTab === 'keterangan')
                    <flux:table.column>Jenis Surat</flux:table.column>
                    <flux:table.column>Dokter Penandatangan</flux:table.column>
                @elseif ($activeTab === 'persetujuan')
                    <flux:table.column>Jenis Persetujuan</flux:table.column>
                    <flux:table.column>Petugas Saksi</flux:table.column>
                @else
                    <flux:table.column>Faskes Tujuan</flux:table.column>
                    <flux:table.column>Dokter Perujuk</flux:table.column>
                @endif
                <flux:table.column class="text-right">Aksi</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($letters as $letter)
                <flux:table.row wire:key="letter-{{ $letter->id }}">
                    <flux:table.cell class="font-mono text-xs text-zinc-500">
                        {{ $letter->created_at->format('d-m-Y H:i') }}
                    </flux:table.cell>
                    <flux:table.cell class="font-bold text-zinc-900 dark:text-white font-mono">
                        {{ $letter->nomor_surat ?? $letter->no_surat }}
                    </flux:table.cell>
                    <flux:table.cell>
                        @php
                            $pasien = $activeTab === 'persetujuan' ? ($letter->pendaftaran?->pasien ?? null) : $letter->pasien;
                        @endphp
                        <div class="font-semibold text-zinc-950 dark:text-white">{{ $pasien->nama_pasien ?? '-' }}</div>
                        <div class="text-xs text-zinc-400 font-mono mt-0.5">{{ $pasien->no_rekam_medis ?? '-' }}</div>
                    </flux:table.cell>
                    @if ($activeTab === 'keterangan')
                        <flux:table.cell>
                            @if ($letter->jenis_surat === 'surat_sakit')
                                <flux:badge color="red" size="sm" class="font-semibold">Surat Sakit</flux:badge>
                            @else
                                <flux:badge color="green" size="sm" class="font-semibold">Surat Sehat</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-800 dark:text-zinc-200">
                            dr. {{ $letter->dokter->nama_petugas ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button variant="primary" size="sm" icon="printer" href="{{ route('medical-letters.print', $letter->id) }}" target="_blank">
                                Cetak
                            </flux:button>
                        </flux:table.cell>
                    @elseif ($activeTab === 'persetujuan')
                        <flux:table.cell>
                            @if ($letter->jenis_persetujuan === 'general_consent')
                                <flux:badge color="indigo" size="sm" class="font-semibold">General Consent</flux:badge>
                            @else
                                <flux:badge color="purple" size="sm" class="font-semibold">Informed Consent</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-800 dark:text-zinc-200">
                            {{ $letter->petugas->nama_petugas ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button variant="primary" size="sm" icon="printer" href="{{ route('print.consent', $letter->id) }}" target="_blank">
                                Cetak
                            </flux:button>
                        </flux:table.cell>
                    @else
                        <flux:table.cell class="font-semibold text-zinc-800 dark:text-zinc-200">
                            {{ $letter->faskes_tujuan ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="font-medium text-zinc-800 dark:text-zinc-200">
                            dr. {{ $letter->dokter->nama_petugas ?? '-' }}
                        </flux:table.cell>
                        <flux:table.cell class="text-right">
                            <flux:button variant="primary" size="sm" icon="printer" href="{{ route('print.referral', $letter->id) }}" target="_blank">
                                Cetak
                            </flux:button>
                        </flux:table.cell>
                    @endif
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="6" class="text-center py-16">
                        <flux:icon.document-text class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-3" />
                        <div class="text-sm font-semibold text-zinc-400">Belum ada arsip dokumen ditemukan</div>
                        <div class="text-xs text-zinc-300 dark:text-zinc-600 mt-1">Dokumen medis akan muncul setelah dibuat di loket atau ruang periksa.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($letters->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $letters->links() }}
        </div>
        @endif
    </div>
</div>
