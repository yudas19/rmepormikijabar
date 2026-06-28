<x-master.layout>
    <x-slot:heading>Master Petugas</x-slot:heading>
    <x-slot:subheading>Kelola data dokter, perawat, apoteker, dan staf administrasi klinik.</x-slot:subheading>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Table List (Left 2/3) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search & Actions -->
            <div class="flex items-center gap-4">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari petugas, NIK, atau tipe..." icon="magnifying-glass" class="w-full" />

                <div class="flex items-center gap-2">
                    <flux:button variant="ghost" icon="document-arrow-down" wire:click="exportCsv">Export</flux:button>
                    <div class="relative flex items-center">
                        <flux:button variant="ghost" icon="document-arrow-up" as="label" for="csv-upload-petugas" class="cursor-pointer">Import</flux:button>
                        <input type="file" id="csv-upload-petugas" wire:model.live="csvFile" class="sr-only" accept=".csv,text/csv" />
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="border p-2 border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column sortable :sorted="$sortField === 'nama_petugas'" :direction="$sortDirection" wire:click="sortBy('nama_petugas')">Nama</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'nik'" :direction="$sortDirection" wire:click="sortBy('nik')">NIK</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'jenis_petugas'" :direction="$sortDirection" wire:click="sortBy('jenis_petugas')">Peran</flux:table.column>
                        <flux:table.column>Role / Hak Akses</flux:table.column>
                        <flux:table.column>SIP / STR</flux:table.column>
                        <flux:table.column sortable :sorted="$sortField === 'is_aktif'" :direction="$sortDirection" wire:click="sortBy('is_aktif')">Status</flux:table.column>
                        <flux:table.column>Aksi</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @forelse ($petugass as $item)
                        <flux:table.row :key="$item->id">
                            <flux:table.cell>
                                <div class="font-medium text-zinc-950 dark:text-white">{{ $item->nama_petugas }}</div>
                                @if($item->user)
                                <div class="text-[10px] text-zinc-500">Linked: {{ $item->user->email }}</div>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="font-mono text-xs">{{ $item->nik }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge size="sm" inset="top bottom">{{ $item->jenis_petugas }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @php
                                    $roleName = $item->user?->getRoleNames()->first();
                                    $badgeColor = match($roleName) {
                                        'admin' => 'indigo',
                                        'dokter_umum', 'dokter_gigi' => 'green',
                                        'perawat', 'bidan' => 'teal',
                                        'analis_lab' => 'purple',
                                        'apoteker' => 'orange',
                                        'kasir' => 'blue',
                                        default => 'zinc',
                                    };
                                @endphp
                                @if ($roleName)
                                    <flux:badge color="{{ $badgeColor }}" size="sm" class="uppercase tracking-wider text-[10px] font-mono">{{ str_replace('_', ' ', $roleName) }}</flux:badge>
                                @else
                                    <span class="text-xs text-zinc-400 dark:text-zinc-600">-</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="text-[10px] space-y-0.5">
                                    <div>SIP: <span class="font-mono text-zinc-600 dark:text-zinc-400">{{ $item->nomor_sip ?? '-' }}</span></div>
                                    <div>STR: <span class="font-mono text-zinc-600 dark:text-zinc-400">{{ $item->nomor_str ?? '-' }}</span></div>
                                </div>
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
                                <flux:button variant="ghost" icon="trash" size="sm" wire:click="delete({{ $item->id }})" wire:confirm="Apakah Anda yakin ingin menghapus petugas ini?" />
                            </flux:table.cell>
                        </flux:table.row>
                        @empty
                        <flux:table.row>
                            <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Tidak ada data petugas ditemukan.</flux:table.cell>
                        </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            <!-- Pagination -->
            <div>
                {{ $petugass->links() }}
            </div>
        </div>

        <!-- Form Card (Right 1/3) -->
        <div>
            <div class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg p-5 shadow-sm space-y-5">
                <div>
                    <flux:heading size="lg">{{ $selectedId ? 'Edit Petugas' : 'Tambah Petugas Baru' }}</flux:heading>
                    <flux:subheading>Masukkan detail data diri dan profesi petugas.</flux:subheading>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <flux:input wire:model="nama_petugas" label="Nama Lengkap (beserta gelar)" required placeholder="Contoh: dr. Budi Santoso, Sp.PD" />
                    <flux:input wire:model="nik" label="NIK" required maxLength="16" placeholder="Nomor Kependudukan 16 digit" />

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="tempat_lahir" label="Tempat Lahir" placeholder="Contoh: Bandung" />
                        <flux:input type="date" wire:model="tanggal_lahir" label="Tanggal Lahir" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="telepon" label="Telepon" placeholder="Contoh: 022-12345" />
                        <flux:input wire:model="no_hp" label="No. Handphone" placeholder="Contoh: 08123456789" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input type="date" wire:model="bekerja_sejak" label="Mulai Bekerja" />
                        <flux:select wire:model="jenis_petugas" label="Peran Petugas" required>
                            <flux:select.option value="Dokter">Dokter</flux:select.option>
                            <flux:select.option value="Perawat">Perawat</flux:select.option>
                            <flux:select.option value="Apoteker">Apoteker</flux:select.option>
                            <flux:select.option value="Bidan">Bidan</flux:select.option>
                            <flux:select.option value="Analis">Analis</flux:select.option>
                            <flux:select.option value="Rekam Medis">Rekam Medis</flux:select.option>
                            <flux:select.option value="Staf Administrasi">Staf Administrasi</flux:select.option>
                        </flux:select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <flux:input wire:model="nomor_str" label="Nomor STR" placeholder="Nomor STR Medis" />
                        <flux:input wire:model="nomor_sip" label="Nomor SIP" placeholder="Nomor SIP Praktik" />
                    </div>

                    <div>
                        <div class="flex items-end gap-2">
                            <flux:input wire:model="ihs_number_practitioner" label="IHS Practitioner Number (SatuSehat)" placeholder="Contoh: P00012345678" class="flex-1" />
                            <flux:button type="button" variant="primary" size="sm" wire:click="verifyIhs">
                                Verifikasi IHS SatuSehat
                            </flux:button>
                        </div>
                    </div>

                    <flux:input wire:model="email" type="email" label="Email Login" required placeholder="Contoh: staff@klinik.com" />

                    <flux:input 
                        wire:model="password" 
                        type="password" 
                        label="Password" 
                        :required="!$selectedId" 
                        placeholder="{{ $selectedId ? 'Kosongkan jika tidak ingin mengubah password' : 'Min. 8 karakter' }}" 
                    />

                    <flux:select wire:model="role" label="Hak Akses / Role" required placeholder="Pilih Hak Akses...">
                        @foreach ($roles as $r)
                            @php
                                $roleDisplay = ucwords(str_replace('_', ' ', $r->name));
                            @endphp
                            <flux:select.option value="{{ $r->name }}">{{ $roleDisplay }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:input wire:model="alamat" label="Alamat Tempat Tinggal" placeholder="Alamat Lengkap" />

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