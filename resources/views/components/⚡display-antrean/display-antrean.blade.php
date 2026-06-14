<div class="min-h-screen bg-zinc-950 text-white font-sans flex flex-col justify-between p-8 select-none"
     wire:poll.3s="checkIncomingCall"
     x-data="{
         activeCall: @entangle('activeCall'),
         isCalling: false,
         timeString: '',
         dateString: '',
         initTimer() {
             setInterval(() => {
                 const now = new Date();
                 this.timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                 this.dateString = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
             }, 1000);
         },
         playActiveCall() {
             if (this.activeCall && !this.isCalling) {
                 this.isCalling = true;
                 
                 // Play chime and read queue number using Web Speech API
                 if ('speechSynthesis' in window) {
                     window.speechSynthesis.cancel();
                     // Split characters for clear spelling (e.g. A - 0 1)
                     const numberSpelled = this.activeCall.nomor_antrean.split('').join(' ');
                     const text = 'Nomor antrean ' + numberSpelled + '. Silakan menuju ke ' + this.activeCall.poli_tujuan;
                     const utterance = new SpeechSynthesisUtterance(text);
                     utterance.lang = 'id-ID';
                     utterance.rate = 0.85;
                     utterance.pitch = 1.0;
                     window.speechSynthesis.speak(utterance);
                 }
                 
                 // Flash calling display on screen, mark done after 6 seconds
                 setTimeout(() => {
                     if (this.activeCall) {
                         $wire.markAsDoneCalling(this.activeCall.id);
                     }
                     this.isCalling = false;
                 }, 6000);
             }
         }
     }"
     x-init="initTimer(); $watch('activeCall', value => { if (value) playActiveCall() }); if (activeCall) playActiveCall();"
>
    <!-- Header Banner -->
    <header class="flex flex-col md:flex-row justify-between items-center bg-zinc-900 border border-zinc-800 rounded-2xl p-6 shadow-2xl mb-8">
        <div class="flex items-center gap-4">
            <div class="bg-emerald-500/10 border border-emerald-500/30 w-16 h-16 rounded-2xl flex items-center justify-center text-emerald-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-9 h-9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-white uppercase">DOKTER & POLIKLINIK ANTREAN TV</h1>
                <p class="text-sm font-semibold text-zinc-400 tracking-wider">SISTEM MONITORING PANGGILAN ANTREAN REAL-TIME</p>
            </div>
        </div>
        <div class="text-center md:text-right mt-4 md:mt-0 bg-zinc-950/40 border border-zinc-800/60 px-6 py-3 rounded-xl">
            <div class="text-xl font-bold font-mono text-emerald-400" x-text="timeString || '--:--:--'"></div>
            <div class="text-xs text-zinc-400 font-semibold mt-0.5" x-text="dateString || 'Memuat Tanggal...'"></div>
        </div>
    </header>

    <!-- Main Screen Layout -->
    <main class="grid grid-cols-1 lg:grid-cols-3 gap-8 flex-1 mb-8 items-stretch">
        <!-- Poli Cards (Left/Center 2 Cols) -->
        <section class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Poli Umum -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-lg flex flex-col justify-between items-center transition-all duration-350 hover:border-blue-500/35 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-blue-500"></div>
                <div class="text-center w-full">
                    <span class="text-xs font-bold text-blue-400 uppercase tracking-widest bg-blue-500/10 px-3 py-1 rounded-full">Poliklinik Umum</span>
                    <h2 class="text-lg font-bold text-zinc-300 mt-4 h-12 flex items-center justify-center">
                        {{ $activeUmum?->pendaftaran?->dokter?->nama_petugas ?? $activeUmum?->dokter?->nama_petugas ?? '-' }}
                    </h2>
                </div>
                <div class="my-6">
                    <div class="text-7xl font-black font-mono text-white tracking-widest bg-zinc-950 border border-zinc-850 px-8 py-6 rounded-2xl shadow-inner animate-pulse">
                        {{ $activeUmum?->nomor_antrean ?? '---' }}
                    </div>
                </div>
                <div class="text-xs font-semibold text-zinc-400 tracking-wider text-center border-t border-zinc-850 w-full pt-4">
                    Panggilan Terakhir
                </div>
            </div>

            <!-- Poli Gigi -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-lg flex flex-col justify-between items-center transition-all duration-350 hover:border-teal-500/35 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-teal-500"></div>
                <div class="text-center w-full">
                    <span class="text-xs font-bold text-teal-400 uppercase tracking-widest bg-teal-500/10 px-3 py-1 rounded-full">Poliklinik Gigi</span>
                    <h2 class="text-lg font-bold text-zinc-300 mt-4 h-12 flex items-center justify-center">
                        {{ $activeGigi?->pendaftaran?->dokter?->nama_petugas ?? $activeGigi?->dokter?->nama_petugas ?? '-' }}
                    </h2>
                </div>
                <div class="my-6">
                    <div class="text-7xl font-black font-mono text-white tracking-widest bg-zinc-950 border border-zinc-850 px-8 py-6 rounded-2xl shadow-inner">
                        {{ $activeGigi?->nomor_antrean ?? '---' }}
                    </div>
                </div>
                <div class="text-xs font-semibold text-zinc-400 tracking-wider text-center border-t border-zinc-850 w-full pt-4">
                    Panggilan Terakhir
                </div>
            </div>

            <!-- Poli KIA -->
            <div class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-lg flex flex-col justify-between items-center transition-all duration-350 hover:border-pink-500/35 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-pink-500"></div>
                <div class="text-center w-full">
                    <span class="text-xs font-bold text-pink-400 uppercase tracking-widest bg-pink-500/10 px-3 py-1 rounded-full">Klinik KIA / Ibu Anak</span>
                    <h2 class="text-lg font-bold text-zinc-300 mt-4 h-12 flex items-center justify-center">
                        {{ $activeKia?->pendaftaran?->dokter?->nama_petugas ?? $activeKia?->dokter?->nama_petugas ?? '-' }}
                    </h2>
                </div>
                <div class="my-6">
                    <div class="text-7xl font-black font-mono text-white tracking-widest bg-zinc-950 border border-zinc-850 px-8 py-6 rounded-2xl shadow-inner">
                        {{ $activeKia?->nomor_antrean ?? '---' }}
                    </div>
                </div>
                <div class="text-xs font-semibold text-zinc-400 tracking-wider text-center border-t border-zinc-850 w-full pt-4">
                    Panggilan Terakhir
                </div>
            </div>
        </section>

        <!-- Right Side: Calling History -->
        <section class="bg-zinc-900 border border-zinc-800 rounded-3xl p-6 shadow-lg flex flex-col justify-between">
            <div>
                <h3 class="text-lg font-extrabold uppercase tracking-widest border-b border-zinc-800 pb-3 mb-4 text-emerald-400 flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                    Riwayat Panggilan
                </h3>
                <div class="space-y-3">
                    @forelse ($history as $h)
                        <div class="flex items-center justify-between bg-zinc-950/60 p-4 border border-zinc-850 rounded-xl">
                            <div>
                                <span class="font-mono text-lg font-black text-white bg-zinc-900 border border-zinc-800 px-3 py-1 rounded-lg mr-2">{{ $h->nomor_antrean }}</span>
                                <span class="text-sm font-semibold text-zinc-300">{{ $h->pasien->nama_pasien }}</span>
                            </div>
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-500">{{ $h->poliklinik_type === 'umum' ? 'Umum' : ($h->poliklinik_type === 'gigi' ? 'Gigi' : 'KIA') }}</span>
                        </div>
                    @empty
                        <div class="text-center text-zinc-500 py-16 text-sm font-semibold italic">Belum ada riwayat panggilan antrean hari ini.</div>
                    @endforelse
                </div>
            </div>
            
            <div class="bg-zinc-950/40 p-4 border border-zinc-850 rounded-2xl flex items-center justify-between text-xs font-bold text-zinc-400 uppercase tracking-widest mt-6">
                <span>Alur Pelayanan</span>
                <span class="text-emerald-500">Pendaftaran &rarr; TTV &rarr; Poli &rarr; Kasir</span>
            </div>
        </section>
    </main>

    <!-- Bottom Ticker -->
    <footer class="bg-zinc-900 border border-zinc-800 rounded-2xl p-4 overflow-hidden shadow-2xl relative">
        <div class="absolute left-0 top-0 bottom-0 bg-zinc-900 w-16 z-10"></div>
        <div class="absolute right-0 top-0 bottom-0 bg-zinc-900 w-16 z-10"></div>
        <div class="whitespace-nowrap flex text-sm font-semibold text-emerald-400 uppercase tracking-widest animate-[marquee_25s_linear_infinite]">
            <span class="mx-8">Selamat Datang di Sistem Antrean Poliklinik Rawat Jalan</span>
            <span class="mx-8">&bull;</span>
            <span class="mx-8">Harap siapkan Kartu Identitas atau KTP dan Kartu BPJS Anda</span>
            <span class="mx-8">&bull;</span>
            <span class="mx-8">Budayakan mengantre dengan tertib demi kenyamanan bersama</span>
            <span class="mx-8">&bull;</span>
            <span class="mx-8">Jika nomor antrean Anda terlewat, harap laporkan ke Loket Pendaftaran</span>
        </div>
    </footer>

    <!-- STYLES FOR MARQUEE ANIMATION -->
    <style>
        @keyframes marquee {
            0% { transform: translateX(0%); }
            100% { transform: translateX(-50%); }
        }
    </style>

    <!-- FULLSCREEN EMERGENCY VISUAL FLASH CALL OVERLAY -->
    <div x-show="isCalling"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-zinc-950/98 backdrop-blur-md p-6"
    >
        <div class="relative w-full max-w-4xl bg-zinc-900 border-4 border-emerald-500 rounded-3xl p-12 text-center shadow-[0_0_50px_rgba(16,185,129,0.3)] space-y-6 flex flex-col justify-center items-center overflow-hidden animate-[pulse_1.5s_infinite]">
            <span class="text-xs font-black text-emerald-400 uppercase tracking-[0.25em] bg-emerald-500/10 border border-emerald-500/20 px-6 py-2 rounded-full animate-bounce">
                PANGGILAN ANTREAN AKTIF
            </span>

            <div class="space-y-2">
                <h3 class="text-2xl font-bold text-zinc-400 uppercase tracking-widest">NOMOR ANTREAN</h3>
                <div class="text-9xl font-black font-mono text-white tracking-widest drop-shadow-[0_0_20px_rgba(255,255,255,0.4)]">
                    <span x-text="activeCall ? activeCall.nomor_antrean : '---'"></span>
                </div>
            </div>

            <div class="w-full max-w-lg border-t-2 border-zinc-800/80 pt-6 space-y-3">
                <h4 class="text-3xl font-extrabold text-emerald-400" x-text="activeCall ? activeCall.poli_tujuan : '-'"></h4>
                <p class="text-xl font-medium text-zinc-300" x-text="activeCall ? activeCall.nama_pasien : '-'"></p>
                <p class="text-sm font-semibold tracking-wider text-zinc-500 uppercase" x-text="activeCall ? activeCall.nama_dokter : '-'"></p>
            </div>
        </div>
    </div>
</div>
