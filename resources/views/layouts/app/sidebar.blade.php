<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-slate-50 dark:bg-slate-950">
    <flux:sidebar sticky collapsible class="border-e border-slate-200/80 bg-slate-900 text-slate-200 dark:border-slate-800 dark:bg-slate-950">
        <flux:sidebar.header class="border-b border-slate-800/50 pb-4">
            <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
            <flux:sidebar.collapse class="lg:hidden" />
            <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />

        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Platform')" class="grid text-slate-400">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>
                    {{ __('Dashboard') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:sidebar.item icon="folder" :href="route('master.index')" :current="request()->routeIs('master.*') && !request()->routeIs('master.jadwal-dokter')" wire:navigate>
            {{ __('Master Data') }}
        </flux:sidebar.item>

        @can('akses_pendaftaran')
        <flux:sidebar.item icon="user-plus" :href="route('pendaftaran.index')" :current="request()->routeIs('pendaftaran.*')" wire:navigate>
            {{ __('Pendaftaran') }}
        </flux:sidebar.item>
        @endcan

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Poliklinik & Layanan')" class="grid text-slate-400">
                @if (auth()->user()->can('akses_poli_umum') || auth()->user()->can('akses_poli_gigi') || auth()->user()->can('akses_poli_kia'))
                @php
                    $totalWaiting = ($waitingCounts['umum'] ?? 0) + ($waitingCounts['gigi'] ?? 0) + ($waitingCounts['kia'] ?? 0);
                @endphp
                <flux:sidebar.item icon="building-office-2" href="{{ route('poli.queue') }}" :current="request()->is('poli*') && !request()->is('poli/*/examine/*')" wire:navigate
                    :badge="$totalWaiting > 0 ? $totalWaiting : null"
                    badge-color="red"
                    badge:variant="solid"
                    badge:class="rounded-full shadow-sm">
                    {{ __('Pemeriksaan Medis') }}
                </flux:sidebar.item>
                @endif

                @can('akses_laboratorium')
                <flux:sidebar.item icon="beaker" href="{{ route('layanan.laboratorium') }}" :current="request()->routeIs('layanan.laboratorium')" wire:navigate>
                    {{ __('Laboratorium') }}
                </flux:sidebar.item>
                @endcan

                @can('akses_farmasi')
                <flux:sidebar.item icon="academic-cap" href="{{ route('layanan.farmasi') }}" :current="request()->routeIs('layanan.farmasi')" wire:navigate>
                    {{ __('Depo Farmasi') }}
                </flux:sidebar.item>
                @endcan

                @can('akses_kasir')
                <flux:sidebar.item icon="credit-card" href="{{ route('kasir.index') }}" :current="request()->routeIs('kasir.index')" wire:navigate>
                    {{ __('Kasir / Billing') }}
                </flux:sidebar.item>
                @endcan

                <flux:sidebar.item icon="document-text" href="{{ route('admin.daftar-surat') }}" :current="request()->routeIs('admin.daftar-surat')" wire:navigate>
                    {{ __('Arsip Surat Keterangan') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>

        @can('akses_pengaturan_akses')
        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('Pengaturan')" class="grid text-slate-400">
                <flux:sidebar.item icon="cog" :href="route('admin.hak-akses')" :current="request()->routeIs('admin.hak-akses')" wire:navigate>
                    {{ __('Hak Akses') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cpu-chip" :href="route('admin.satusehat-dashboard')" :current="request()->routeIs('admin.satusehat-dashboard')" wire:navigate>
                    {{ __('SatuSehat Dashboard') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="chart-bar" :href="route('admin.laporan-eksekutif')" :current="request()->routeIs('admin.laporan-eksekutif')" wire:navigate>
                    {{ __('Laporan Executive') }}
                </flux:sidebar.item>
            </flux:sidebar.group>
        </flux:sidebar.nav>
        @endcan

        <flux:sidebar.item icon="computer-desktop" href="{{ route('display-antrean') }}" :current="request()->routeIs('display-antrean')" wire:navigate>
            {{ __('Display Antrean') }}
        </flux:sidebar.item>

        <flux:spacer />

        <flux:sidebar.nav>
            <flux:sidebar.item icon="folder-git-2" href="https://github.com/yudas19/rmepormikijabar" target="_blank" class="text-slate-400 hover:text-slate-200">
                {{ __('Repository GitHub') }}
            </flux:sidebar.item>

            <flux:sidebar.item icon="book-open-text" href="https://youtu.be/ulp83bbf6-U" target="_blank" class="text-slate-400 hover:text-slate-200">
                {{ __('Tutorial Penggunaan') }}
            </flux:sidebar.item>
        </flux:sidebar.nav>
        <x-desktop-user-menu class="hidden lg:block border-t border-slate-800 pt-4" :name="auth()->user()->name" />
    </flux:sidebar>

    <flux:header class="lg:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar
                                :name="auth()->user()->name"
                                :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item
                        as="button"
                        type="submit"
                        icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer"
                        data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
    <flux:toast.group>
        <flux:toast />
    </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>