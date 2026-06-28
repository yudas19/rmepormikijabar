<x-master.layout>
    <x-slot:heading>Master Laboratorium</x-slot:heading>
    <x-slot:subheading>Kelola pemeriksaan laboratorium, nilai rujukan, satuan, dan tarif pelayanan.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pemeriksaan..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-lab" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-lab" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'test_name'" :direction="$sortDirection" wire:click="sortBy('test_name')">Nama Pemeriksaan</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'default_normal_range'" :direction="$sortDirection" wire:click="sortBy('default_normal_range')">Nilai Normal</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'default_unit'" :direction="$sortDirection" wire:click="sortBy('default_unit')">Satuan</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'tarif_bpjs'" :direction="$sortDirection" wire:click="sortBy('tarif_bpjs')">Tarif BPJS</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'tarif_umum'" :direction="$sortDirection" wire:click="sortBy('tarif_umum')">Tarif Umum</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_aktif'" :direction="$sortDirection" wire:click="sortBy('is_aktif')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($labs as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->test_name }}</flux:table.cell>
                                <flux:table.cell>{{ $item->default_normal_range }}</flux:table.cell>
                                <flux:table.cell class="text-zinc-600 dark:text-zinc-400">{{ $item->default_unit }}</flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($item->tarif_bpjs, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell>Rp {{ number_format($item->tarif_umum, 0, ',', '.') }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($item->is_aktif)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus pemeriksaan lab ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data pemeriksaan lab ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $labs->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedLabTestId ? 'Edit Pemeriksaan' : 'Tambah Pemeriksaan Baru' }}</flux:heading>
                    <flux:subheading>Tuliskan detail pemeriksaan laboratorium klinis di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="test_name" label="Nama Pemeriksaan" required placeholder="Contoh: Hemoglobin (Hb)" />

                    <flux:input wire:model="category" label="Kategori" required placeholder="Contoh: Darah Lengkap / Urine" />

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="default_normal_range" label="Nilai Normal Rujukan" required placeholder="Contoh: 12 - 16" />
                        <flux:input wire:model="default_unit" label="Satuan" required placeholder="Contoh: g/dL" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input type="number" step="1" wire:model="tarif_bpjs" label="Tarif BPJS (Rp)" required placeholder="Tarif BPJS" />
                        <flux:input type="number" step="1" wire:model="tarif_umum" label="Tarif Umum (Rp)" required placeholder="Tarif Umum" />
                    </div>

                    <flux:select wire:model="is_active" label="Status Keaktifan">
                        <flux:select.option value="1">Aktif</flux:select.option>
                        <flux:select.option value="0">Non-Aktif</flux:select.option>
                    </flux:select>

                    <div class="flex gap-2 pt-2">
                        <flux:button type="submit" variant="primary" class="flex-1">Simpan</flux:button>
                        @if ($selectedLabTestId)
                            <flux:button type="button" variant="filled" wire:click="resetForm">Batal</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-master.layout>