<!DOCTYPE html>
<!-- Menghapus class "dark" agar sistem membaca Mode Terang secara default, sesuai keinginan Anda -->
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="selection:bg-amber-400 selection:text-zinc-900">
    <head>
        @include('partials.head')
    </head>
    <!-- Background utama diubah menjadi biru bersih (bg-white) -->
    <body class="min-h-screen bg-gradient-to-br from-blue-400 via-blue-500 to-indigo-600 antialiased relative overflow-hidden">
        
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10 relative z-10">
            <div class="flex w-full max-w-sm flex-col gap-4">
                
                <!-- AREA LOGO (Diperbesar secara signifikan & Lebih Tegas) -->
                @php
                    $faskes = \App\Models\FaskesProfile::first();
                    $logoPath = $faskes?->logo_path;
                    $faskesName = $faskes?->nama_faskes ?? 'RME PORMIKI JABAR';
                @endphp
                <a href="{{ route('home') }}" class="flex flex-col items-center gap-3 font-medium group mb-2" wire:navigate>
                    <!-- Ukuran box logo dinaikkan dari h-9/h-10 menjadi h-16 w-16 -->
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white border border-zinc-200 shadow-xl transition-transform group-hover:scale-105 duration-200 overflow-hidden">
                        @if ($logoPath)
                            <img src="{{ asset('storage/' . $logoPath) }}" class="object-contain w-full h-full p-2" alt="{{ $faskesName }}" />
                        @else
                            <!-- Ukuran icon logo diperbesar dari size-6 menjadi size-10 -->
                            <x-app-logo-icon class="size-10 fill-current text-amber-400" />
                        @endif
                    </span>
                    <!-- Menampilkan nama aplikasi dengan tipografi yang kuat di bawah logo (Opsional namun disarankan) -->
                    <span class="text-xs font-black tracking-widest text-zinc-100 uppercase">{{ $faskesName }}</span>
                </a>

                <!-- Slot Tempat Card Login Berada -->
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
                
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>