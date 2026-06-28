<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to NEW RME</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 p-0 bg-slate-50 font-sans antialiased selection:bg-blue-500 selection:text-white">

  <div class="flex min-h-screen w-full flex-col md:flex-row">
    
    <!-- SISI KIRI: Branding & Informasi Target (Deep Slate Premium + Gradasi Indigo Violet) -->
    <div class="flex w-full flex-col justify-between bg-blue-900 text-blue-200 md:w-1/2 p-8 lg:p-16 relative overflow-hidden border-b md:border-b-0 md:border-r border-slate-900">

      <!-- Efek Latar Belakang Glow Halus (Premium Tech Aura) -->
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-500/15 rounded-full blur-[120px]"></div>
      <div class="absolute bottom-10 right-10 w-72 h-72 bg-blue-500/10 rounded-full blur-[100px]"></div>

      <!-- Logo / Header Kecil -->
      <div class="z-10 flex items-center gap-3">
        <div class="h-2.5 w-2.5 rounded-full bg-blue-400 animate-pulse"></div>
        <span class="text-xs font-bold tracking-widest text-white uppercase">RME PORMIKI JAWA BARAT</span>
      </div>

      <!-- Konten Utama Kiri -->
      <div class="my-auto z-10 pt-12 pb-8">
        <img src="{{ asset('images/pormiki.png') }}" alt="" class="w-36 mb-8 opacity-90">
        <h1 class="text-4xl font-black tracking-tight mb-4 lg:text-5xl leading-tight text-white">
          Transformasi Digital <br>
          <!-- Menggunakan gradasi mewah Indigo, Purple, ke Pink halus -->
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-sky-400  to-white">
            Rekam Medis Elektronik
          </span>
        </h1>
        <p class="text-slate-400 text-sm lg:text-base max-w-md mb-8 leading-relaxed">
          Solusi RME standar KEMENKES yang aman, handal, dan dirancang khusus untuk mempermudah operasional fasilitas kesehatan Anda.
        </p>

        <!-- Tag Target Fasilitas Kesehatan (Aksen Indigo Transparan Modern) -->
        <div class="flex flex-wrap gap-2">
          <span class="px-4 py-1.5 text-xs font-bold rounded-xl bg-sky-950/40 border border-sky-500/30 text-white shadow-sm">
            ✦ TPMD
          </span>
          <span class="px-4 py-1.5 text-xs font-bold rounded-xl bg-sky-950/40 border border-sky-500/30 text-white shadow-sm">
            ✦ TPMDG
          </span>
          <span class="px-4 py-1.5 text-xs font-bold rounded-xl bg-sky-950/40 border border-sky-500/30 text-white shadow-sm">
            ✦ Klinik Pratama
          </span>
        </div>
      </div>

      <!-- Footer Kecil Kiri -->
      <div class="z-10 text-xs text-slate-600">
        © 2026 PORMIKI Jawa Barat. All rights reserved.
      </div>
    </div>

    <!-- SISI KANAN: Akses Masuk & Call To Action (Putih Bersih dengan Aksen Indigo Kontras) -->
    <div class="flex w-full flex-col justify-center bg-white text-slate-900 md:w-1/2 p-8 lg:p-16 relative">
      
      <!-- Efek Pembatas Gradasi Halus di Tengah Card -->
      <div class="absolute top-0 left-0 right-0 h-[4px] md:h-full md:w-[4px] md:right-auto bg-gradient-to-r md:bg-gradient-to-b from-indigo-500 via-purple-500 to-transparent"></div>

      <div class="max-w-sm w-full mx-auto text-center md:text-left">
        <span class="text-sky-600 text-xs font-bold uppercase tracking-wider block mb-2">Siap Memulai?</span>
        <h2 class="text-3xl font-extrabold mb-4 tracking-tight text-slate-950">
          Akses Layanan Aplikasi
        </h2>
        <p class="text-sm text-slate-500 mb-8 leading-relaxed">
          Silakan masuk ke akun Anda atau daftarkan fasilitas kesehatan Anda untuk mulai mendigitalisasi rekam medis secara profesional.
        </p>

        <!-- Tombol Utama: Indigo Bold (Konsisten dengan Tombol Login Baru) -->
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-sky-600 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-sky-700 transition-all duration-200 shadow-lg shadow-sky-600/20 tracking-wide text-sm w-full sm:w-auto">
            Masuk / Daftar Akun
          </a>
        </div>
        
        <!-- Info Tambahan -->
        <p class="text-xs text-gray-700 mt-8 border-t border-sky-100 pt-4">
          Butuh bantuan teknis? Hubungi <span class="text-blue-400 font-semibold cursor-pointer hover:underline">Helpdesk RME PORMIKI Jabar</span>.
        </p>
      </div>
    </div>

  </div>

</body>
</html>