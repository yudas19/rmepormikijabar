<x-layouts::app :title="__('Dashboard')">
    <!-- Load Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    <!-- Diubah menjadi bg-zinc-50 (terang) dan bg-zinc-950 (gelap) yang solid dan bersih -->
    <div class="space-y-8 p-6 font-jakarta bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        
        <!-- Welcome banner (Optimasi Premium: Putih/Hitam dengan Border Aksen Gradasi) -->
        <div class="relative overflow-hidden rounded-2xl border border-zinc-200 bg-white p-8 text-zinc-900 shadow-xl shadow-zinc-200/50 dark:border-zinc-800 dark:bg-zinc-900 dark:text-white dark:shadow-none">
            <!-- Efek dekoratif glow yang tidak merusak keterbacaan teks -->
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-amber-400/10 blur-2xl dark:bg-amber-400/5"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-emerald-500/10 blur-2xl dark:bg-emerald-500/5"></div>
            
            <!-- Garis aksen tipis khas PORMIKI di sisi kiri banner -->
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-emerald-500 via-amber-400 to-emerald-600"></div>

            <div class="relative z-10 max-w-2xl pl-2">
                <!-- Badge dengan aksen hijau medis -->
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400 border border-emerald-200/50 dark:border-emerald-800/30">
                    <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Sistem EMR Terintegrasi
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight md:text-4xl text-zinc-950 dark:text-white">
                  Selamat Datang di Portal Klinik
                </h1>
                <p class="mt-2 text-zinc-600 dark:text-zinc-400 text-sm md:text-base font-medium leading-relaxed">
                  Monitor antrean pasien poliklinik, penunjang medis laboratorium, farmasi, kasir, dan integrasi SatuSehat secara real-time.
                </p>
            </div>
        </div>

        <!-- Upper Stat Cards -->
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            
            <!-- Pasien Hari Ini (Aksen Kuning Emas/Amber) -->
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-amber-400/10 transition-all group-hover:scale-110"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-400/10 text-amber-600 dark:bg-amber-400/20 dark:text-amber-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 18H9.91a11.386 11.386 0 01-4.84.233m4.84-.233L9.708 15.2M9.693 15.074A3 3 0 008 15.074M6 21H3v-2.25a2.25 2.25 0 011.64-2.157m1.522-.886A9.39 9.39 0 003 16.035m1.522-.886A9.337 9.337 0 019 13.5h3c1.78 0 3.42.5 4.8 1.357m-9.6-.886l.006-.003a3 3 0 014.288-4.286H9.98a3 3 0 014.288 4.286" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-amber-400/10 px-2.5 py-0.5 text-xs font-bold text-amber-700 dark:bg-amber-400/20 dark:text-amber-400">Hari Ini</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider dark:text-zinc-500">Pendaftaran Pasien</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">{{ $todayCount }}</p>
                    <p class="mt-1 text-xs text-zinc-400">Total pasien didaftarkan hari ini</p>
                </div>
            </div>

            <!-- Total Pasien (Aksen Hitam / Zinc Elegan) -->
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-zinc-500/10 transition-all group-hover:scale-110"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-900 text-white dark:bg-zinc-800 dark:text-zinc-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.97 5.97 0 00-.75-2.906m-.173-.173L13 14.8M12 14.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-bold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">Keseluruhan</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider dark:text-zinc-500">Total Database Pasien</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">{{ $totalPatients }}</p>
                    <p class="mt-1 text-xs text-zinc-400">Jumlah data pasien terdaftar</p>
                </div>
            </div>

            <!-- Rasio Keaktifan (Aksen Hijau Emerald) -->
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-emerald-500/10 transition-all group-hover:scale-110"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-emerald-500/10 px-2.5 py-0.5 text-xs font-bold text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400">Aktif</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider dark:text-zinc-500">Rasio Keaktifan</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-white font-mono">
                        {{ $totalPatients > 0 ? round(($todayCount / $totalPatients) * 100, 1) : 0 }}%
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">Persentase kunjungan hari ini</p>
                </div>
            </div>

            <!-- Poli Teraktif (Kombinasi Bersih) -->
            @php
                $mostActivePoli = $poliStats->sortByDesc('pendaftaran_count')->first();
            @endphp
            <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-16 w-16 rounded-full bg-zinc-900/5 transition-all group-hover:scale-110 dark:bg-white/5"></div>
                <div class="flex items-center justify-between">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-zinc-100 text-zinc-800 dark:bg-white/10 dark:text-zinc-100">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.203 0-4.361.147-6.478.432V21m12.956-10.25H4.044" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-bold text-zinc-700 dark:bg-white/10 dark:text-zinc-300">Poli Utama</span>
                </div>
                <div class="mt-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider dark:text-zinc-500">Poli Teraktif</h3>
                    <p class="mt-1 text-lg font-extrabold tracking-tight text-zinc-900 dark:text-white truncate">
                        {{ $mostActivePoli && $mostActivePoli->pendaftaran_count > 0 ? $mostActivePoli->nama_poli : 'Belum Ada Kunjungan' }}
                    </p>
                    <p class="mt-1 text-xs text-zinc-400">
                        {{ $mostActivePoli && $mostActivePoli->pendaftaran_count > 0 ? $mostActivePoli->pendaftaran_count . ' kunjungan aktif' : 'Semua poli kosong' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Lower Detailed Stats -->
        <div class="grid gap-6 lg:grid-cols-2">
            
            <!-- Pasien per Poliklinik (Visualisasi Warna Bertema Harmonis) -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-2 mb-6 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <svg class="h-5 w-5 text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.203 0-4.361.147-6.478.432V21m12.956-10.25H4.044" />
                    </svg>
                    <flux:heading size="lg" class="font-extrabold text-zinc-950 dark:text-white">Distribusi Pasien per Poliklinik (Hari Ini)</flux:heading>
                </div>

                <div class="space-y-4">
                    @forelse($poliStats as $poli)
                    @php
                        $percentage = $todayCount > 0 ? round(($poli->pendaftaran_count / $todayCount) * 100, 1) : 0;
                        // Penyesuaian bar warna agar masuk ke ekosistem Putih-Hitam-Kuning-Hijau
                        $barColor = match($poli->kode_poli) {
                            'UMU' => 'bg-amber-400 dark:bg-amber-500', // Kuning Accent
                            'GIG' => 'bg-emerald-500 dark:bg-emerald-600', // Hijau Accent
                            'KIA' => 'bg-zinc-900 dark:bg-zinc-400', // Hitam/Zinc Accent
                            default => 'bg-zinc-300 dark:bg-zinc-700'
                        };
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $poli->nama_poli }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-950 dark:text-white font-mono">{{ $poli->pendaftaran_count }} pasien</span>
                                <span class="text-xs font-medium text-zinc-400">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-sm text-zinc-400 italic">Belum ada poliklinik terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <!-- Pasien berdasarkan Pekerjaan -->
            <div class="rounded-2xl border border-zinc-200/80 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="flex items-center gap-2 mb-6 border-b border-zinc-100 dark:border-zinc-800/80 pb-3">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .28-.22.5-.5.5h-15.5c-.28 0-.5-.22-.5-.5v-4.25m16.5 0c0-.55-.45-1-1-1H3.75c-.55 0-1 .45-1 1m17.5 0L12 9.75 3.75 14.15M12 9.75V3m0 0L8.25 6.75M12 3l3.75 3.75" />
                    </svg>
                    <flux:heading size="lg" class="font-extrabold text-zinc-950 dark:text-white">Demografi Pekerjaan Pasien Terdaftar</flux:heading>
                </div>

                <div class="space-y-4">
                    @forelse($jobStats as $job)
                    @php
                        $percentage = $totalPatients > 0 ? round(($job->count / $totalPatients) * 100, 1) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-zinc-700 dark:text-zinc-300">{{ $job->pekerjaan }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-zinc-950 dark:text-white font-mono">{{ $job->count }} orang</span>
                                <span class="text-xs font-medium text-zinc-400">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="h-2 w-full rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <!-- Menggunakan gradien halus dari Kuning Amber ke Hijau Emerald untuk representasi data yang cantik -->
                            <div class="h-full rounded-full bg-gradient-to-r from-amber-400 to-emerald-500 transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-sm text-zinc-400 italic">Belum ada demografi pekerjaan tercatat.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-layouts::app>