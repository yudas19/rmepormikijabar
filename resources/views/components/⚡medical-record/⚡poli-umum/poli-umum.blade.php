<div class="space-y-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">POLIKLINIK UMUM</flux:heading>
                    <flux:badge color="blue" size="md">Workspace</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">No. Antrean: <span class="font-bold text-zinc-900 dark:text-white">{{ $record->nomor_antrean }}</span> | Encounter ID: <span class="font-mono text-xs">{{ $record->encounter_id }}</span></flux:subheading>
                <div class="mt-3 max-w-[200px]">
                    <flux:input type="date" wire:model="tanggal_kunjungan" label="Tanggal Kunjungan" :disabled="!$isEditable" />
                </div>
            </div>

            <div class="flex items-center gap-2 bg-zinc-50 dark:bg-zinc-950 p-2 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80">
                @if ($isEditable)
                <button type="button" wire:click="changeStatus('anamnesis')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $status === 'anamnesis' ? 'bg-orange-500 text-white shadow-xs' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Anamnesis
                </button>
                <div class="text-zinc-300 dark:text-zinc-800">/</div>
                <button type="button" wire:click="changeStatus('waiting_doctor')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $status === 'waiting_doctor' ? 'bg-amber-500 text-white shadow-xs' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Menunggu Dokter
                </button>
                <div class="text-zinc-300 dark:text-zinc-800">/</div>
                <button type="button" wire:click="changeStatus('examination')" class="px-3 py-1.5 rounded-md text-xs font-semibold transition-all {{ $status === 'examination' ? 'bg-blue-500 text-white shadow-xs' : 'text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300' }}">
                    Pemeriksaan
                </button>
                @else
                <flux:badge color="green" size="md" icon="check-circle" class="px-3 py-1.5 font-bold">Pemeriksaan Selesai (Locked)</flux:badge>
                @if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('rekam_medis'))
                <flux:button variant="filled" color="red" size="xs" wire:click="$set('isEditable', true)" class="ml-2 font-bold">
                    Edit / Buka Kunci
                </flux:button>
                @endif
                @endif
            </div>
        </div>
    </div>

    <div x-data="{ activeTab: localStorage.getItem('active_tab_{{ $record->id }}') || 'ttv' }" x-init="$watch('activeTab', val => localStorage.setItem('active_tab_{{ $record->id }}', val))" class="space-y-6">
        <!-- Tabs Menu Bar -->
        <div class="bg-zinc-100 dark:bg-zinc-800/50 p-1.5 rounded-2xl flex flex-wrap gap-1 border border-zinc-200/50 dark:border-zinc-800/40">
            <button type="button" @click="activeTab = 'ttv'" :class="activeTab === 'ttv' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.heart class="w-4 h-4" />
                TTV & Fisik
            </button>
            <button type="button" @click="activeTab = 'soape'" :class="activeTab === 'soape' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.document-text class="w-4 h-4" />
                SOAPE
            </button>
            <button type="button" @click="activeTab = 'diagnosis'" :class="activeTab === 'diagnosis' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.clipboard class="w-4 h-4" />
                Diagnosis (ICD)
            </button>
            <button type="button" @click="activeTab = 'resep'" :class="activeTab === 'resep' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                Resep Obat
            </button>
            <button type="button" @click="activeTab = 'lab'" :class="activeTab === 'lab' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.beaker class="w-4 h-4" />
                Pemeriksaan Lab
            </button>
            <button type="button" @click="activeTab = 'dokumen'" :class="activeTab === 'dokumen' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.bookmark class="w-4 h-4" />
                Dokumen & Surat
            </button>
        </div>

        <!-- Tab Content Pane: TTV & Fisik -->
        <div x-show="activeTab === 'ttv'" class="space-y-6" x-transition>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.heart class="w-5 h-5 text-red-500" />
                    <flux:heading size="lg" class="font-bold">1. Tanda-Tanda Vital (TTV) & Pemeriksaan Fisik</flux:heading>
                </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="grid grid-cols-2 gap-2">
                <flux:input wire:model.live="tensi_sistole" label="Sistole" placeholder="120" type="number" suffix="mmHg" :disabled="!$isEditable" />
                <flux:input wire:model.live="tensi_diastole" label="Diastole" placeholder="80" type="number" suffix="mmHg" :disabled="!$isEditable" />
            </div>

            <flux:input wire:model.live="pulse_rate" label="Nadi (Pulse Rate)" placeholder="80" type="number" suffix="x/mnt" :disabled="!$isEditable" />

            <flux:input wire:model.live="respiratory_rate" label="Napas (Resp. Rate)" placeholder="20" type="number" suffix="x/mnt" :disabled="!$isEditable" />

            <flux:input wire:model.live="temperature" label="Suhu Tubuh" placeholder="36.5" type="number" step="0.1" suffix="°C" :disabled="!$isEditable" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <flux:input wire:model.live="height" label="Tinggi Badan" placeholder="170" type="number" suffix="cm" :disabled="!$isEditable" />

            <flux:input wire:model.live="weight" label="Berat Badan" placeholder="60.5" type="number" step="0.1" suffix="kg" :disabled="!$isEditable" />

            <div class="grid gap-2">
                <flux:label>BMI (Body Mass Index)</flux:label>
                <div class="flex items-center h-10 px-3 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950 font-semibold font-mono text-zinc-800 dark:text-zinc-200">
                    {{ $bmi ?? '-' }}
                    @if ($bmi)
                    <span class="ml-2 text-xs">
                        @if ($bmi < 18.5) <flux:badge size="sm" color="blue">Kurus</flux:badge>
                            @elseif ($bmi < 25) <flux:badge size="sm" color="green">Normal</flux:badge>
                                @elseif ($bmi < 30) <flux:badge size="sm" color="orange">Gemuk</flux:badge>
                                    @else <flux:badge size="sm" color="red">Obesitas</flux:badge>
                                    @endif
                    </span>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Keadaan Umum</label>
                <select wire:model="keadaan_umum" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                    <option value="Good">Baik (Good)</option>
                    <option value="Moderate">Sedang (Moderate)</option>
                    <option value="Weak">Lemah (Weak)</option>
                </select>
            </div>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-950/40 p-5 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 mt-6 space-y-4">
            <flux:heading size="md" class="font-bold">Skala Koma Glasgow (GCS) & Kesadaran</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Tingkat Kesadaran</label>
                    <select wire:model="kesadaran_gcs" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                        <option value="Compos Mentis">Compos Mentis (Sadar Penuh)</option>
                        <option value="Apatis">Apatis (Acuh tak acuh)</option>
                        <option value="Somnolen">Somnolen (Mengantuk)</option>
                        <option value="Sopor">Sopor (Setengah Koma)</option>
                        <option value="Coma">Koma (Coma)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Eye - Refleks Mata</label>
                    <select wire:model.live="gcs_eye" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                        <option value="4">4 - Spontan membuka mata</option>
                        <option value="3">3 - Membuka mata terhadap suara</option>
                        <option value="2">2 - Membuka mata terhadap nyeri</option>
                        <option value="1">1 - Tidak merespons</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Verbal - Respons Suara</label>
                    <select wire:model.live="gcs_verbal" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                        <option value="5">5 - Orientasi baik & lancar</option>
                        <option value="4">4 - Bingung, bicara kacau</option>
                        <option value="3">3 - Kata-kata tidak teratur</option>
                        <option value="2">2 - Suara menggumam/mengerang</option>
                        <option value="1">1 - Tidak ada suara</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Motorik - Respons Gerak</label>
                    <select wire:model.live="gcs_motor" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                        <option value="6">6 - Mematuhi perintah gerak</option>
                        <option value="5">5 - Mengetahui lokasi nyeri</option>
                        <option value="4">4 - Menarik tubuh terhadap nyeri</option>
                        <option value="3">3 - Fleksi abnormal (Dekortikasi)</option>
                        <option value="2">2 - Ekstensi abnormal (Deserebrasi)</option>
                        <option value="1">1 - Tidak ada gerakan</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <span class="text-xs font-semibold">Total Skor GCS:</span>
                <flux:badge color="{{ $gcs_score >= 13 ? 'green' : ($gcs_score >= 9 ? 'yellow' : 'red') }}" size="md" class="font-mono text-xs font-bold">
                    E{{ $gcs_eye }}V{{ $gcs_verbal }}M{{ $gcs_motor }} = Score {{ $gcs_score }}
                </flux:badge>
            </div>
        </div>

        <div class="flex justify-between items-center mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
            <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
                @if ($record->perawat)
                    ⚠️ Diinput oleh: <span class="font-bold text-zinc-700 dark:text-zinc-300">{{ $record->perawat->nama_petugas }}</span> pada {{ $record->created_at->format('d-m-Y H:i') }}
                @else
                    ⚠️ Diinput oleh: - pada -
                @endif
            </div>
            @if ($isEditable)
                <flux:button size="sm" variant="filled" color="teal" icon="document-check" wire:click="saveTtv">Simpan TTV</flux:button>
            @endif
        </div>
    </div>
</div> {{-- End of TTV Tab --}}

<!-- Tab Content Pane: SOAPE -->
<div x-show="activeTab === 'soape'" class="space-y-6" x-transition>
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.document-text class="w-5 h-5 text-blue-500" />
            <flux:heading size="lg" class="font-bold">2. Deskripsi Medis (SOAPE)</flux:heading>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:textarea wire:model="keluhan_utama" label="Keluhan Utama (Chief Complaint)" placeholder="Masukkan keluhan utama..." rows="2" :disabled="!$isEditable" />
                <flux:textarea wire:model="riwayat_alergi" label="Riwayat Alergi (Allergy History)" placeholder="Masukkan riwayat alergi jika ada..." rows="2" :disabled="!$isEditable" />
            </div>

            <flux:textarea wire:model="subjective" label="Subjective (S)" placeholder="Keluhan utama pasien, keluhan tambahan, riwayat alergi, riwayat penyakit saat ini..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="objective" label="Objective (O)" placeholder="Hasil pemeriksaan fisik, status lokalis, inspeksi, palpasi, auskultasi..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="assessment" label="Assessment (A)" placeholder="Analisis diagnosis klinis dokter, diagnosa kerja, diagnosa banding..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="plan" label="Plan (P)" placeholder="Rencana tatalaksana, rencana terapi obat/non-obat, rencana pemeriksaan penunjang..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="evaluation" label="Evaluation (E)" placeholder="Evaluasi klinis pasca tindakan / monitoring perkembangan pasien (Opsional)..." rows="3" :disabled="!$isEditable" />
        </div>
        <div class="flex justify-end mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
            @if ($isEditable)
                <flux:button size="sm" variant="filled" color="blue" icon="document-check" wire:click="saveSoape">Simpan SOAPE</flux:button>
            @endif
        </div>
    </div>
</div> {{-- End of SOAPE Tab --}}

<div x-show="activeTab === 'diagnosis'" class="space-y-6" x-transition>
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.hashtag class="w-5 h-5 text-indigo-500" />
            <flux:heading size="lg" class="font-bold">3. Kode Diagnosis (ICD-10) & Prosedur (ICD-9)</flux:heading>
        </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-4">
            <flux:heading size="md" class="font-bold">Diagnosis ICD-10</flux:heading>

            @if ($isEditable)
            <div class="relative">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="icd10Query" placeholder="Cari Kode atau Nama Diagnosa ICD-10..." class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 pl-10 pr-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>

                @if (count($icd10Results) > 0)
                <div wire:key="icd10-dropdown-list-container" class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                    @foreach ($icd10Results as $res)
                    <button type="button" wire:click="selectIcd10({{ $res['id'] }})" wire:key="icd10-item-option-{{ $res['id'] }}-{{ $loop->index }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2 transition-colors">
                        <span class="font-bold font-mono text-zinc-900 dark:text-white">{{ $res['kode'] }}</span>
                        <span class="text-zinc-600 dark:text-zinc-300 truncate">{{ $res['nama_penyakit_indonesia'] ?? $res['nama_penyakit'] }}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <div class="space-y-2 max-h-60 overflow-y-auto">
                @forelse ($selectedIcd10s as $index => $icd)
                <div wire:key="icd10-selected-row-{{ $icd['id'] ?? $index }}-{{ $index }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
                    <div class="flex items-center gap-3">
                        <span class="font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $icd['kode'] }}</span>
                        <span class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $icd['nama_penyakit'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (isset($icd['is_primary']) && $icd['is_primary'])
                        <span class="inline-flex items-center rounded-md bg-green-50 dark:bg-green-900/30 px-2 py-1 text-xs font-medium text-green-700 dark:text-green-400 ring-1 ring-inset ring-green-600/20">Utama</span>
                        @elseif ($isEditable)
                        <button type="button" wire:click="setPrimaryIcd10({{ $index }})" wire:key="btn-primary-icd10-{{ $index }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 bg-transparent px-2 py-1 rounded">Set Utama</button>
                        @endif

                        @if ($isEditable)
                        <button type="button" wire:click="removeIcd10({{ $index }})" wire:key="btn-delete-icd10-{{ $index }}" class="text-zinc-400 hover:text-red-500 p-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6m-4.74 0L9.26 15m9.96-3-.34 22m-1.53 4.41a5 5 0 0 1-5.4 0L2.25 7.5m10.5 0v-1.5a3 3 0 0 0-3-3h-1.5a3 3 0 0 0-3 3v1.5m10.5 0H1.5" /></svg>
                        </button>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">Belum ada diagnosis terpilih.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-4">
            <flux:heading size="md" class="font-bold">Prosedur Medis ICD-9</flux:heading>

            @if ($isEditable)
            <div class="relative">
                <div class="relative rounded-md shadow-sm">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                    </div>
                    <input type="text" wire:model.live.debounce.250ms="icd9Query" placeholder="Cari Kode atau Nama Prosedur ICD-9..." class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 pl-10 pr-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                </div>

                @if (count($icd9Results) > 0)
                <div wire:key="icd9-dropdown-list-container" class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                    @foreach ($icd9Results as $res)
                    <button type="button" wire:click="selectIcd9({{ $res['id'] }})" wire:key="icd9-item-option-{{ $res['id'] }}-{{ $loop->index }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2 transition-colors">
                        <span class="font-bold font-mono text-zinc-900 dark:text-white">{{ $res['kode'] }}</span>
                        <span class="text-zinc-600 dark:text-zinc-300 truncate">{{ $res['nama'] }}</span>
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @endif

            <div class="space-y-2 max-h-60 overflow-y-auto">
                @forelse ($selectedIcd9s as $index => $icd)
                <div wire:key="icd9-selected-row-{{ $icd['id'] ?? $index }}-{{ $index }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
                    <div class="flex items-center gap-3">
                        <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $icd['kode'] }}</span>
                        <span class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $icd['nama'] }}</span>
                    </div>
                    @if ($isEditable)
                    <button type="button" wire:click="removeIcd9({{ $index }})" wire:key="btn-delete-icd9-{{ $index }}" class="text-zinc-400 hover:text-red-500 p-1 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.34 6m-4.74 0L9.26 15m9.96-3-.34 22m-1.53 4.41a5 5 0 0 1-5.4 0L2.25 7.5m10.5 0v-1.5a3 3 0 0 0-3-3h-1.5a3 3 0 0 0-3 3v1.5m10.5 0H1.5" /></svg>
                    </button>
                    @endif
                </div>
                @empty
                <div class="text-center py-4 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">Belum ada prosedur terpilih.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
        <div class="text-xs text-zinc-500 dark:text-zinc-400 font-medium">
            @if ($record && $record->dokter)
                🩺 Dokter Penanggung Jawab: <span class="font-bold text-zinc-700 dark:text-zinc-300">dr. {{ $record->dokter->nama_petugas }}</span> (SIP: {{ $record->dokter->nomor_sip ?? '-' }}) pada {{ $record->updated_at->format('d-m-Y H:i') }}
            @else
                🩺 Dokter Penanggung Jawab: dr. - (SIP: -) pada -
            @endif
        </div>
        @if ($isEditable)
            <flux:button size="sm" variant="filled" color="indigo" icon="document-check" wire:click="saveIcd">Simpan Diagnosis</flux:button>
        @endif
    </div>
</div>
</div> {{-- End of Diagnosis Tab --}}

<div class="flex justify-end bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
        <div class="flex gap-2">
            @if ($isEditable)
            <flux:button variant="filled" wire:click="saveDraft">Save as Draft</flux:button>
            <flux:button variant="primary" wire:click="finalizeAndLock">Finalize & Lock</flux:button>
            @else
            <flux:badge color="zinc" size="md">Lock State: Tidak dapat diedit</flux:badge>
            @endif
        </div>
    </div>
</div> {{-- End of parent space-y-6 container --}}


    @if ($showSickLeaveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Buat Surat Keterangan Sakit</flux:heading>
                <button type="button" wire:click="$set('showSickLeaveModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="generateSickLeave" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <flux:input wire:model="sick_start_date" type="date" label="Tanggal Mulai" required />
                    <flux:input wire:model="sick_end_date" type="date" label="Tanggal Selesai" required />
                </div>

                <flux:input wire:model="sick_diagnose" label="Diagnosis Ringkasan (Opsional)" placeholder="Contoh: Febris, Dyspepsia" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter TTD</label>
                    <select wire:model="sick_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="sick-doc-opt-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showSickLeaveModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showHealthCertModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Buat Surat Keterangan Sehat</flux:heading>
                <button type="button" wire:click="$set('showHealthCertModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="generateHealthCert" class="p-6 space-y-4">
                <div class="grid grid-cols-4 gap-4">
                    <flux:input wire:model="health_height" label="Tinggi Badan (cm)" required type="number" />
                    <flux:input wire:model="health_weight" label="Berat Badan (kg)" required type="number" />
                    <flux:input wire:model="health_tensi" label="Tekanan Darah" required placeholder="120/80" />
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Gol. Darah</label>
                        <select wire:model="health_golongan_darah" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="AB">AB</option>
                            <option value="O">O</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Buta Warna</label>
                        <select wire:model="health_butawarna" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="Tidak">Tidak Buta Warna</option>
                            <option value="Ya">Buta Warna</option>
                        </select>
                    </div>
                    <flux:input wire:model="health_catatan" label="Kesimpulan / Catatan" required />
                </div>

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter TTD</label>
                    <select wire:model="health_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="health-doc-opt-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showHealthCertModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showReferralModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Buat Surat Rujukan Eksternal</flux:heading>
                <button type="button" wire:click="$set('showReferralModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="generateReferral" class="p-6 space-y-4">
                <flux:input wire:model="ref_faskes_tujuan" label="Faskes Rujukan Tujuan" required placeholder="Contoh: RSUD Kabupaten Karawang" />
                <flux:input wire:model="ref_diagnosa" label="Diagnosis Utama Rujukan" required placeholder="Contoh: Hipertensi, Susp. Appendicitis" />
                <flux:textarea wire:model="ref_catatan" label="Catatan Penanganan Awal / Terapi Tindakan" placeholder="Deskripsikan terapi awal atau alasan dirujuk..." rows="3" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter Merujuk</label>
                    <select wire:model="ref_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="ref-doc-opt-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showReferralModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showNarkobaModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Buat Surat Keterangan Bebas Narkoba</flux:heading>
                <button type="button" wire:click="$set('showNarkobaModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="generateNarkoba" class="p-6 space-y-4">
                <flux:input wire:model="narkoba_keperluan" label="Keperluan Pembuatan Surat" required placeholder="Contoh: Melamar Pekerjaan, Persyaratan Sekolah" />
                <flux:textarea wire:model="narkoba_hasil" label="Hasil Pemeriksaan Laboratorium Urin" required rows="3" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter TTD</label>
                    <select wire:model="narkoba_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="narkoba-doc-opt-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showNarkobaModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showConsentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">
                    {{ $consent_type === 'general_consent' ? 'Buat Persetujuan Umum (General Consent)' : 'Buat Persetujuan Tindakan (Informed Consent)' }}
                </flux:heading>
                <button type="button" wire:click="$set('showConsentModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="generateConsent" class="p-6 space-y-4">
                <flux:input wire:model="consent_nama_penanggung_jawab" label="Nama Penanggung Jawab" required placeholder="Nama lengkap" />

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Hubungan Penanggung Jawab</label>
                        <select wire:model="consent_hubungan_penanggung_jawab" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="diri_sendiri">Diri Sendiri</option>
                            <option value="suami">Suami</option>
                            <option value="istri">Istri</option>
                            <option value="ayah">Ayah</option>
                            <option value="ibu">Ibu</option>
                            <option value="anak">Anak</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <flux:input wire:model="consent_nik_penanggung_jawab" label="NIK Penanggung Jawab" placeholder="16 Digit NIK" class="font-mono" />
                </div>

                @if ($consent_type === 'informed_consent_tindakan')
                <flux:input wire:model="consent_nama_tindakan_medis" label="Nama Tindakan Medis" required placeholder="Contoh: Jahit Luka, Ekstraksi Gigi" />
                @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Pernyataan</label>
                        <select wire:model="consent_pernyataan" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="setuju">Setuju (MENYETUJUI)</option>
                            <option value="menolak">Menolak (MENOLAK)</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Saksi Klinik (Petugas/Perawat)</label>
                        <select wire:model="consent_petugas_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                            <option value="">Pilih Saksi</option>
                            @foreach ($staff as $st)
                            <option value="{{ $st->id }}" wire:key="consent-staff-opt-{{ $st->id }}">{{ $st->nama_petugas }} ({{ $st->jenis_petugas }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showConsentModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-print-tab', (data) => {
                const eventData = Array.isArray(data) ? data[0] : data;
                if (eventData && eventData.url) {
                    window.open(eventData.url, '_blank');
                }
            });
        });
    </script>
</div>