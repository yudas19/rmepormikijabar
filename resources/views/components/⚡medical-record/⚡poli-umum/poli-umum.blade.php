<div class="space-y-6">
    <!-- Workspace Header / Status Bar -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">POLIKLINIK UMUM</flux:heading>
                    <flux:badge color="blue" size="md">Workspace</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">No. Antrean: <span class="font-bold text-zinc-900 dark:text-white">{{ $record->nomor_antrean }}</span> | Encounter ID: <span class="font-mono text-xs">{{ $record->encounter_id }}</span></flux:subheading>
            </div>

            <!-- Interactive Status Step Progress -->
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
                @endif
            </div>
        </div>
    </div>

    <!-- TTV & Physical Exam Section -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.heart class="w-5 h-5 text-red-500" />
            <flux:heading size="lg" class="font-bold">1. Tanda-Tanda Vital (TTV) & Pemeriksaan Fisik</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Blood Pressure -->
            <div class="grid grid-cols-2 gap-2">
                <flux:input wire:model.live="tensi_sistole" label="Sistole" placeholder="120" type="number" suffix="mmHg" :disabled="!$isEditable" />
                <flux:input wire:model.live="tensi_diastole" label="Diastole" placeholder="80" type="number" suffix="mmHg" :disabled="!$isEditable" />
            </div>

            <!-- Pulse Rate -->
            <flux:input wire:model.live="pulse_rate" label="Nadi (Pulse Rate)" placeholder="80" type="number" suffix="x/mnt" :disabled="!$isEditable" />

            <!-- Respiratory Rate -->
            <flux:input wire:model.live="respiratory_rate" label="Napas (Resp. Rate)" placeholder="20" type="number" suffix="x/mnt" :disabled="!$isEditable" />

            <!-- Temperature -->
            <flux:input wire:model.live="temperature" label="Suhu Tubuh" placeholder="36.5" type="number" step="0.1" suffix="°C" :disabled="!$isEditable" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <!-- Height -->
            <flux:input wire:model.live="height" label="Tinggi Badan" placeholder="170" type="number" suffix="cm" :disabled="!$isEditable" />

            <!-- Weight -->
            <flux:input wire:model.live="weight" label="Berat Badan" placeholder="60.5" type="number" step="0.1" suffix="kg" :disabled="!$isEditable" />

            <!-- BMI (Auto Calculated) -->
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

            <!-- Keadaan Umum -->
            <flux:select wire:model="keadaan_umum" label="Keadaan Umum" :disabled="!$isEditable">
                <flux:select.option value="Good">Baik (Good)</flux:select.option>
                <flux:select.option value="Moderate">Sedang (Moderate)</flux:select.option>
                <flux:select.option value="Weak">Lemah (Weak)</flux:select.option>
            </flux:select>
        </div>

        <!-- GCS / Consciousness Section -->
        <div class="bg-zinc-50 dark:bg-zinc-950/40 p-5 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 mt-6 space-y-4">
            <flux:heading size="md" class="font-bold">Skala Koma Glasgow (GCS) & Kesadaran</flux:heading>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Consciousness Dropdown -->
                <flux:select wire:model="kesadaran_gcs" label="Tingkat Kesadaran" :disabled="!$isEditable">
                    <flux:select.option value="Compos Mentis">Compos Mentis (Sadar Penuh)</flux:select.option>
                    <flux:select.option value="Apatis">Apatis (Acuh tak acuh)</flux:select.option>
                    <flux:select.option value="Somnolen">Somnolen (Mengantuk)</flux:select.option>
                    <flux:select.option value="Sopor">Sopor (Setengah Koma)</flux:select.option>
                    <flux:select.option value="Coma">Koma (Coma)</flux:select.option>
                </flux:select>

                <!-- Eye -->
                <flux:select wire:model.live="gcs_eye" label="Eye (E) - Refleks Mata" :disabled="!$isEditable">
                    <flux:select.option value="4">4 - Spontan membuka mata</flux:select.option>
                    <flux:select.option value="3">3 - Membuka mata terhadap suara</flux:select.option>
                    <flux:select.option value="2">2 - Membuka mata terhadap nyeri</flux:select.option>
                    <flux:select.option value="1">1 - Tidak merespons</flux:select.option>
                </flux:select>

                <!-- Verbal -->
                <flux:select wire:model.live="gcs_verbal" label="Verbal (V) - Respons Suara" :disabled="!$isEditable">
                    <flux:select.option value="5">5 - Orientasi baik & lancar</flux:select.option>
                    <flux:select.option value="4">4 - Bingung, bicara kacau</flux:select.option>
                    <flux:select.option value="3">3 - Kata-kata tidak teratur</flux:select.option>
                    <flux:select.option value="2">2 - Suara menggumam/mengerang</flux:select.option>
                    <flux:select.option value="1">1 - Tidak ada suara</flux:select.option>
                </flux:select>

                <!-- Motor -->
                <flux:select wire:model.live="gcs_motor" label="Motorik (M) - Respons Gerak" :disabled="!$isEditable">
                    <flux:select.option value="6">6 - Mematuhi perintah gerak</flux:select.option>
                    <flux:select.option value="5">5 - Mengetahui lokasi nyeri</flux:select.option>
                    <flux:select.option value="4">4 - Menarik tubuh terhadap nyeri</flux:select.option>
                    <flux:select.option value="3">3 - Fleksi abnormal (Dekortikasi)</flux:select.option>
                    <flux:select.option value="2">2 - Ekstensi abnormal (Deserebrasi)</flux:select.option>
                    <flux:select.option value="1">1 - Tidak ada gerakan</flux:select.option>
                </flux:select>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <span class="text-sm font-semibold">Total Skor GCS:</span>
                <flux:badge color="{{ $gcs_score >= 13 ? 'green' : ($gcs_score >= 9 ? 'yellow' : 'red') }}" size="md" class="font-mono text-sm font-bold">
                    E{{ $gcs_eye }}V{{ $gcs_verbal }}M{{ $gcs_motor }} = Score {{ $gcs_score }}
                </flux:badge>
            </div>
        </div>
    </div>

    <!-- SOAPE Section -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.document-text class="w-5 h-5 text-blue-500" />
            <flux:heading size="lg" class="font-bold">2. Deskripsi Medis (SOAPE)</flux:heading>
        </div>

        <div class="space-y-4">
            <flux:textarea wire:model="subjective" label="Subjective (S)" placeholder="Keluhan utama pasien, keluhan tambahan, riwayat alergi, riwayat penyakit saat ini..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="objective" label="Objective (O)" placeholder="Hasil pemeriksaan fisik, status lokalis, inspeksi, palpasi, auskultasi..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="assessment" label="Assessment (A)" placeholder="Analisis diagnosis klinis dokter, diagnosa kerja, diagnosa banding..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="plan" label="Plan (P)" placeholder="Rencana tatalaksana, rencana terapi obat/non-obat, rencana pemeriksaan penunjang..." rows="3" :disabled="!$isEditable" />
            <flux:textarea wire:model="evaluation" label="Evaluation (E)" placeholder="Evaluasi klinis pasca tindakan / monitoring perkembangan pasien (Opsional)..." rows="3" :disabled="!$isEditable" />
        </div>
    </div>

    <!-- ICD-10 & ICD-9 Section -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.hashtag class="w-5 h-5 text-indigo-500" />
            <flux:heading size="lg" class="font-bold">3. Kode Diagnosis (ICD-10) & Prosedur (ICD-9)</flux:heading>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- ICD-10 (Diagnosa) -->
            <div class="space-y-4">
                <flux:heading size="md" class="font-bold">Diagnosis ICD-10</flux:heading>
                
                @if ($isEditable)
                    <div class="relative">
                        <flux:input wire:model.live.debounce.250ms="icd10Query" placeholder="Cari Kode atau Nama Diagnosa ICD-10..." icon="magnifying-glass" />
                        
                        @if (count($icd10Results) > 0)
                            <div class="absolute z-20 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                                @foreach ($icd10Results as $res)
                                    <button type="button" wire:click="selectIcd10({{ $res['id'] }})" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2">
                                        <span class="font-bold font-mono text-zinc-900 dark:text-white">{{ $res['kode'] }}</span>
                                        <span class="text-zinc-600 dark:text-zinc-300 truncate">{{ $res['nama_penyakit_indonesia'] ?? $res['nama_penyakit'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- ICD-10 Added List -->
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @forelse ($selectedIcd10s as $index => $icd)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
                            <div class="flex items-center gap-3">
                                <span class="font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $icd['kode'] }}</span>
                                <span class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $icd['nama_penyakit'] }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @if ($icd['is_primary'])
                                    <flux:badge color="green" size="sm">Utama</flux:badge>
                                @elseif ($isEditable)
                                    <flux:button variant="ghost" size="sm" class="text-xs" wire:click="setPrimaryIcd10({{ $index }})">Set Utama</flux:button>
                                @endif
                                
                                @if ($isEditable)
                                    <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500" wire:click="removeIcd10({{ $index }})" />
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">Belum ada diagnosis terpilih.</div>
                    @endforelse
                </div>
            </div>

            <!-- ICD-9 (Prosedur) -->
            <div class="space-y-4">
                <flux:heading size="md" class="font-bold">Prosedur Medis ICD-9</flux:heading>
                
                @if ($isEditable)
                    <div class="relative">
                        <flux:input wire:model.live.debounce.250ms="icd9Query" placeholder="Cari Kode atau Nama Prosedur ICD-9..." icon="magnifying-glass" />
                        
                        @if (count($icd9Results) > 0)
                            <div class="absolute z-20 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                                @foreach ($icd9Results as $res)
                                    <button type="button" wire:click="selectIcd9({{ $res['id'] }})" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2">
                                        <span class="font-bold font-mono text-zinc-900 dark:text-white">{{ $res['kode'] }}</span>
                                        <span class="text-zinc-600 dark:text-zinc-300 truncate">{{ $res['nama'] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- ICD-9 Added List -->
                <div class="space-y-2 max-h-60 overflow-y-auto">
                    @forelse ($selectedIcd9s as $index => $icd)
                        <div class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
                            <div class="flex items-center gap-3">
                                <span class="font-bold font-mono text-emerald-600 dark:text-emerald-400">{{ $icd['kode'] }}</span>
                                <span class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $icd['nama'] }}</span>
                            </div>
                            @if ($isEditable)
                                <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500" wire:click="removeIcd9({{ $index }})" />
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">Belum ada prosedur terpilih.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Prescriptions (E-Resep) Section -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.beaker class="w-5 h-5 text-emerald-500" />
            <flux:heading size="lg" class="font-bold">4. Rencana Resep Elektronik (E-Resep)</flux:heading>
        </div>

        @if ($isEditable)
            <!-- Prescription Form Builder -->
            <div class="p-5 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl mb-6 space-y-4">
                <div class="flex gap-4">
                    <flux:radio wire:model.live="presc_type" value="non-racikan" label="Non-Racikan (Standar)" />
                    <flux:radio wire:model.live="presc_type" value="racikan" label="Racikan (Compound)" />
                </div>

                @if ($presc_type === 'racikan')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-zinc-200/60 dark:border-zinc-800/80 pb-4">
                        <flux:input wire:model="presc_nama_racikan" label="Nama Kelompok Racikan" placeholder="Contoh: Kapsul Batuk Flu" />
                        <flux:select wire:model="presc_metode_racik_id" label="Metode Compounding">
                            <flux:select.option value="">Pilih Metode</flux:select.option>
                            @foreach ($metodeRaciks as $m)
                                <flux:select.option value="{{ $m->id }}">{{ $m->nama_metode_racik }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input wire:model="presc_jumlah_kemasan" label="Jumlah Kemasan (Caps/Puyer)" type="number" placeholder="10" />
                    </div>
                @endif

                <!-- Drug Add Row -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-6 relative">
                        <flux:input wire:model.live.debounce.250ms="drugQuery" label="Cari & Pilih Obat" placeholder="Ketik nama obat..." />
                        @if (count($drugResults) > 0)
                            <div class="absolute z-30 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                                @foreach ($drugResults as $res)
                                    <button type="button" wire:click="selectDrug({{ $res['id'] }})" class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between items-center">
                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $res['nama_obat'] }}</span>
                                        <flux:badge size="sm" color="zinc">{{ $res['satuan'] }} (Stok: {{ $res['stok'] }})</flux:badge>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="md:col-span-2">
                        <flux:input wire:model="drugQty" label="Jumlah" type="number" step="0.1" placeholder="10" />
                    </div>

                    <div class="md:col-span-4 flex items-end">
                        <flux:button variant="filled" class="w-full h-10" wire:click="addIngredient">
                            {{ $presc_type === 'racikan' ? '+ Tambah Bahan' : '+ Tambah Obat' }}
                        </flux:button>
                    </div>
                </div>

                <!-- Show Ingredients compiler for Racikan -->
                @if ($presc_type === 'racikan' && count($presc_ingredients) > 0)
                    <div class="bg-zinc-100/50 dark:bg-zinc-950 p-4 rounded-lg border border-zinc-200/50 dark:border-zinc-800">
                        <flux:heading size="sm" class="mb-3 font-semibold text-zinc-800 dark:text-zinc-300">Bahan Racikan Terpilih:</flux:heading>
                        <div class="space-y-2">
                            @foreach ($presc_ingredients as $i => $ing)
                                <div class="flex justify-between items-center text-xs p-2 bg-white dark:bg-zinc-900 rounded border border-zinc-150 dark:border-zinc-850">
                                    <span><strong>{{ $ing['nama_obat'] }}</strong> ({{ $ing['jumlah'] }} {{ $ing['satuan'] }})</span>
                                    <flux:button variant="ghost" size="xs" icon="x-mark" class="text-red-500" wire:click="removeIngredient({{ $i }})" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Dosage and packaging logic -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="presc_aturan_pakai" label="Aturan Pakai (Signa)" placeholder="Contoh: 3 x 1 sehari" />
                    <flux:input wire:model="presc_catatan" label="Catatan Konsumsi" placeholder="Contoh: Sesudah makan, dihabiskan" />
                </div>

                @if ($presc_type === 'racikan')
                    <div class="flex justify-end pt-2">
                        <flux:button variant="primary" wire:click="addRacikanPrescription">
                            Tambahkan Kelompok Racikan ke Resep
                        </flux:button>
                    </div>
                @endif
            </div>
        @endif

        <!-- Prescriptions Added Compilation List Table -->
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Tipe Resep</flux:table.column>
                    <flux:table.column>Nama Obat / Racikan</flux:table.column>
                    <flux:table.column>Detail Kandungan</flux:table.column>
                    <flux:table.column>Aturan Pakai</flux:table.column>
                    <flux:table.column>Catatan</flux:table.column>
                    @if ($isEditable)
                        <flux:table.column>Aksi</flux:table.column>
                    @endif
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($prescriptionsList as $index => $presc)
                        <flux:table.row :key="$index">
                            <flux:table.cell>
                                <flux:badge color="{{ $presc['type'] === 'racikan' ? 'purple' : 'zinc' }}" size="sm">
                                    {{ $presc['type'] === 'racikan' ? 'Racikan' : 'Non-Racikan' }}
                                </flux:badge>
                            </flux:table.cell>
                            <flux:table.cell class="font-semibold text-zinc-900 dark:text-white">
                                @if ($presc['type'] === 'racikan')
                                    {{ $presc['nama_racikan'] }}
                                    <div class="text-[10px] text-zinc-500 uppercase font-mono mt-0.5">Metode: {{ $presc['metode_racik_nama'] }} ({{ $presc['jumlah_kemasan'] }} Bks)</div>
                                @else
                                    {{ $presc['items'][0]['nama_obat'] }}
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="text-xs">
                                @if ($presc['type'] === 'racikan')
                                    <ul class="list-disc pl-4 space-y-0.5">
                                        @foreach ($presc['items'] as $item)
                                            <li>{{ $item['nama_obat'] }} <span class="text-zinc-500 font-mono text-[10px]">({{ $item['jumlah'] }} {{ $item['satuan'] }})</span></li>
                                        @endforeach
                                    </ul>
                                @else
                                    {{ $presc['items'][0]['jumlah'] }} {{ $presc['items'][0]['satuan'] }}
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="font-medium font-mono text-xs text-zinc-800 dark:text-zinc-200">{{ $presc['aturan_pakai'] }}</flux:table.cell>
                            <flux:table.cell>{{ $presc['catatan'] ?: '-' }}</flux:table.cell>
                            @if ($isEditable)
                                <flux:table.cell>
                                    <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500" wire:click="removePrescription({{ $index }})" />
                                </flux:table.cell>
                            @endif
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="{{ $isEditable ? 6 : 5 }}" class="text-center text-zinc-500 py-8">Belum ada obat yang diresepkan.</flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    <!-- Action Bar / Buttons -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
        <!-- Cetak Surat Keterangan Dropdown -->
        <flux:dropdown>
            <flux:button icon="document-text" icon-trailing="chevron-down">Cetak Surat Keterangan</flux:button>
            <flux:menu class="min-w-56">
                <flux:menu.item icon="document" wire:click="openSickLeave">Surat Keterangan Sakit (Sick Leave)</flux:menu.item>
                <flux:menu.item icon="document" wire:click="openHealthCert">Surat Keterangan Sehat (Health Cert.)</flux:menu.item>
                <flux:menu.item icon="document-arrow-up" wire:click="openReferral">Surat Rujukan Eksternal (Referral)</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <div class="flex gap-2">
            @if ($isEditable)
                <flux:button variant="filled" wire:click="saveDraft">Save as Draft</flux:button>
                <flux:button variant="primary" wire:click="finalizeAndLock">Finalize & Lock</flux:button>
            @else
                <flux:badge color="zinc" size="md">Lock State: Tidak dapat diedit</flux:badge>
            @endif
        </div>
    </div>

    <!-- ==================== SICK LEAVE MODAL ==================== -->
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

                    <flux:select wire:model="sick_dokter_id" label="Dokter TTD" required>
                        <flux:select.option value="">Pilih Dokter</flux:select.option>
                        @foreach ($doctors as $doc)
                            <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                        <flux:button type="button" variant="filled" wire:click="$set('showSickLeaveModal', false)">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== HEALTH CERTIFICATE MODAL ==================== -->
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
                    <div class="grid grid-cols-3 gap-4">
                        <flux:input wire:model="health_height" label="Tinggi Badan (cm)" required type="number" />
                        <flux:input wire:model="health_weight" label="Berat Badan (kg)" required type="number" />
                        <flux:input wire:model="health_tensi" label="Tekanan Darah" required placeholder="120/80" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:select wire:model="health_butawarna" label="Buta Warna" required>
                            <flux:select.option value="tidak">Tidak Buta Warna</flux:select.option>
                            <flux:select.option value="ya">Buta Warna</flux:select.option>
                        </flux:select>
                        <flux:input wire:model="health_catatan" label="Catatan Medis" required />
                    </div>

                    <flux:select wire:model="health_dokter_id" label="Dokter TTD" required>
                        <flux:select.option value="">Pilih Dokter</flux:select.option>
                        @foreach ($doctors as $doc)
                            <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                        <flux:button type="button" variant="filled" wire:click="$set('showHealthCertModal', false)">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- ==================== REFERRAL MODAL ==================== -->
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

                    <flux:select wire:model="ref_dokter_id" label="Dokter Merujuk" required>
                        <flux:select.option value="">Pilih Dokter</flux:select.option>
                        @foreach ($doctors as $doc)
                            <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                        @endforeach
                    </flux:select>

                    <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                        <flux:button type="button" variant="filled" wire:click="$set('showReferralModal', false)">Batal</flux:button>
                        <flux:button type="submit" variant="primary">Simpan & Cetak</flux:button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Print tab listener script -->
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
