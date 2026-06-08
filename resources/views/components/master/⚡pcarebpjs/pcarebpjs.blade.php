<x-master.layout>
    <x-slot:heading>Master PCare BPJS</x-slot:heading>
    <x-slot:subheading>Kelola konfigurasi faskes, kode regional, dan parameter bridging BPJS PCare.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode/nama pcare..." icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-pcare" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-pcare" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'kode_pcare'" :direction="$sortDirection" wire:click="sortBy('kode_pcare')">Kode PCare</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_pcare'" :direction="$sortDirection" wire:click="sortBy('nama_pcare')">Nama PCare</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'kode_faskes'" :direction="$sortDirection" wire:click="sortBy('kode_faskes')">Kode Faskes</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_faskes'" :direction="$sortDirection" wire:click="sortBy('nama_faskes')">Nama Faskes</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_active'" :direction="$sortDirection" wire:click="sortBy('is_active')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($pcares as $item)
                            <flux:table.row :key="$item->id">
                                <flux:table.cell class="font-mono text-xs">{{ $item->kode_pcare }}</flux:table.cell>
                                <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->nama_pcare }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs">{{ $item->kode_faskes ?? '-' }}</flux:table.cell>
                                <flux:table.cell>{{ $item->nama_faskes ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($item->is_active)
                                        <flux:badge color="green" size="sm">Aktif</flux:badge>
                                    @else
                                        <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="flex items-center gap-1">
                                    <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                    <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus konfigurasi PCare ini?" />
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data konfigurasi PCare ditemukan.</flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $pcares->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit PCare' : 'Tambah PCare Baru' }}</flux:heading>
                    <flux:subheading>Atur parameter integrasi BPJS PCare di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    <flux:input wire:model="kode_pcare" label="Kode PCare" required placeholder="Contoh: PCR-001" />
                    <flux:input wire:model="nama_pcare" label="Nama Konfigurasi" required placeholder="Contoh: PCare Klinik" />
                    <flux:input wire:model="kode_faskes" label="Kode Faskes BPJS" placeholder="Contoh: 0112B001" />
                    <flux:input wire:model="nama_faskes" label="Nama Faskes BPJS" placeholder="Nama Faskes" />
                    
                    <flux:input wire:model="kode_rs" label="Kode RS Rujukan" placeholder="Kode RS" />
                    <flux:input wire:model="kode_wilayah" label="Kode Wilayah" placeholder="Kode Wilayah" />
                    
                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_provinsi" label="Kode Prov" placeholder="Kode Provinsi" />
                        <flux:input wire:model="nama_propinsi" label="Nama Prov" placeholder="Nama Provinsi" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_kabupaten" label="Kode Kab" placeholder="Kode Kab/Kota" />
                        <flux:input wire:model="nama_kabupaten" label="Nama Kab" placeholder="Nama Kab/Kota" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_kecamatan" label="Kode Kec" placeholder="Kode Kecamatan" />
                        <flux:input wire:model="nama_kecamatan" label="Nama Kec" placeholder="Nama Kecamatan" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="jenis_faskes" label="Jenis Faskes" placeholder="Klinik" />
                        <flux:input wire:model="tipe_faskes" label="Tipe Faskes" placeholder="Pratama" />
                    </div>

                    <flux:input wire:model="tipe_layanan" label="Tipe Layanan" placeholder="Rawat Jalan" />
                    <flux:input wire:model="telepon" label="Telepon" placeholder="Telepon Faskes" />
                    <flux:input type="email" wire:model="email" label="Email" placeholder="Email Faskes" />
                    <flux:input wire:model="alamat" label="Alamat" placeholder="Alamat Faskes" />

                    <div class="grid grid-cols-2 gap-3">
                        <flux:select wire:model="is_bpjs" label="Status BPJS">
                            <flux:select.option value="1">Ya</flux:select.option>
                            <flux:select.option value="0">Tidak</flux:select.option>
                        </flux:select>
                        <flux:select wire:model="is_active" label="Status Keaktifan">
                            <flux:select.option value="1">Aktif</flux:select.option>
                            <flux:select.option value="0">Non-Aktif</flux:select.option>
                        </flux:select>
                    </div>

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