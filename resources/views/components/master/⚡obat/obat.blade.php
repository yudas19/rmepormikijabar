<x-master.layout>
    <x-slot:heading>Master Obat</x-slot:heading>
    <x-slot:subheading>Kelola persediaan obat, satuan kemasan, harga beli/jual, serta kode KFA Kemenkes.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari obat atau kode KFA..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-obat" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-obat" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'nama_obat'" :direction="$sortDirection" wire:click="sortBy('nama_obat')">Nama Obat</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'satuan'" :direction="$sortDirection" wire:click="sortBy('satuan')">Satuan</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'stok'" :direction="$sortDirection" wire:click="sortBy('stok')">Stok</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'harga_jual'" :direction="$sortDirection" wire:click="sortBy('harga_jual')">Harga Jual</flux:table.column>
                        <flux:table.column>KFA Kemenkes</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_aktif'" :direction="$sortDirection" wire:click="sortBy('is_aktif')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($obats as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->nama_obat }}</flux:table.cell>
                                <flux:table.cell>{{ $item->satuan }}</flux:table.cell>
                                <flux:table.cell>{{ $item->stok }}</flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell>
                                    @if($item->kode_kfa)
                                        <div class="text-[10px] truncate max-w-xs">
                                            <span class="font-mono font-semibold">{{ $item->kode_kfa }}</span>
                                            <div class="text-zinc-500">{{ $item->nama_kfa }}</div>
                                        </div>
                                    @else
                                        <span class="text-zinc-400 text-xs">-</span>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell>
                                    @if ($item->is_aktif)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus obat ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="text-center text-zinc-500 py-8">Tidak ada data obat ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $obats->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Obat' : 'Tambah Obat Baru' }}</flux:heading>
                    <flux:subheading>Tuliskan detail sediaan obat di bawah ini.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="nama_obat" label="Nama Obat" required placeholder="Contoh: Paracetamol 500mg" />

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="satuan" label="Satuan" required placeholder="Contoh: Tablet" />
                        <flux:input type="number" wire:model="stok" label="Stok Awal" required placeholder="Contoh: 100" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input type="number" step="0.01" wire:model="harga_beli" label="Harga Beli (Rp)" required placeholder="Contoh: 500" />
                        <flux:input type="number" step="0.01" wire:model="harga_jual" label="Harga Jual (Rp)" required placeholder="Contoh: 1000" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_kfa" label="Kode KFA Kemenkes" placeholder="Contoh: 93000123" />
                        <flux:input wire:model="nama_kfa" label="Nama KFA" placeholder="Nama Resmi KFA" />
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