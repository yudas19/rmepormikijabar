<x-layouts::app title="Dashboard Master Data">
    
    <div class="py-6">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-900">Pilih Menu Manajemen</h3>
            <p class="text-sm text-gray-600">Silakan pilih data master yang ingin Anda kelola di bawah ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧑‍⚕️</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Petugas</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Kelola data dokter, perawat, dan administrasi rekam medis.</p>
            </a>

            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">💊</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Obat</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Kelola data obat, stok, dan informasi farmakologis.</p>
            </a>

            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🩺</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Poli</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Kelola daftar poliklinik (Poli Umum, Gigi, Anak, dll).</p>
            </a>

            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🧪</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Laboratorium</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Kelola daftar layanan laboratorium dan hasil pemeriksaan.</p>
            </a>

            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏬</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Pcare</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Bridging Pcare BPJS.</p>
            </a>

            <a href="#" class="block p-6 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-50 transition duration-200 group">
                <div class="flex items-center space-x-3 mb-2">
                    <span class="text-2xl">🏥</span>
                    <h5 class="text-lg font-bold tracking-tight text-gray-900 group-hover:text-indigo-600">Master Satusehat</h5>
                </div>
                <p class="font-normal text-sm text-gray-700">Bridging Satu Sehat Kemenkes.</p>
            </a>


        </div>
    </div>

</x-layouts::app>