<x-master.layout>
    <x-slot:heading>Master Poliklinik</x-slot:heading>
    <x-slot:subheading>Kelola poliklinik medis, pemetaan jaminan BPJS Kesehatan, dan ID lokasi SatuSehat.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama poli..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-poli" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-poli" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'kode_poli'" :direction="$sortDirection" wire:click="sortBy('kode_poli')">Kode Poli</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_poli'" :direction="$sortDirection" wire:click="sortBy('nama_poli')">Nama Poliklinik</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'kode_poli_bpjs'" :direction="$sortDirection" wire:click="sortBy('kode_poli_bpjs')">Kode BPJS</flux:table.column>
                        <flux:table.column>ID Lokasi SatuSehat</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_active'" :direction="$sortDirection" wire:click="sortBy('is_active')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($polis as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell class="font-mono text-xs font-semibold">{{ $item->kode_poli }}</flux:table.cell>
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->nama_poli }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs">{{ $item->kode_poli_bpjs ?? '-' }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-400 truncate max-w-xs">{{ $item->satu_sehat_location_id ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($item->is_active)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus poliklinik ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data poliklinik ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $polis->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Poliklinik' : 'Tambah Poliklinik Baru' }}</flux:heading>
                    <flux:subheading>Tuliskan detail data poliklinik medis di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="kode_poli" label="Kode Poliklinik" required placeholder="Contoh: POL-UMUM" />
                    <flux:input wire:model="nama_poli" label="Nama Poliklinik" required placeholder="Contoh: Poli Umum" />

                    <div class="grid grid-cols-1 gap-3">
                        <flux:input wire:model="kode_poli_bpjs" label="Kode Poli BPJS (HFIS)" placeholder="Contoh: 001" />
                        <flux:input wire:model="satu_sehat_location_id" label="Location ID SatuSehat" placeholder="Contoh: 10000003" />
                    </div>

                    <flux:select wire:model="is_active" label="Status Keaktifan">
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