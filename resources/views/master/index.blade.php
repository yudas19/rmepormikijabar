<x-layouts::app title="Dashboard Master Data">
    <div class="p-6 md:p-8 bg-slate-50 dark:bg-slate-950 min-h-screen">
        
        <!-- Header Section -->
        <div class="mb-8 border-b border-slate-200 dark:border-slate-800 pb-5">
            <h3 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white">Master Data</h3>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Lengkapi data-data master untuk memastikan operasional sistem klinik berjalan dengan optimal.</p>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- 1. Master Petugas -->
            <a href="{{ route('master.petugas') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg shadow-blue-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-blue-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🧑‍⚕️</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Petugas</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-blue-100/80 leading-relaxed relative z-10">Kelola data dokter, perawat, dan administrasi rekam medis.</p>
            </a>

            <!-- 2. Master Obat -->
            <a href="{{ route('master.obat') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg shadow-emerald-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">💊</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Obat</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-emerald-100/80 leading-relaxed relative z-10">Kelola data obat, stok, dan informasi farmakologis.</p>
            </a>

            <!-- 3. Master Poli -->
            <a href="{{ route('master.poli') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg shadow-amber-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-yellow-300/10"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🩺</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Poli</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-amber-100/80 leading-relaxed relative z-10">Kelola daftar poliklinik (Poli Umum, Gigi, Anak, dll).</p>
            </a>

            <!-- 4. Master Laboratorium -->
            <a href="{{ route('master.laboratorium') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-purple-500 to-violet-600 shadow-lg shadow-purple-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🧪</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Laboratorium</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-purple-100/80 leading-relaxed relative z-10">Kelola daftar layanan laboratorium dan hasil pemeriksaan.</p>
            </a>

            <!-- 5. Master Tindakan -->
            <a href="{{ route('master.tindakan') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg shadow-rose-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">⚡</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Tindakan</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-rose-100/80 leading-relaxed relative z-10">Nama-nama tindakan medis beserta tarifnya di klinik.</p>
            </a>

            <!-- 6. Master Cara Pakai -->
            <a href="{{ route('master.cara-pakai-obat') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-cyan-500 to-sky-600 shadow-lg shadow-cyan-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🧾</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Cara Pakai</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-cyan-100/80 leading-relaxed relative z-10">Kelola aturan pakai dan dosis konsumsi obat pasien.</p>
            </a>
            
            <!-- 7. Master Pekerjaan -->
            <a href="{{ route('master.pekerjaan') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 shadow-lg shadow-slate-500/10 hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-500/20 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/5 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-blue-500/10"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10 text-xl backdrop-blur-sm">💼</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Pekerjaan</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-slate-300/80 leading-relaxed relative z-10">Kelola opsi daftar pekerjaan untuk registrasi pasien.</p>
            </a>

            <!-- 8. Master Pendidikan -->
            <a href="{{ route('master.pendidikan') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 shadow-lg shadow-indigo-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-indigo-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🎓</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Pendidikan</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-indigo-100/80 leading-relaxed relative z-10">Kelola opsi jenjang pendidikan terakhir pasien.</p>
            </a>

            <!-- 9. Master Agama -->
            <a href="{{ route('master.agama') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 shadow-lg shadow-teal-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-teal-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🕌</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Agama</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-teal-100/80 leading-relaxed relative z-10">Kelola daftar agama untuk data demografi pasien.</p>
            </a>

            <!-- 10. Master Provinsi -->
            <a href="{{ route('master.provinsi') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-600 shadow-lg shadow-violet-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-violet-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🗺️</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Provinsi</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-violet-100/80 leading-relaxed relative z-10">Kelola wilayah provinsi di Indonesia untuk data alamat.</p>
            </a>

            <!-- 11. Master Kabupaten / Kota -->
            <a href="{{ route('master.kabupaten-kota') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-fuchsia-500 to-pink-600 shadow-lg shadow-fuchsia-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-fuchsia-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🏙️</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Kabupaten / Kota</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-fuchsia-100/80 leading-relaxed relative z-10">Kelola wilayah kabupaten/kota untuk detail data alamat.</p>
            </a>

            <!-- 12. Master PCare BPJS -->
            <a href="{{ route('master.pcarebpjs') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-sky-500 to-blue-600 shadow-lg shadow-sky-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-sky-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🏬</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master PCare</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-sky-100/80 leading-relaxed relative z-10">Konfigurasi dan data bridging PCare BPJS Kesehatan.</p>
            </a>

            <!-- 13. Master Satu Sehat -->
            <a href="{{ route('master.satusehat') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 shadow-lg shadow-orange-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-orange-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-yellow-300/10"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🏥</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Master Satusehat</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-orange-100/80 leading-relaxed relative z-10">Konfigurasi dan data bridging Kemenkes SatuSehat.</p>
            </a>

            <!-- 14. Profil Faskes -->
            <a href="{{ route('master.faskes-profile') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-lime-500 to-green-600 shadow-lg shadow-lime-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-lime-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">🏢</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Profil Faskes</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-lime-100/80 leading-relaxed relative z-10">Atur profil klinik, dokter penanggung jawab, dan logo.</p>
            </a>

            <!-- 15. Jadwal Dokter -->
            <a href="{{ route('master.jadwal-dokter') }}" wire:navigate class="group relative block overflow-hidden p-6 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-lg shadow-pink-500/20 hover:-translate-y-1 hover:shadow-xl hover:shadow-pink-500/30 transition-all duration-200">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 h-20 w-20 rounded-full bg-white/10 transition-all group-hover:scale-125"></div>
                <div class="absolute -bottom-6 -left-6 h-24 w-24 rounded-full bg-white/5"></div>
                <div class="flex items-center space-x-3 mb-3 relative z-10">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20 text-xl backdrop-blur-sm">📅</div>
                    <h5 class="text-base font-bold tracking-tight text-white">Jadwal Dokter</h5>
                </div>
                <p class="font-medium text-xs md:text-sm text-pink-100/80 leading-relaxed relative z-10">Atur jadwal praktik, kuota pasien, dan jam operasional dokter.</p>
            </a>

        </div>
    </div>
</x-layouts::app>