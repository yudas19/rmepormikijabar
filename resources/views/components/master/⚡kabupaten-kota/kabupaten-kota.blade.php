<x-master.layout>
    <x-slot:heading>Master Kabupaten / Kota</x-slot:heading>
    <x-slot:subheading>Kelola data wilayah Kabupaten dan Kota untuk detail data alamat.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama kabupaten/kota..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-kabupaten-kota" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-kabupaten-kota" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'kode_kabupaten_kota'" :direction="$sortDirection" wire:click="sortBy('kode_kabupaten_kota')">Kode Wilayah</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_kabupaten_kota'" :direction="$sortDirection" wire:click="sortBy('nama_kabupaten_kota')">Nama Kabupaten / Kota</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($kabupatenKotas as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell class="font-mono text-xs">{{ $item->kode_kabupaten_kota ?? '-' }}</flux:table.cell>
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->nama_kabupaten_kota }}</flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus kabupaten/kota ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="3" class="text-center text-zinc-500 py-8">Tidak ada data kabupaten/kota ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $kabupatenKotas->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Kabupaten / Kota' : 'Tambah Kabupaten / Kota Baru' }}</flux:heading>
                    <flux:subheading>Tuliskan detail data wilayah kabupaten/kota di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="kode_kabupaten_kota" label="Kode Kabupaten / Kota (Opsional)" placeholder="Contoh: 32.73" />
                    <flux:input wire:model="nama_kabupaten_kota" label="Nama Kabupaten / Kota" required placeholder="Contoh: KOTA BANDUNG" />

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