@if ($showPcareReferralForm)
<div class="bg-gradient-to-br from-indigo-50/50 to-emerald-50/30 dark:from-zinc-950 dark:to-zinc-900/50 border-2 border-indigo-200 dark:border-indigo-950/80 rounded-2xl p-6 shadow-lg mb-6 ring-4 ring-indigo-500/5 transition-all duration-300">
    <div class="flex items-center justify-between pb-4 border-b border-indigo-100 dark:border-indigo-950 mb-6">
        <div class="flex items-center gap-3">
            <div class="p-2.5 bg-indigo-600 text-white rounded-xl shadow-md shadow-indigo-500/20">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                </svg>
            </div>
            <div>
                <flux:heading size="xl" class="font-black text-indigo-900 dark:text-indigo-100 tracking-tight">Rujukan Keluar BPJS (PCare Bridging)</flux:heading>
                <flux:subheading class="text-xs text-indigo-600/80 dark:text-indigo-400/80 font-medium">Pengiriman data rujukan real-time langsung ke server BPJS Kesehatan</flux:subheading>
            </div>
        </div>
        <flux:button variant="ghost" icon="x-mark" size="sm" wire:click="togglePcareReferral" class="text-zinc-400 hover:text-zinc-600 rounded-lg" />
    </div>

    <!-- Section 1: Active Live States (Read-Only Summary) -->
    <div class="bg-white/80 dark:bg-zinc-900/60 border border-zinc-100 dark:border-zinc-800/80 rounded-xl p-4 shadow-xs mb-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Status Pasien & TTV</h4>
            <div class="text-sm space-y-1">
                <div><span class="text-zinc-400">No. Kartu BPJS:</span> <span class="font-mono font-bold text-zinc-900 dark:text-white">{{ $record->pasien?->no_bpjs ?: '-' }}</span></div>
                <div><span class="text-zinc-400">Tensi Darah (TTV):</span> <span class="font-bold text-zinc-900 dark:text-white">{{ $tensi_sistole ?: '-' }}/{{ $tensi_diastole ?: '-' }} mmHg</span></div>
                <div><span class="text-zinc-400">Kesadaran:</span> <span class="text-zinc-800 dark:text-zinc-200">{{ $kesadaran_gcs ?: '-' }}</span></div>
            </div>
        </div>

        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Anamnesa (SOAPE)</h4>
            <div class="text-sm">
                <span class="text-zinc-400">Keluhan Utama (S):</span>
                <p class="text-xs text-zinc-700 dark:text-zinc-300 mt-1 line-clamp-2 leading-relaxed bg-zinc-50 dark:bg-zinc-950 p-2 rounded-md border border-zinc-100 dark:border-zinc-800">
                    {{ $subjective ?: 'Tidak ada catatan subjektif' }}
                </p>
            </div>
        </div>

        <div class="space-y-3">
            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400">Diagnosa & Dokter</h4>
            <div class="text-sm space-y-1">
                @php
                    $primaryIcd10 = collect($selectedIcd10s ?? [])->first(fn($icd) => !empty($icd['is_primary']));
                    $isNonSpesialistik = $primaryIcd10 ? $this->isNonSpesialistik($primaryIcd10['kode']) : false;
                @endphp
                <div>
                    <span class="text-zinc-400">Diagnosa Utama:</span> 
                    @if ($primaryIcd10)
                        <span class="inline-flex items-center gap-1 font-bold text-zinc-900 dark:text-white">
                            <span class="font-mono text-emerald-600">{{ $primaryIcd10['kode'] }}</span> - {{ $primaryIcd10['nama_penyakit'] }}
                        </span>
                        @if ($isNonSpesialistik)
                            <span class="block mt-1 text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-950/30 px-2 py-0.5 rounded border border-red-200/50 w-max">Diagnosa Non-Spesialistik (TACC Wajib)</span>
                        @endif
                    @else
                        <span class="text-red-500 italic">Belum ada diagnosa utama terpilih</span>
                    @endif
                </div>
                <div>
                    <span class="text-zinc-400">Dokter Perujuk:</span> 
                    <span class="font-bold text-zinc-800 dark:text-zinc-200">dr. {{ $record->dokter->nama_petugas ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Referral Bridging Input Fields -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Specialty & Date -->
        <div class="space-y-4">
            @php
                $bpjsSpecialties = \App\Models\MasterSpesialisBpjs::where('is_active', true)->orderBy('nama_spesialis')->get();
            @endphp
            <div>
                <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-1">Poli Spesialis Rujukan</label>
                <select wire:model.live="rujuk_spesialis" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-xs">
                    <option value="">Pilih Spesialis</option>
                    @foreach ($bpjsSpecialties as $spec)
                        <option value="{{ $spec->kode_spesialis }}">{{ $spec->nama_spesialis }} ({{ $spec->kode_spesialis }})</option>
                    @endforeach
                </select>
                @error('rujuk_spesialis') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:input type="date" wire:model.live="rujuk_tanggal_est" label="Tgl Rencana Rujukan" min="{{ date('Y-m-d') }}" />
                <flux:input wire:model="rujuk_subspesialis" label="Subspesialis (Opsional)" placeholder="Contoh: Kardiologi Anak" />
            </div>
        </div>

        <!-- Target Hospital & Sarana (Reactive Cascading Dropdowns) -->
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-1">Rumah Sakit Tujuan (Real-time BPJS)</label>
                <select wire:model.live="rujuk_ppk_kode" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-xs" :disabled="empty($rujuk_spesialis)">
                    <option value="">Pilih Rumah Sakit</option>
                    @forelse ($availableHospitals as $hosp)
                        <option value="{{ $hosp['kode_faskes'] }}">
                            {{ $hosp['nama_faskes'] }} [Jarak: {{ $hosp['jarak'] }} | Kuota: {{ $hosp['kuota'] }}/{{ $hosp['kapasitas'] }}]
                        </option>
                    @empty
                        @if (empty($rujuk_spesialis))
                            <option value="">Tentukan spesialis rujukan terlebih dahulu</option>
                        @else
                            <option value="">Tidak ada Faskes tujuan yang tersedia</option>
                        @endif
                    @endforelse
                </select>
                @error('rujuk_ppk_kode') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-1">Sarana Rujukan (Poli Penunjang)</label>
                <select wire:model.live="rujuk_sarana" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-xs" :disabled="empty($rujuk_ppk_kode)">
                    <option value="">Pilih Sarana</option>
                    @forelse ($availableSaranas as $sarana)
                        <option value="{{ $sarana['kode_sarana'] }}">{{ $sarana['nama_sarana'] }}</option>
                    @empty
                        @if (empty($rujuk_ppk_kode))
                            <option value="">Pilih Rumah Sakit terlebih dahulu</option>
                        @else
                            <option value="">Tidak ada sarana pendukung</option>
                        @endif
                    @endforelse
                </select>
            </div>
        </div>
    </div>

    <!-- Section 3: TACC Conditional Matrix -->
    @if ($isNonSpesialistik)
    <div class="bg-red-50 dark:bg-red-950/20 border-l-4 border-red-500 rounded-r-xl p-5 mb-6 space-y-4">
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 text-red-600 shrink-0 mt-0.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
                <h5 class="text-sm font-bold text-red-900 dark:text-red-100">Validasi TACC (Time, Age, Comorbidity, Complication) Diperlukan</h5>
                <p class="text-xs text-red-700 dark:text-red-300 mt-0.5">Diagnosa penyakit ini dikelompokkan ke dalam kategori Non-Spesialistik. Rujukan hanya diperbolehkan jika memenuhi salah satu justifikasi klinis di bawah ini.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-start">
            <div class="md:col-span-1">
                <label class="block text-xs font-bold text-red-800 dark:text-red-200 mb-1">Jenis TACC</label>
                <select wire:model.live="rujuk_tacc_jenis" class="block w-full rounded-lg border-red-200 dark:border-red-900 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-red-900 dark:text-white focus:border-red-500 focus:ring-red-500 shadow-xs">
                    <option value="">Pilih Kategori TACC</option>
                    <option value="Time">Time (Waktu/Durasi Penyakit)</option>
                    <option value="Age">Age (Karakteristik Usia Pasien)</option>
                    <option value="Comorbidity">Comorbidity (Adanya Penyakit Penyerta)</option>
                    <option value="Complication">Complication (Adanya Komplikasi Penyakit)</option>
                </select>
                @error('rujuk_tacc_jenis') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-red-800 dark:text-red-200 mb-1">Alasan Klinis TACC</label>
                <textarea wire:model.live="rujuk_tacc_alasan" rows="2" placeholder="Tuliskan detail catatan klinis/alasan merujuk..." class="block w-full rounded-lg border-red-200 dark:border-red-900 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-red-900 dark:text-white focus:border-red-500 focus:ring-red-500 shadow-xs"></textarea>
                @error('rujuk_tacc_alasan') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
    @endif

    <!-- Action Bar -->
    <div class="flex justify-end items-center gap-3 pt-4 border-t border-indigo-100 dark:border-indigo-950">
        <flux:button variant="ghost" wire:click="togglePcareReferral" class="text-zinc-500 hover:text-zinc-700">Batal</flux:button>
        <flux:button variant="primary" color="indigo" wire:click="saveAndSubmitRujukan" class="font-bold flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
            </svg>
            <span>Kirim Rujukan ke PCare</span>
        </flux:button>
    </div>
</div>
@endif
