<x-layouts::app title="Dashboard Master Data">
    <div class="py-6 rounded-2xl p-8 bg-grey-200 shadow-xl shadow-yellow-500/50">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Master Data</h3>
            <p class="text-sm text-gray-600 dark:text-zinc-400">Lengkapi Data-data Master untuk memastikan sistem berjalan dengan baik.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- 1. Master Petugas -->
            <a href="{{ route('master.petugas') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧑‍⚕️</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Petugas</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola data dokter, perawat, dan administrasi rekam medis.</p>
            </a>

            <!-- 2. Master Obat -->
            <a href="{{ route('master.obat') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">💊</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Obat</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola data obat, stok, dan informasi farmakologis.</p>
            </a>

            <!-- 3. Master Poli -->
            <a href="{{ route('master.poli') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🩺</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Poli</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola daftar poliklinik (Poli Umum, Gigi, Anak, dll).</p>
            </a>

            <!-- 4. Master Laboratorium -->
            <a href="{{ route('master.laboratorium') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧪</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Laboratorium</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola daftar layanan laboratorium dan hasil pemeriksaan.</p>
            </a>

            <!-- 5. Master Tindakan -->
            <a href="{{ route('master.tindakan') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">⚡</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Tindakan</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Nama-nama tindakan medis beserta tarifnya di klinik.</p>
            </a>

            <!-- 6. Master Cara Pakai -->
            <a href="{{ route('master.cara-pakai-obat') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧾</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Cara Pakai</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola aturan pakai dan dosis konsumsi obat pasien.</p>
            </a>
            
            <!-- 7. Master Pekerjaan -->
            <a href="{{ route('master.pekerjaan') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">💼</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Pekerjaan</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola opsi daftar pekerjaan untuk registrasi pasien.</p>
            </a>

            <!-- 8. Master Pendidikan -->
            <a href="{{ route('master.pendidikan') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🎓</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Pendidikan</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola opsi jenjang pendidikan terakhir pasien.</p>
            </a>

            <!-- 9. Master Agama -->
            <a href="{{ route('master.agama') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🕌</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Agama</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola daftar agama untuk data demografi pasien.</p>
            </a>

            <!-- 10. Master Provinsi -->
            <a href="{{ route('master.provinsi') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🗺️</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Provinsi</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola wilayah provinsi di Indonesia untuk data alamat.</p>
            </a>

            <!-- 10. Master Kabupaten / Kota -->
            <a href="{{ route('master.kabupaten-kota') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏙️</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Kabupaten / Kota</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Kelola wilayah kabupaten/kota untuk detail data alamat.</p>
            </a>

            <!-- 11. Master PCare BPJS -->
            <a href="{{ route('master.pcarebpjs') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏬</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master PCare</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Konfigurasi dan data bridging PCare BPJS Kesehatan.</p>
            </a>

            <!-- 12. Master Satu Sehat -->
            <a href="{{ route('master.satusehat') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏥</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Master Satusehat</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Konfigurasi dan data bridging Kemenkes SatuSehat.</p>
            </a>

            <!-- 13. Profil Faskes -->
            <a href="{{ route('master.faskes-profile') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏢</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Profil Faskes</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Atur profil klinik, dokter penanggung jawab, dan logo.</p>
            </a>

            <!-- 14. Jadwal Dokter -->
            <a href="{{ route('master.jadwal-dokter') }}" wire:navigate class="block p-6 bg-white dark:bg-zinc-900 border border-gray-200 dark:border-zinc-800 rounded-lg shadow hover:bg-gray-50 dark:hover:bg-zinc-800 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">📅</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 dark:text-white group-hover:text-indigo-600">Jadwal Dokter</h5>
                </div>
                <p class="font-normal text-sm text-gray-600 dark:text-zinc-400">Atur jadwal praktik, kuota pasien, dan jam operasional dokter.</p>
            </a>
        </div>
    </div>
</x-layouts::app>