<x-master.layout>
    <x-slot:heading>Master Jadwal Dokter</x-slot:heading>
    <x-slot:subheading>Kelola jadwal praktik dokter, kuota pasien, dan jam operasional layanan poliklinik klinik.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Search -->
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama dokter, poli, atau hari..." icon="magnifying-glass" class="w-full" />

            <!-- Schedules grouped by Polyclinic -->
            <div class="space-y-6">
                @forelse ($groupedSchedules as $poliName => $schedules)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                        <!-- Group Header -->
                        <div class="px-5 py-3.5 bg-zinc-50/50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-zinc-800 flex items-center justify-between">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-white flex items-center gap-2">
                                <span>🩺</span> {{ $poliName }}
                            </h4>
                            <flux:badge size="sm" color="indigo">{{ $schedules->count() }} Jadwal</flux:badge>
                        </div>

                        <!-- Schedules Table -->
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column>Nama Dokter</flux:table.column>
                                <flux:table.column>Hari</flux:table.column>
                                <flux:table.column>Jam Praktik</flux:table.column>
                                <flux:table.column>Kuota</flux:table.column>
                                <flux:table.column>Aksi</flux:table.column>
                            </flux:table.columns>

                            <flux:table.rows>
                                @foreach ($schedules as $item)
                                    <flux:table.row :key="$item->id">
                                        <flux:table.cell class="font-medium">{{ $item->petugas->nama_petugas }}</flux:table.cell>
                                        <flux:table.cell>{{ $item->hari }}</flux:table.cell>
                                        <flux:table.cell>
                                            <span class="font-mono text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">
                                                {{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}
                                            </span>
                                        </flux:table.cell>
                                        <flux:table.cell>{{ $item->kuota_pasien }} Pasien</flux:table.cell>
                                        <flux:table.cell>
                                            <div class="flex items-center gap-2">
                                                <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $item->id }})" />
                                                <flux:button size="sm" variant="ghost" icon="trash" color="red" wire:click="delete({{ $item->id }})" />
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    </div>
                @empty
                    <div class="p-8 text-center bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl">
                        <flux:heading>Belum Ada Jadwal</flux:heading>
                        <flux:subheading>Tidak ditemukan jadwal dokter yang sesuai dengan kriteria.</flux:subheading>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Form (Right 1/3) -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Jadwal Dokter' : 'Tambah Jadwal Dokter' }}</flux:heading>
                    <flux:subheading>Atur jadwal praktik, poliklinik, dan kuota harian dokter.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4 mt-4">
                    <flux:select wire:model="petugas_id" label="Pilih Dokter" required placeholder="Pilih dokter...">
                        @foreach ($doctors as $doc)
                            <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="poli_id" label="Pilih Poliklinik" required placeholder="Pilih poliklinik...">
                        @foreach ($polis as $poli)
                            <flux:select.option value="{{ $poli->id }}">{{ $poli->nama_poli }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="hari" label="Hari Praktik" required>
                        <flux:select.option value="Senin">Senin</flux:select.option>
                        <flux:select.option value="Selasa">Selasa</flux:select.option>
                        <flux:select.option value="Rabu">Rabu</flux:select.option>
                        <flux:select.option value="Kamis">Kamis</flux:select.option>
                        <flux:select.option value="Jumat">Jumat</flux:select.option>
                        <flux:select.option value="Sabtu">Sabtu</flux:select.option>
                        <flux:select.option value="Minggu">Minggu</flux:select.option>
                    </flux:select>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="jam_mulai" type="time" label="Jam Mulai" required />
                        <flux:input wire:model="jam_selesai" type="time" label="Jam Selesai" required />
                    </div>

                    <flux:input wire:model="kuota_pasien" type="number" min="1" label="Kuota Pasien (Harian)" required />

                    <div class="flex gap-2 pt-2">
                        <flux:button type="submit" variant="primary" class="flex-1">Simpan</flux:button>
                        @if ($selectedId)
                            <flux:button type="button" variant="filled" wire:click="resetForm">Batal</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-master.layout>