<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to RME PORMIKI JABAR</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="m-0 p-0 bg-white font-sans antialiased selection:bg-amber-400 selection:text-zinc-900">

  <div class="flex min-h-screen w-full flex-col md:flex-row">
    
    <!-- SISI KIRI: Branding & Informasi Target (Hitam Premium + Gradasi Hijau/Kuning yang Lebih Terang) -->
    <div class="flex w-full flex-col justify-between bg-zinc-950 text-zinc-950 md:w-1/2 p-8 lg:p-16 relative overflow-hidden border-b md:border-b-0 md:border-r border-zinc-900">

      <!-- Efek Dekoratif Cahaya Latar Belakang (Glow ditingkatkan agar warna hijau & kuning lebih hidup) -->
      <div class="absolute -top-40 -left-40 w-96 h-96 bg-emerald-500/15 rounded-full blur-[120px]"></div>
      <div class="absolute bottom-10 right-10 w-72 h-72 bg-amber-400/10 rounded-full blur-[100px]"></div>

      <!-- Logo / Header Kecil -->
      <div class="z-10 flex items-center gap-3">
        <div class="h-3 w-3 rounded-full bg-emerald-400 animate-pulse"></div>
        <span class="text-xs font-bold tracking-widest text-zinc-400 uppercase">RME PORMIKI JAWA BARAT</span>
      </div>

      <!-- Konten Utama Kiri -->
      <div class="my-auto z-10 pt-12 pb-8">
        <img src="{{ asset('images/pormiki.png') }}" alt="" class="w-40 mb-6">
        <h1 class="text-4xl font-black tracking-tight mb-4 lg:text-5xl leading-tight text-white">
          Transformasi Digital <br>
          <!-- Menggunakan perpaduan Hijau Emerald dan Kuning Amber yang tajam di atas background hitam -->
          <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-emerald-300 to-amber-300">
            Rekam Medis Elektronik
          </span>
        </h1>
        <p class="text-zinc-400 text-base lg:text-lg max-w-md mb-8 leading-relaxed">
          Solusi RME standar PORMIKI yang aman, handal, dan dirancang khusus untuk mempermudah operasional fasilitas kesehatan Anda.
        </p>

        <!-- Tag Target Fasilitas Kesehatan (Menggunakan aksen Hijau Emerald transparan yang modern) -->
        <div class="flex flex-wrap gap-2.5">
          <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 shadow-sm">
            ✦ TPMD
          </span>
          <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 shadow-sm">
            ✦ TPMDG
          </span>
          <span class="px-3.5 py-1.5 text-xs font-semibold rounded-full bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 shadow-sm">
            ✦ Klinik Pratama
          </span>
        </div>
      </div>

      <!-- Footer Kecil Kiri -->
      <div class="z-10 text-xs text-zinc-500">
        © 2026 PORMIKI Jawa Barat. All rights reserved.
      </div>
    </div>

    <!-- SISI KANAN: Akses Masuk & Call To Action (Diubah menjadi PUTIH BERSIH agar kontras, profesional, dan nyaman di mata) -->
    <div class="flex w-full flex-col justify-center bg-white text-zinc-900 md:w-1/2 p-8 lg:p-16 relative">
      
      <!-- Efek Pembatas Gradasi antara Hitam (Kiri) dan Putih (Kanan) -->
      <div class="absolute top-0 left-0 right-0 h-[4px] md:h-full md:w-[4px] md:right-auto bg-gradient-to-r md:bg-gradient-to-b from-emerald-500 via-amber-400 to-transparent"></div>

      <div class="max-w-sm w-full mx-auto text-center md:text-left">
        <!-- Menggunakan aksen Hijau untuk sub-header agar terlihat formal khas dunia kesehatan -->
        <span class="text-emerald-600 text-xs font-bold uppercase tracking-wider block mb-2">Siap Memulai?</span>
        <h2 class="text-3xl font-extrabold mb-4 tracking-tight text-zinc-950">
          Akses Layanan Aplikasi
        </h2>
        <p class="text-sm text-zinc-600 mb-8 leading-relaxed">
          Silakan masuk ke akun Anda atau daftarkan fasilitas kesehatan Anda untuk mulai mendigitalisasi rekam medis secara profesional.
        </p>

        <!-- Tombol Utama: Kuning Amber Mencolok dengan Teks Hitam Pekat (Sama seperti tombol login) -->
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-amber-400 text-zinc-950 px-8 py-3.5 rounded-xl font-bold hover:bg-amber-500 transition-all duration-200 shadow-lg shadow-amber-400/30 tracking-wide text-sm w-full sm:w-auto">
            Masuk / Daftar
          </a>
        </div>
        
        <!-- Info Tambahan -->
        <p class="text-xs text-zinc-500 mt-8 border-t border-zinc-100 pt-4">
          Butuh bantuan teknis? Hubungi <span class="text-emerald-600 font-semibold cursor-pointer hover:underline">Helpdesk RME PORMIKI Jabar</span>.
        </p>
      </div>
    </div>

  </div>

</body>
</html>