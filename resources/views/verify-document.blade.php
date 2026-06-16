<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Dokumen Medis Elektronik</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <style>
        body {
            background: radial-gradient(circle at top right, rgba(240, 253, 244, 1) 0%, rgba(244, 244, 245, 1) 100%);
        }
        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 antialiased text-zinc-800">
    <div class="w-full max-w-md glass border border-white/60 shadow-2xl rounded-2xl p-6 md:p-8 transition-all hover:shadow-emerald-100/50">
        
        <!-- Header Logo / Brand -->
        <div class="flex flex-col items-center text-center mb-6">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-emerald-500 text-white shadow-lg shadow-emerald-500/20 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                </svg>
            </div>
            <h1 class="text-sm font-extrabold tracking-widest text-emerald-600 uppercase">Verifikasi TTE</h1>
            <p class="text-xs text-zinc-500 mt-0.5">Sistem EHR Terintegrasi</p>
        </div>

        @if ($isValid)
        <!-- VALID STATE -->
        <div id="verify-status" class="flex flex-col items-center text-center">
            <!-- Valid Check Animation -->
            <div class="relative flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 text-emerald-500 border border-emerald-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span class="absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-20 animate-ping"></span>
            </div>

            <h2 class="text-xl font-bold text-zinc-900 leading-tight">Dokumen Terverifikasi</h2>
            <div class="mt-3 text-sm text-zinc-650 dark:text-zinc-700 bg-emerald-50/50 border border-emerald-100 rounded-xl p-4 leading-relaxed text-left">
                Dokumen ini <strong class="text-emerald-700">SAH</strong> dan terverifikasi secara elektronik oleh <strong id="verify-faskes">{{ $namaFaskes }}</strong>. Dikeluarkan oleh <strong id="verify-doctor">dr. {{ $doctorName }}</strong> untuk pasien <strong id="verify-patient">{{ $patientName }}</strong> pada tanggal <strong id="verify-date">{{ $visitDate }}</strong>.
            </div>

            <!-- Details Table -->
            <div class="w-full mt-6 text-left border-t border-zinc-150 pt-4 space-y-3">
                <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-wider">Detail Dokumen</h3>
                
                <div class="grid grid-cols-3 text-xs py-1 border-b border-zinc-100 pb-2">
                    <span class="text-zinc-500 font-semibold">Jenis Dokumen</span>
                    <span class="col-span-2 font-bold text-zinc-800 text-right">{{ $documentTitle }}</span>
                </div>

                <div class="grid grid-cols-3 text-xs py-1 border-b border-zinc-100 pb-2">
                    <span class="text-zinc-500 font-semibold">Nama Dokter</span>
                    <span class="col-span-2 font-bold text-zinc-800 text-right">dr. {{ $doctorName }}</span>
                </div>

                <div class="grid grid-cols-3 text-xs py-1 border-b border-zinc-100 pb-2">
                    <span class="text-zinc-500 font-semibold">SIP Dokter</span>
                    <span class="col-span-2 font-mono text-zinc-650 text-right">{{ $doctorSip }}</span>
                </div>

                <div class="grid grid-cols-3 text-xs py-1 border-b border-zinc-100 pb-2">
                    <span class="text-zinc-500 font-semibold">Nama Pasien</span>
                    <span class="col-span-2 font-bold text-zinc-800 text-right">{{ $patientName }}</span>
                </div>

                <div class="grid grid-cols-3 text-xs py-1">
                    <span class="text-zinc-500 font-semibold">Tanggal Dokumen</span>
                    <span class="col-span-2 font-mono text-zinc-650 text-right">{{ $visitDate }}</span>
                </div>
            </div>

            <!-- Security Footer -->
            <div class="mt-8 flex items-center justify-center gap-1.5 text-[10px] text-emerald-600 font-semibold tracking-wide uppercase bg-emerald-50 border border-emerald-100 px-3 py-1.5 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                </svg>
                Tanda Tangan Elektronik Valid
            </div>
        </div>
        @else
        <!-- INVALID STATE -->
        <div id="verify-status" class="flex flex-col items-center text-center">
            <div class="relative flex items-center justify-center w-20 h-20 rounded-full bg-red-50 text-red-500 border border-red-100 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-10 h-10">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.249-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
                </svg>
            </div>

            <h2 class="text-xl font-bold text-red-650">Verifikasi Gagal</h2>
            <p class="text-sm text-zinc-500 mt-2 leading-relaxed">
                {{ $error ?? 'Dokumen ini tidak valid atau tanda tangan elektronik telah dimodifikasi.' }}
            </p>

            <div class="w-full mt-6 border-t border-zinc-150 pt-4 text-xs text-zinc-400 text-center">
                Silakan hubungi fasilitas kesehatan penerbit jika Anda merasa ini adalah kesalahan.
            </div>
        </div>
        @endif

    </div>
</body>
</html>
