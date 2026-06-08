<x-master.layout>
    <x-slot:heading>Master Tindakan</x-slot:heading>
    <x-slot:subheading>Kelola jenis tindakan medis, tarif layanan, dan pemetaan kode ICD-9 CM.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama tindakan atau kode ICD-9..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-tindakan" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-tindakan" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'nama_tindakan'" :direction="$sortDirection" wire:click="sortBy('nama_tindakan')">Nama Tindakan</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'tarif'" :direction="$sortDirection" wire:click="sortBy('tarif')">Tarif</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'kode_icd9'" :direction="$sortDirection" wire:click="sortBy('kode_icd9')">Kode ICD-9</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_icd9'" :direction="$sortDirection" wire:click="sortBy('nama_icd9')">Nama ICD-9</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_aktif'" :direction="$sortDirection" wire:click="sortBy('is_aktif')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($tindakans as $tindakan)
                            <flux:table.row :key="$tindakan->id">
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $tindakan->nama_tindakan }}</flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($tindakan->tarif, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs">{{ $tindakan->kode_icd9 ?? '-' }}</flux:table.cell>
                                <flux:table.cell>{{ $tindakan->nama_icd9 ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($tindakan->is_aktif)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $tindakan->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $tindakan->id }})" wire:confirm="Apakah Anda yakin ingin menghapus tindakan ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data tindakan ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $tindakans->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Tindakan' : 'Tambah Tindakan Baru' }}</flux:heading>
                    <flux:subheading>Tuliskan detail data tindakan medis di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="nama_tindakan" label="Nama Tindakan" required placeholder="Contoh: Pemeriksaan Fisik Umum" />
                    <flux:input type="number" step="0.01" wire:model="tarif" label="Tarif (Rp)" required placeholder="Contoh: 50000" />

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_icd9" label="Kode ICD-9" placeholder="Contoh: 89.0" />
                        <flux:input wire:model="nama_icd9" label="Nama ICD-9" placeholder="Contoh: Evaluation" />
                    </div>

                    <flux:select wire:model="is_aktif" label="Status Keaktifan">
                        <flux:select.option value="1">Aktif</flux:select.option>
                        <flux:select.option value="0">Non-Aktif</flux:select.option>
                    </flux:select>

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
