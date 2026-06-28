<x-layouts::app :title="__('Dashboard')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>

    <div class="space-y-8 p-6 font-jakarta bg-slate-50 dark:bg-slate-950 min-h-screen">
        
        <div class="relative overflow-hidden rounded-2xl border border-blue-100 bg-slate-950 p-8 text-white shadow-xl shadow-blue-900/5 dark:border-slate-800">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-amber-400/20 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-blue-600/20 blur-2xl"></div>
            
            <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-gradient-to-b from-blue-600 via-white"></div>

            <div class="relative z-10 max-w-2xl pl-2">
                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-500/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-green-400 border border-blue-500/30">
                    <span class="h-2 w-2 rounded-full bg-blue-400 animate-pulse"></span>
                    Sistem EMR Terintegrasi
                </span>
                <h1 class="mt-4 text-3xl font-black tracking-tight md:text-4xl text-white">
                  Selamat Datang di Portal Klinik
                </h1>
                <p class="mt-2 text-slate-300 text-sm md:text-base font-medium leading-relaxed">
                  Monitor antrean pasien poliklinik, penunjang medis laboratorium, farmasi, kasir, dan integrasi SatuSehat secara real-time.
                </p>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 p-6 shadow-lg shadow-blue-500/20 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/30">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A11.386 11.386 0 0110.089 18H9.91a11.386 11.386 0 01-4.84.233m4.84-.233L9.708 15.2M9.693 15.074A3 3 0 008 15.074M6 21H3v-2.25a2.25 2.25 0 011.64-2.157m1.522-.886A9.39 9.39 0 003 16.035m1.522-.886A9.337 9.337 0 019 13.5h3c1.78 0 3.42.5 4.8 1.357m-9.6-.886l.006-.003a3 3 0 014.288-4.286H9.98a3 3 0 014.288 4.286" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/20 backdrop-blur-sm text-white px-2.5 py-0.5 text-xs font-black uppercase tracking-wider">Hari Ini</span>
                </div>
                <div class="mt-4 relative z-10">
                    <h3 class="text-xs font-bold text-blue-100 uppercase tracking-wider">Pendaftaran Pasien</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-white font-mono">{{ $todayCount }}</p>
                    <p class="mt-1 text-xs text-blue-200/80">Total pasien didaftarkan hari ini</p>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-6 shadow-lg shadow-slate-500/10 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-500/20">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/5 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-blue-500/10"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-white backdrop-blur-sm">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.97 5.97 0 00-.75-2.906m-.173-.173L13 14.8M12 14.5a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/10 backdrop-blur-sm border border-white/10 px-2.5 py-0.5 text-xs font-bold text-slate-200">Keseluruhan</span>
                </div>
                <div class="mt-4 relative z-10">
                    <h3 class="text-xs font-bold text-slate-300 uppercase tracking-wider">Total Database Pasien</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-white font-mono">{{ $totalPatients }}</p>
                    <p class="mt-1 text-xs text-slate-400">Jumlah data pasien terdaftar</p>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 p-6 shadow-lg shadow-amber-500/20 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-500/30">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-yellow-300/10"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/20 backdrop-blur-sm text-white px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider">Aktif</span>
                </div>
                <div class="mt-4 relative z-10">
                    <h3 class="text-xs font-bold text-amber-100 uppercase tracking-wider">Rasio Keaktifan</h3>
                    <p class="mt-1 text-3xl font-extrabold tracking-tight text-white font-mono">
                        {{ $totalPatients > 0 ? round(($todayCount / $totalPatients) * 100, 1) : 0 }}%
                    </p>
                    <p class="mt-1 text-xs text-amber-200/80">Persentase kunjungan hari ini</p>
                </div>
            </div>

            @php
                $mostActivePoli = $poliStats->sortByDesc('pendaftaran_count')->first();
            @endphp
            <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 p-6 shadow-lg shadow-emerald-500/20 transition-all hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/30">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/20 text-white backdrop-blur-sm">
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.203 0-4.361.147-6.478.432V21m12.956-10.25H4.044" />
                        </svg>
                    </div>
                    <span class="rounded-full bg-white/20 backdrop-blur-sm text-white px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider">Poli Utama</span>
                </div>
                <div class="mt-4 relative z-10">
                    <h3 class="text-xs font-bold text-emerald-100 uppercase tracking-wider">Poli Teraktif</h3>
                    <p class="mt-1 text-lg font-extrabold tracking-tight text-white truncate">
                        {{ $mostActivePoli && $mostActivePoli->pendaftaran_count > 0 ? $mostActivePoli->nama_poli : 'Belum Ada Kunjungan' }}
                    </p>
                    <p class="mt-1 text-xs text-emerald-200/80">
                        {{ $mostActivePoli && $mostActivePoli->pendaftaran_count > 0 ? $mostActivePoli->pendaftaran_count . ' kunjungan aktif' : 'Semua poli kosong' }}
                    </p>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <svg class="h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.203 0-4.361.147-6.478.432V21m12.956-10.25H4.044" />
                    </svg>
                    <flux:heading size="lg" class="font-extrabold text-slate-950 dark:text-white">Distribusi Pasien per Poliklinik (Hari Ini)</flux:heading>
                </div>

                <div class="space-y-4">
                    @forelse($poliStats as $poli)
                    @php
                        $percentage = $todayCount > 0 ? round(($poli->pendaftaran_count / $todayCount) * 100, 1) : 0;
                        
                        // Menyesuaikan bar warna agar harmonis dengan tema Biru, Kuning, Hitam
                        $barColor = match($poli->kode_poli) {
                            'UMU' => 'bg-blue-600 dark:bg-blue-500', 
                            'GIG' => 'bg-amber-400 dark:bg-amber-500', 
                            'KIA' => 'bg-slate-950 dark:bg-slate-400', 
                            default => 'bg-blue-400 dark:bg-slate-700'
                        };
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $poli->nama_poli }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-950 dark:text-white font-mono">{{ $poli->pendaftaran_count }} pasien</span>
                                <span class="text-xs font-medium text-slate-400">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full {{ $barColor }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-sm text-slate-400 italic">Belum ada poliklinik terdaftar.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center gap-2 mb-6 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <svg class="h-5 w-5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 .28-.22.5-.5.5h-15.5c-.28 0-.5-.22-.5-.5v-4.25m16.5 0c0-.55-.45-1-1-1H3.75c-.55 0-1 .45-1 1m17.5 0L12 9.75 3.75 14.15M12 9.75V3m0 0L8.25 6.75M12 3l3.75 3.75" />
                    </svg>
                    <flux:heading size="lg" class="font-extrabold text-slate-950 dark:text-white">Demografi Pekerjaan Pasien Terdaftar</flux:heading>
                </div>

                <div class="space-y-4">
                    @forelse($jobStats as $job)
                    @php
                        $percentage = $totalPatients > 0 ? round(($job->count / $totalPatients) * 100, 1) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $job->pekerjaan }}</span>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-950 dark:text-white font-mono">{{ $job->count }} orang</span>
                                <span class="text-xs font-medium text-slate-400">({{ $percentage }}%)</span>
                            </div>
                        </div>
                        <div class="h-2.5 w-full rounded-full bg-slate-100 dark:bg-slate-800">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-600 to-white transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-sm text-slate-400 italic">Belum ada demografi pekerjaan tercatat.</div>
                    @endforelse
                </div>
            </div>

        </div>
        
    </div>
</x-layouts::app>