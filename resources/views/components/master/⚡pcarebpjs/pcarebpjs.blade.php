<x-master.layout>
    <x-slot:heading>Master PCare BPJS</x-slot:heading>
    <x-slot:subheading>Kelola profil integrasi dan kredensial bridging BPJS PCare untuk faskes Anda.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kode, nama konfigurasi, atau faskes..." icon="magnifying-glass" class="w-full" />
                
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
                        <flux:table.column sortable :sorted="$sortField === 'nama_pcare'" :direction="$sortDirection" wire:click="sortBy('nama_pcare')">Nama Konfigurasi</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'kode_faskes'" :direction="$sortDirection" wire:click="sortBy('kode_faskes')">Kode Faskes BPJS</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nama_faskes'" :direction="$sortDirection" wire:click="sortBy('nama_faskes')">Nama Faskes BPJS</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'bpjs_env'" :direction="$sortDirection" wire:click="sortBy('bpjs_env')">Environment</flux:table.column>
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
                                    @if (($item->bpjs_env ?? 'development') === 'production')
                                        <flux:badge color="purple" size="sm">Production</flux:badge>
                                    @else
                                        <flux:badge color="zinc" size="sm">Development</flux:badge>
                                    @endif
                                </flux:table.cell>
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
                                <flux:table.cell colspan="7" class="text-center text-zinc-500 py-8">Tidak ada data konfigurasi PCare ditemukan.</flux:table.cell>
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
                    <flux:heading size="lg">{{ $selectedPcareId ? 'Edit PCare' : 'Tambah PCare Baru' }}</flux:heading>
                    <flux:subheading>Atur parameter integrasi BPJS PCare di bawah.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4 max-h-[75vh] overflow-y-auto pr-1">
                    <flux:input wire:model="nama_pcare" label="Nama Konfigurasi" required placeholder="Contoh: Klinik Utama Dev Mode" />
                    
                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="kode_pcare" label="Kode PCare" required placeholder="Contoh: PCR-001" />
                        <flux:input wire:model="kode_faskes" label="Kode Faskes BPJS" placeholder="Contoh: 0112B001" />
                    </div>

                    <flux:input wire:model="nama_faskes" label="Nama Faskes BPJS" placeholder="Nama Faskes" />

                    <flux:select wire:model="bpjs_env" label="BPJS Environment">
                        <flux:select.option value="development">Development / Trust-Mark</flux:select.option>
                        <flux:select.option value="production">Production Mode</flux:select.option>
                    </flux:select>

                    <div class="border-t border-zinc-200 dark:border-zinc-800 my-4 pt-3">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">BPJS Credentials & Security Keys</span>
                    </div>

                    <flux:input wire:model="bpjs_cons_id" label="BPJS Consumer ID (Cons ID)" placeholder="Cons ID (12345)" />
                    <flux:input type="password" wire:model="bpjs_secret_key" label="BPJS Secret Key" placeholder="Secret Key" />
                    <flux:input type="password" wire:model="bpjs_user_key" label="BPJS User Key (V2/V3 Portal)" placeholder="User Key" />

                    <div class="border-t border-zinc-200 dark:border-zinc-800 my-4 pt-3">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">PCare Application Credentials</span>
                    </div>

                    <flux:input wire:model="pcare_username" label="PCare Username" placeholder="Username Aplikasi PCare" />
                    <flux:input type="password" wire:model="pcare_password" label="PCare Password" placeholder="Password Aplikasi PCare" />

                    <div class="border-t border-zinc-200 dark:border-zinc-800 my-4 pt-3">
                        <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">MJKN Antrol (Optional Block)</span>
                    </div>

                    <flux:input wire:model="user_mjkn" label="Antrol MJKN Config" placeholder="Username / Config parameter" />

                    <div class="grid grid-cols-2 gap-3 border-t border-zinc-200 dark:border-zinc-800 pt-3">
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
                        @if ($selectedPcareId)
                            <flux:button type="button" variant="filled" wire:click="resetForm">Batal</flux:button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-master.layout>