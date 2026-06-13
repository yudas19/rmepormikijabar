<div>
    <x-slot:title>Pengaturan Hak Akses</x-slot:title>

    <div class="py-6">
        <div class="mb-6">
            <flux:heading size="xl" class="font-bold tracking-tight">Pengaturan Hak Akses</flux:heading>
            <flux:subheading class="mt-1">Kelola dan sinkronisasi hak akses (Spatie Permissions) untuk setiap peran petugas.</flux:subheading>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Column: Role List (4 cols) -->
            <div class="lg:col-span-4 space-y-3">
                <flux:heading level="2" size="lg" class="mb-3 font-semibold text-zinc-900 dark:text-white">Daftar Peran (Roles)</flux:heading>
                <div class="space-y-2.5">
                    @foreach ($roles as $role)
                        @php
                            $isActive = $role->id === $selectedRoleId;
                            $displayName = ucwords(str_replace('_', ' ', $role->name));
                        @endphp
                        <button 
                            wire:click="selectRole({{ $role->id }})"
                            type="button"
                            class="w-full text-left p-4 rounded-xl border transition-all duration-200 flex items-center justify-between group cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $isActive ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/30 dark:border-indigo-850' : 'bg-white border-zinc-200 dark:bg-zinc-900 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800/50' }}"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="p-2.5 rounded-lg {{ $isActive ? 'bg-indigo-100 text-indigo-600 dark:bg-indigo-900/50 dark:text-indigo-400' : 'bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 group-hover:bg-zinc-200 dark:group-hover:bg-zinc-700' }}">
                                    @switch($role->name)
                                        @case('admin')
                                            <flux:icon.check-badge class="w-5 h-5" />
                                            @break
                                        @case('rekam_medis')
                                            <flux:icon.document-text class="w-5 h-5" />
                                            @break
                                        @case('dokter_umum')
                                        @case('dokter_gigi')
                                            <flux:icon.user-circle class="w-5 h-5" />
                                            @break
                                        @case('perawat')
                                        @case('bidan')
                                            <flux:icon.users class="w-5 h-5" />
                                            @break
                                        @case('analis_lab')
                                            <flux:icon.beaker class="w-5 h-5" />
                                            @break
                                        @case('apoteker')
                                            <flux:icon.archive-box class="w-5 h-5" />
                                            @break
                                        @case('kasir')
                                            <flux:icon.credit-card class="w-5 h-5" />
                                            @break
                                        @default
                                            <flux:icon.cog class="w-5 h-5" />
                                    @endswitch
                                </div>
                                <div>
                                    <span class="block font-semibold text-sm {{ $isActive ? 'text-indigo-950 dark:text-indigo-200' : 'text-zinc-800 dark:text-zinc-200' }}">
                                        {{ $displayName }}
                                    </span>
                                    <span class="block text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                        slug: {{ $role->name }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <flux:icon.chevron-right class="w-4 h-4 transition-transform duration-200 {{ $isActive ? 'text-indigo-500 translate-x-1' : 'text-zinc-400 group-hover:translate-x-1' }}" />
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Right Column: Permission Matrix (8 cols) -->
            <div class="lg:col-span-8">
                @if ($activeRole)
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
                        <!-- Header -->
                        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <flux:badge color="indigo" size="sm" class="uppercase tracking-wider font-mono">Role Matrix</flux:badge>
                                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                                    <span class="text-xs text-zinc-500 dark:text-zinc-400 font-mono">ID: {{ $activeRole->id }}</span>
                                </div>
                                <flux:heading size="lg" class="mt-1 font-bold">
                                    Hak Akses: {{ ucwords(str_replace('_', ' ', $activeRole->name)) }}
                                </flux:heading>
                            </div>
                            <div>
                                <flux:button wire:click="save" variant="primary" icon="check" class="w-full sm:w-auto shadow-md shadow-indigo-500/10 hover:shadow-indigo-500/20">
                                    Simpan Hak Akses
                                </flux:button>
                            </div>
                        </div>

                        <!-- Matrix Body -->
                        <div class="p-6 space-y-8">
                            <form wire:submit.prevent="save" class="space-y-8">
                                @foreach ($permissionGroups as $groupName => $groupPerms)
                                    <div class="space-y-3">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 flex items-center space-x-2">
                                            <span>{{ $groupName }}</span>
                                            <span class="h-px bg-zinc-100 dark:bg-zinc-800 flex-1 ml-3"></span>
                                        </h4>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach ($groupPerms as $permName => $permLabel)
                                                <label 
                                                    class="flex items-start p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-750 bg-zinc-50/20 dark:bg-zinc-900/20 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-all duration-200 cursor-pointer select-none group"
                                                >
                                                    <div class="flex items-center h-5 mr-3 pt-0.5">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model="selectedPermissions.{{ $permName }}" 
                                                            id="perm-{{ $permName }}"
                                                            class="w-4.5 h-4.5 text-indigo-600 bg-white border-zinc-300 rounded focus:ring-indigo-500 dark:bg-zinc-800 dark:border-zinc-750 dark:focus:ring-offset-zinc-900 cursor-pointer"
                                                        />
                                                    </div>
                                                    <div class="text-sm">
                                                        <span class="font-semibold text-zinc-950 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-150">
                                                            {{ $permLabel }}
                                                        </span>
                                                        <span class="block text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">
                                                            {{ $permName }}
                                                        </span>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Action Buttons Bottom -->
                                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 flex justify-end">
                                    <flux:button type="submit" variant="primary" icon="check" class="shadow-md shadow-indigo-500/10">
                                        Simpan Hak Akses
                                    </flux:button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="border border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-12 text-center text-zinc-500 dark:text-zinc-400 bg-zinc-50/30 dark:bg-zinc-900/20">
                        <span class="text-4xl block mb-3">👈</span>
                        <flux:heading size="lg">Pilih Peran Terlebih Dahulu</flux:heading>
                        <flux:subheading class="mt-1">Pilih peran petugas di kolom kiri untuk melihat dan menyinkronkan hak akses.</flux:subheading>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>