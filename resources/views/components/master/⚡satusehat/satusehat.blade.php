<x-master.layout>
    <x-slot:heading>Master SatuSehat Kemenkes</x-slot:heading>
    <x-slot:subheading>Kelola konfigurasi organisasi, kredensial API, dan pemetaan poliklinik dengan SatuSehat Kemenkes.</x-slot:subheading>

    <!-- Tab Navigation -->
    <div class="flex gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-2 mb-6">
        <button type="button" wire:click="$set('activeTab', 'config')" class="text-sm font-semibold pb-2 px-1 border-b-2 transition duration-200 {{ $activeTab === 'config' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            Konfigurasi API
        </button>
        <button type="button" wire:click="$set('activeTab', 'poli')" class="text-sm font-semibold pb-2 px-1 border-b-2 transition duration-200 {{ $activeTab === 'poli' ? 'border-indigo-600 text-indigo-600 dark:text-indigo-400 dark:border-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
            Pemetaan Poliklinik
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="{{ $activeTab === 'config' ? 'Cari Client ID / Org ID / Env...' : 'Cari Poliklinik / Location ID...' }}" icon="magnifying-glass" class="w-full" />
                
                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-satusehat" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-satusehat" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                @if ($activeTab === 'config')
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column sortable :sorted="$sortField === 'id'" :direction="$sortDirection" wire:click="sortBy('id')">ID</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'environment'" :direction="$sortDirection" wire:click="sortBy('environment')">Environment</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'client_id'" :direction="$sortDirection" wire:click="sortBy('client_id')">Client ID</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'organization_id'" :direction="$sortDirection" wire:click="sortBy('organization_id')">Organization ID</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'is_active'" :direction="$sortDirection" wire:click="sortBy('is_active')">Status</flux:table.column>
                            <flux:table.column>Aksi</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($satusehats as $item)
                                <flux:table.row :key="'config-'.$item->id">
                                    <flux:table.cell class="font-mono text-xs">{{ $item->id }}</flux:table.cell>
                                    <flux:table.cell>
                                        <flux:badge color="{{ $item->environment === 'production' ? 'indigo' : 'zinc' }}" size="sm">
                                            {{ strtoupper($item->environment) }}
                                        </flux:badge>
                                    </flux:table.cell>
                                    <flux:table.cell class="font-mono text-xs">{{ $item->client_id }}</flux:table.cell>
                                    <flux:table.cell class="font-mono text-xs">{{ $item->organization_id }}</flux:table.cell>
                                    <flux:table.cell>
                                        @if ($item->is_active)
                                            <flux:badge color="green" size="sm">Aktif</flux:badge>
                                        @else
                                            <flux:badge color="red" size="sm">Non-Aktif</flux:badge>
                                        @endif
                                    </flux:table.cell>
                                    <flux:table.cell class="flex items-center gap-1">
                                        <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                        <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus konfigurasi SatuSehat ini?" />
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data konfigurasi SatuSehat ditemukan.</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @else
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column sortable :sorted="$sortField === 'id'" :direction="$sortDirection" wire:click="sortBy('id')">ID</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'master_polis.kode_poli'" :direction="$sortDirection" wire:click="sortBy('master_polis.kode_poli')">Kode Poli</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'master_polis.nama_poli'" :direction="$sortDirection" wire:click="sortBy('master_polis.nama_poli')">Nama Poliklinik</flux:table.column>
                            <flux:table.column sortable :sorted="$sortField === 'satusehat_location_id'" :direction="$sortDirection" wire:click="sortBy('satusehat_location_id')">SatuSehat Location ID</flux:table.column>
                            <flux:table.column>Aksi</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($satusehats as $item)
                                <flux:table.row :key="'poli-'.$item->id">
                                    <flux:table.cell class="font-mono text-xs">{{ $item->id }}</flux:table.cell>
                                    <flux:table.cell class="font-mono text-xs">{{ $item->masterPoli->kode_poli ?? '-' }}</flux:table.cell>
                                    <flux:table.cell class="font-medium text-zinc-950 dark:text-white">{{ $item->masterPoli->nama_poli ?? '-' }}</flux:table.cell>
                                    <flux:table.cell class="font-mono text-xs">{{ $item->satusehat_location_id }}</flux:table.cell>
                                    <flux:table.cell class="flex items-center gap-1">
                                        <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="edit({{ $item->id }})" />
                                        <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus pemetaan Poli SatuSehat ini?" />
                                    </flux:table.cell>
                                </flux:table.row>
                            @empty
                                <flux:table.row>
                                    <flux:table.cell colspan="5" class="text-center text-zinc-500 py-8">Tidak ada data pemetaan poliklinik ditemukan.</flux:table.cell>
                                </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                @endif
            </div>

            <!-- Pagination -->
            <div>
                {{ $satusehats->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    @if ($activeTab === 'config')
                        <flux:heading size="lg">{{ $selectedId ? 'Edit Konfigurasi' : 'Tambah Konfigurasi' }}</flux:heading>
                        <flux:subheading>Atur kredensial & environment API SatuSehat.</flux:subheading>
                    @else
                        <flux:heading size="lg">{{ $selectedId ? 'Edit Pemetaan' : 'Tambah Pemetaan' }}</flux:heading>
                        <flux:subheading>Petakan Poliklinik ke SatuSehat Location ID.</flux:subheading>
                    @endif
                </div>

                <form wire:submit.prevent="save" class="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
                    @if ($activeTab === 'config')
                        <flux:select wire:model="environment" label="Environment" required>
                            <flux:select.option value="sandbox">Sandbox (Development)</flux:select.option>
                            <flux:select.option value="production">Production</flux:select.option>
                        </flux:select>
                        
                        <flux:input wire:model="client_id" label="Client ID" required placeholder="Masukkan Client ID" />
                        <flux:input wire:model="client_secret" label="Client Secret" required placeholder="Masukkan Client Secret" />
                        <flux:input wire:model="organization_id" label="Organization ID" required placeholder="Masukkan Organization ID" />

                        <flux:select wire:model="is_active" label="Status Keaktifan" required>
                            <flux:select.option value="1">Aktif</flux:select.option>
                            <flux:select.option value="0">Non-Aktif</flux:select.option>
                        </flux:select>
                    @else
                        <flux:select wire:model="master_poli_id" label="Poliklinik" required placeholder="Pilih Poliklinik">
                            @foreach ($polis as $poli)
                                <flux:select.option value="{{ $poli->id }}">{{ $poli->kode_poli }} - {{ $poli->nama_poli }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:input wire:model="satusehat_location_id" label="SatuSehat Location ID" required placeholder="Masukkan Location ID" />
                    @endif

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