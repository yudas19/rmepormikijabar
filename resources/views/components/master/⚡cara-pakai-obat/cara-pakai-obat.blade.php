<x-master.layout>
    <x-slot:heading>Master Cara Pakai Obat</x-slot:heading>
    <x-slot:subheading>Kelola pilihan aturan pakai obat (dosis/instruksi konsumsi) untuk penulisan resep.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari cara pakai..." icon="magnifying-glass" class="w-full" />

                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-cara-pakai" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-cara-pakai" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'nama_aturan_pakai'" :direction="$sortDirection" wire:click="sortBy('nama_aturan_pakai')">Aturan Pakai Obat</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($aturanPakais as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell style="padding: 0;">
                                <span style="display: inline-block; padding-left: 16px; padding-top: 12px; padding-bottom: 12px;" class="font-medium text-zinc-950 dark:text-white">
                                    {{ $item->nama_aturan_pakai }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell style="padding: 0;">
                                <div style="display: flex; align-items: center; gap: 4px; padding: 8px 16px 8px 8px;">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus aturan pakai ini?" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                        @empty
                        <flux:table.row>
                            <flux:table.cell colspan="2" class="text-center text-zinc-500 py-8">
                                Tidak ada data aturan pakai ditemukan.
                            </flux:table.cell>
                        </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $aturanPakais->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Cara Pakai' : 'Tambah Cara Pakai' }}</flux:heading>
                    <flux:subheading>Tuliskan instruksi pemakaian obat di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="nama_aturan_pakai" label="Cara Pakai / Aturan Pakai" required placeholder="Contoh: 3 x 1 Tablet Sehari (Sesudah Makan)" />

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