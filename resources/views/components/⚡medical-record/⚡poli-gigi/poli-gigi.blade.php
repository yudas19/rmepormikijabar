<div class="space-y-6">
    {{-- Header Workspace Card --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">KLINIK GIGI</flux:heading>
                    <flux:badge color="teal" size="md">Workspace</flux:badge>
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

    {{-- Upper Section: High-Density 2-Column Grid --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        {{-- Left: TTV & Physical Exam Card --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.heart class="w-5 h-5 text-red-500" />
                    <flux:heading size="lg" class="font-bold">Tanda-Tanda Vital & Fisik</flux:heading>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="col-span-2 grid grid-cols-2 gap-2">
                        <flux:input wire:model.live="tensi_sistole" label="Sistole" placeholder="120" type="number" suffix="mmHg" :disabled="!$isEditable" />
                        <flux:input wire:model.live="tensi_diastole" label="Diastole" placeholder="80" type="number" suffix="mmHg" :disabled="!$isEditable" />
                    </div>
                    <flux:input wire:model.live="pulse_rate" label="Nadi" placeholder="80" type="number" suffix="x/m" :disabled="!$isEditable" />
                    <flux:input wire:model.live="respiratory_rate" label="Napas" placeholder="20" type="number" suffix="x/m" :disabled="!$isEditable" />
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <flux:input wire:model.live="temperature" label="Suhu" placeholder="36.5" type="number" step="0.1" suffix="°C" :disabled="!$isEditable" />
                    <flux:input wire:model.live="height" label="Tinggi" placeholder="170" type="number" suffix="cm" :disabled="!$isEditable" />
                    <flux:input wire:model.live="weight" label="Berat" placeholder="60.5" type="number" step="0.1" suffix="kg" :disabled="!$isEditable" />
                    <div class="grid gap-1">
                        <flux:label class="text-xs">BMI</flux:label>
                        <div class="flex items-center h-10 px-2.5 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950 font-semibold font-mono text-xs text-zinc-800 dark:text-zinc-200">
                            {{ $bmi ?? '-' }}
                            @if ($bmi)
                            <span class="ml-1">
                                @if ($bmi < 18.5) <flux:badge size="sm" color="blue">Kurus</flux:badge>
                                @elseif ($bmi < 25) <flux:badge size="sm" color="green">Normal</flux:badge>
                                @elseif ($bmi < 30) <flux:badge size="sm" color="orange">Gemuk</flux:badge>
                                @else <flux:badge size="sm" color="red">Obese</flux:badge>
                                @endif
                            </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Keadaan Umum</label>
                        <select wire:model="keadaan_umum" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                            <option value="Good">Baik (Good)</option>
                            <option value="Moderate">Sedang (Moderate)</option>
                            <option value="Weak">Lemah (Weak)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Kesadaran</label>
                        <select wire:model="kesadaran_gcs" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-xs text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" {{ !$isEditable ? 'disabled' : '' }}>
                            <option value="Compos Mentis">Compos Mentis (Sadar Penuh)</option>
                            <option value="Apatis">Apatis (Acuh tak acuh)</option>
                            <option value="Somnolen">Somnolen (Mengantuk)</option>
                            <option value="Sopor">Sopor (Setengah Koma)</option>
                            <option value="Coma">Koma (Coma)</option>
                        </select>
                    </div>
                </div>

                {{-- GCS Selector Row --}}
                <div class="grid grid-cols-3 gap-2 mt-4 p-3 bg-zinc-50 dark:bg-zinc-950/40 rounded-lg border border-zinc-150 dark:border-zinc-850">
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 mb-0.5">GCS Eye (E)</label>
                        <select wire:model.live="gcs_eye" class="block w-full rounded border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-2 py-1 text-[11px]" {{ !$isEditable ? 'disabled' : '' }}>
                            <option value="4">4 - Spontan</option>
                            <option value="3">3 - Suara</option>
                            <option value="2">2 - Nyeri</option>
                            <option value="1">1 - Nihil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 mb-0.5">GCS Verbal (V)</label>
                        <select wire:model.live="gcs_verbal" class="block w-full rounded border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-2 py-1 text-[11px]" {{ !$isEditable ? 'disabled' : '' }}>
                            <option value="5">5 - Orientasi</option>
                            <option value="4">4 - Bingung</option>
                            <option value="3">3 - Kacau</option>
                            <option value="2">2 - Mengerang</option>
                            <option value="1">1 - Nihil</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-zinc-500 mb-0.5">GCS Motor (M)</label>
                        <select wire:model.live="gcs_motor" class="block w-full rounded border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-2 py-1 text-[11px]" {{ !$isEditable ? 'disabled' : '' }}>
                            <option value="6">6 - Patuh</option>
                            <option value="5">5 - Lokasi Nyeri</option>
                            <option value="4">4 - Menarik</option>
                            <option value="3">3 - Fleksi</option>
                            <option value="2">2 - Ekstensi</option>
                            <option value="1">1 - Nihil</option>
                        </select>
                    </div>
                    <div class="col-span-3 flex justify-between items-center mt-2 pt-1.5 border-t border-zinc-200/60 dark:border-zinc-800/80">
                        <span class="text-[11px] font-semibold text-zinc-600 dark:text-zinc-400">Total Skor GCS:</span>
                        <flux:badge color="{{ $gcs_score >= 13 ? 'green' : ($gcs_score >= 9 ? 'yellow' : 'red') }}" size="sm" class="font-mono font-bold text-xs">
                            E{{ $gcs_eye }}V{{ $gcs_verbal }}M{{ $gcs_motor }} = {{ $gcs_score }}
                        </flux:badge>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <span class="text-[10px] text-zinc-400 font-medium">Input: {{ $record->perawat?->nama_petugas ?? '-' }}</span>
                @if ($isEditable)
                <flux:button size="sm" variant="filled" color="teal" icon="document-check" wire:click="saveTtv">Simpan TTV</flux:button>
                @endif
            </div>
        </div>

        {{-- Right: SOAPE Card --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.document-text class="w-5 h-5 text-blue-500" />
                    <flux:heading size="lg" class="font-bold">SOAPE & Deskripsi Medis</flux:heading>
                </div>

                <div class="space-y-3">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:textarea wire:model="keluhan_utama" label="Keluhan Utama" placeholder="Keluhan utama..." rows="1" :disabled="!$isEditable" />
                        <flux:textarea wire:model="riwayat_alergi" label="Riwayat Alergi" placeholder="Alergi..." rows="1" :disabled="!$isEditable" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <flux:textarea wire:model="subjective" label="Subjective (S)" placeholder="S..." rows="2" :disabled="!$isEditable" />
                        <flux:textarea wire:model="objective" label="Objective (O)" placeholder="O..." rows="2" :disabled="!$isEditable" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <flux:textarea wire:model="assessment" label="Assessment (A)" placeholder="A..." rows="2" :disabled="!$isEditable" class="md:col-span-1" />
                        <flux:textarea wire:model="plan" label="Plan (P)" placeholder="P..." rows="2" :disabled="!$isEditable" class="md:col-span-1" />
                        <flux:textarea wire:model="evaluation" label="Evaluation (E)" placeholder="E..." rows="2" :disabled="!$isEditable" class="md:col-span-1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center mt-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <span class="text-[10px] text-zinc-400 font-medium">Input: drg. {{ $record->dokter?->nama_petugas ?? '-' }}</span>
                @if ($isEditable)
                <flux:button size="sm" variant="filled" color="blue" icon="document-check" wire:click="saveSoape">Simpan SOAPE</flux:button>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Tabbed Container --}}
    <div x-data="{ activeTab: localStorage.getItem('active_tab_gigi_{{ $record->id }}') || 'odontogram' }" x-init="$watch('activeTab', val => localStorage.setItem('active_tab_gigi_{{ $record->id }}', val))" class="space-y-6">
        <!-- Tabs Menu Bar -->
        <div class="bg-zinc-100 dark:bg-zinc-800/50 p-1.5 rounded-2xl flex flex-wrap gap-1 border border-zinc-200/50 dark:border-zinc-800/40">
            <button type="button" @click="activeTab = 'odontogram'" :class="activeTab === 'odontogram' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.sparkles class="w-4 h-4" />
                Odontogram Chart
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
                Permiksaan Lab
            </button>
            <button type="button" @click="activeTab = 'dokumen'" :class="activeTab === 'dokumen' ? 'bg-emerald-600 text-white shadow-md dark:bg-emerald-500' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200/50 dark:hover:bg-zinc-800/60 hover:text-zinc-900 dark:hover:text-zinc-200'" class="px-4 py-2 text-xs md:text-sm font-semibold rounded-xl transition-all duration-200 flex items-center gap-2">
                <flux:icon.bookmark class="w-4 h-4" />
                Dokumen & Surat
            </button>
        </div>

        <!-- Tab Content: Odontogram Chart -->
        <div x-show="activeTab === 'odontogram'" class="space-y-6" x-transition>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.sparkles class="w-5 h-5 text-teal-500" />
                    <flux:heading size="lg" class="font-bold">Pemeriksaan Odontogram</flux:heading>
                    <flux:badge color="teal" size="sm">Poli Gigi</flux:badge>
                </div>

                {{-- Condition Legend --}}
                <div class="flex flex-wrap gap-2 mb-5">
                    @foreach (['SOU' => ['label' => 'Sound (Sehat)', 'color' => 'green'], 'CAR' => ['label' => 'Caries', 'color' => 'red'], 'MIS' => ['label' => 'Missing', 'color' => 'zinc'], 'FML' => ['label' => 'Filled', 'color' => 'blue'], 'FRA' => ['label' => 'Fracture', 'color' => 'orange'], 'CFR' => ['label' => 'Crown Fraktur', 'color' => 'yellow']] as $code => $info)
                    <flux:badge color="{{ $info['color'] }}" size="sm" class="font-mono font-bold">{{ $code }} — {{ $info['label'] }}</flux:badge>
                    @endforeach
                </div>

                @php
                    $conditionBorderColors = [
                        'SOU' => 'border-green-400 bg-green-50 dark:bg-green-950/30',
                        'CAR' => 'border-red-400 bg-red-50 dark:bg-red-950/30',
                        'MIS' => 'border-zinc-400 bg-zinc-100 dark:bg-zinc-800',
                        'FML' => 'border-blue-400 bg-blue-50 dark:bg-blue-950/30',
                        'FRA' => 'border-orange-400 bg-orange-50 dark:bg-orange-950/30',
                        'CFR' => 'border-yellow-400 bg-yellow-50 dark:bg-yellow-950/30',
                    ];
                    $defaultToothClass = 'border-zinc-200 dark:border-zinc-700 hover:border-teal-400 bg-zinc-50 dark:bg-zinc-950';
                    $adultUpperRight = [18, 17, 16, 15, 14, 13, 12, 11];
                    $adultUpperLeft  = [21, 22, 23, 24, 25, 26, 27, 28];
                    $adultLowerRight = [48, 47, 46, 45, 44, 43, 42, 41];
                    $adultLowerLeft  = [31, 32, 33, 34, 35, 36, 37, 38];
                    $childUpperRight = [55, 54, 53, 52, 51];
                    $childUpperLeft  = [61, 62, 63, 64, 65];
                    $childLowerRight = [85, 84, 83, 82, 81];
                    $childLowerLeft  = [71, 72, 73, 74, 75];
                @endphp

                {{-- Dental Chart Grid --}}
                <div class="space-y-1 select-none overflow-x-auto pb-2">
                    <p class="text-center text-xs font-semibold text-zinc-400 uppercase tracking-widest mb-1">RAHANG ATAS (Maxilla)</p>

                    {{-- Adult Upper Row --}}
                    <div class="flex justify-center gap-0.5">
                        <div class="flex gap-0.5">
                            @foreach ($adultUpperRight as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-end w-9 h-14 rounded-t-lg border-2 transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi {{ $tooth }}{{ $cond ? ' — ' . $cond['condition_code'] : '' }}">
                                @if ($cond)<span class="absolute top-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                                <span class="text-[10px] font-bold font-mono pb-1 text-zinc-600 dark:text-zinc-400">{{ $tooth }}</span>
                            </button>
                            @endforeach
                        </div>
                        <div class="w-px bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                        <div class="flex gap-0.5">
                            @foreach ($adultUpperLeft as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-end w-9 h-14 rounded-t-lg border-2 transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi {{ $tooth }}{{ $cond ? ' — ' . $cond['condition_code'] : '' }}">
                                @if ($cond)<span class="absolute top-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                                <span class="text-[10px] font-bold font-mono pb-1 text-zinc-600 dark:text-zinc-400">{{ $tooth }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Child Upper Row (dashed) --}}
                    <div class="flex justify-center gap-0.5 mt-0.5">
                        <div class="flex gap-0.5">
                            @foreach ($childUpperRight as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-end w-9 h-10 border-2 border-dashed transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi Susu {{ $tooth }}">
                                @if ($cond)<span class="absolute top-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                                <span class="text-[9px] font-mono pb-0.5 text-zinc-500">{{ $tooth }}</span>
                            </button>
                            @endforeach
                        </div>
                        <div class="w-px bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                        <div class="flex gap-0.5">
                            @foreach ($childUpperLeft as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-end w-9 h-10 border-2 border-dashed transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi Susu {{ $tooth }}">
                                @if ($cond)<span class="absolute top-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                                <span class="text-[9px] font-mono pb-0.5 text-zinc-500">{{ $tooth }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Center Divider --}}
                    <div class="relative my-2 mx-4">
                        <div class="border-t-2 border-dashed border-zinc-300 dark:border-zinc-700"></div>
                        <span class="absolute -top-2.5 left-1/2 -translate-x-1/2 bg-white dark:bg-zinc-900 px-2 text-[10px] text-zinc-400 font-semibold tracking-wider">━ GARIS MEDIAN ━</span>
                    </div>

                    {{-- Child Lower Row (dashed) --}}
                    <div class="flex justify-center gap-0.5 mt-1">
                        <div class="flex gap-0.5">
                            @foreach ($childLowerRight as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-start w-9 h-10 border-2 border-dashed transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi Susu {{ $tooth }}">
                                <span class="text-[9px] font-mono pt-0.5 text-zinc-500">{{ $tooth }}</span>
                                @if ($cond)<span class="absolute bottom-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                        <div class="w-px bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                        <div class="flex gap-0.5">
                            @foreach ($childLowerLeft as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-start w-9 h-10 border-2 border-dashed transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi Susu {{ $tooth }}">
                                <span class="text-[9px] font-mono pt-0.5 text-zinc-500">{{ $tooth }}</span>
                                @if ($cond)<span class="absolute bottom-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Adult Lower Row --}}
                    <div class="flex justify-center gap-0.5 mt-0.5">
                        <div class="flex gap-0.5">
                            @foreach ($adultLowerRight as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-start w-9 h-14 rounded-b-lg border-2 transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi {{ $tooth }}{{ $cond ? ' — ' . $cond['condition_code'] : '' }}">
                                <span class="text-[10px] font-bold font-mono pt-1 text-zinc-600 dark:text-zinc-400">{{ $tooth }}</span>
                                @if ($cond)<span class="absolute bottom-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                        <div class="w-px bg-zinc-300 dark:bg-zinc-700 mx-1"></div>
                        <div class="flex gap-0.5">
                            @foreach ($adultLowerLeft as $tooth)
                            @php $cond = $teethMap[$tooth] ?? null; $toothClass = $cond ? ($conditionBorderColors[$cond['condition_code']] ?? $defaultToothClass) : $defaultToothClass; @endphp
                            <button type="button" wire:click="openTooth({{ $tooth }})"
                                class="relative flex flex-col items-center justify-start w-9 h-14 rounded-b-lg border-2 transition-all {{ $toothClass }} {{ !$isEditable ? 'cursor-default' : '' }}"
                                title="Gigi {{ $tooth }}{{ $cond ? ' — ' . $cond['condition_code'] : '' }}">
                                <span class="text-[10px] font-bold font-mono pt-1 text-zinc-600 dark:text-zinc-400">{{ $tooth }}</span>
                                @if ($cond)<span class="absolute bottom-0.5 text-[9px] font-bold font-mono text-zinc-700 dark:text-zinc-200">{{ $cond['condition_code'] }}</span>@endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                    <p class="text-center text-xs font-semibold text-zinc-400 uppercase tracking-widest mt-1">RAHANG BAWAH (Mandibula)</p>
                </div>

                {{-- Tooth Summary Table --}}
                @if (count($teethMap) > 0)
                <div class="mt-6 border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>No. Gigi</flux:table.column>
                            <flux:table.column>Kondisi</flux:table.column>
                            <flux:table.column>Catatan</flux:table.column>
                            @if ($isEditable)<flux:table.column>Aksi</flux:table.column>@endif
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($teethMap as $toothNum => $data)
                            <flux:table.row wire:key="tooth-row-{{ $toothNum }}">
                                <flux:table.cell class="font-mono font-bold text-teal-700 dark:text-teal-400">{{ $toothNum }}</flux:table.cell>
                                <flux:table.cell>
                                    @php $badgeColor = ['SOU' => 'green', 'CAR' => 'red', 'MIS' => 'zinc', 'FML' => 'blue', 'FRA' => 'orange', 'CFR' => 'yellow'][$data['condition_code']] ?? 'zinc'; @endphp
                                    <flux:badge color="{{ $badgeColor }}" size="sm" class="font-mono font-bold">{{ $data['condition_code'] }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-300">{{ $data['notes'] ?: '-' }}</flux:table.cell>
                                @if ($isEditable)
                                <flux:table.cell>
                                    <div class="flex gap-1">
                                        <flux:button variant="ghost" size="sm" icon="pencil" wire:click="openTooth({{ $toothNum }})" />
                                        <flux:button variant="ghost" size="sm" icon="trash" class="text-red-500" wire:click="clearTooth({{ $toothNum }})" />
                                    </div>
                                </flux:table.cell>
                                @endif
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
                @else
                <div class="mt-4 text-center py-6 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                    Klik pada gigi di diagram untuk mencatat kondisi klinis.
                </div>
                @endif

                {{-- Tooth Condition Modal --}}
                @if ($showToothModal && $activeTooth)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
                    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-sm overflow-hidden">
                        <div class="p-5 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                            <div>
                                <flux:heading size="lg">Kondisi Gigi #{{ $activeTooth }}</flux:heading>
                                <p class="text-sm text-zinc-500 mt-0.5">Pilih kondisi dan tambahkan catatan klinis</p>
                            </div>
                            <button type="button" wire:click="closeToothModal" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                                <flux:icon.x-mark class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['SOU' => ['label' => 'Sound', 'bg' => 'bg-green-500 hover:bg-green-600'], 'CAR' => ['label' => 'Caries', 'bg' => 'bg-red-500 hover:bg-red-600'], 'MIS' => ['label' => 'Missing', 'bg' => 'bg-zinc-500 hover:bg-zinc-600'], 'FML' => ['label' => 'Filled', 'bg' => 'bg-blue-500 hover:bg-blue-600'], 'FRA' => ['label' => 'Fracture', 'bg' => 'bg-orange-500 hover:bg-orange-600'], 'CFR' => ['label' => 'Crown Fr.', 'bg' => 'bg-yellow-500 hover:bg-yellow-600']] as $code => $info)
                                <button type="button"
                                    wire:click="$set('activeToothCondition', '{{ $code }}')"
                                    class="rounded-lg p-2.5 text-xs font-bold text-white transition-all {{ $info['bg'] }} {{ $activeToothCondition === $code ? 'ring-2 ring-offset-2 ring-white shadow-lg scale-105' : 'opacity-70 hover:opacity-100' }}">
                                    {{ $code }}<br><span class="font-normal text-[10px] opacity-90">{{ $info['label'] }}</span>
                                </button>
                                @endforeach
                            </div>
                            <flux:textarea wire:model="activeToothNotes" label="Catatan Klinis (Opsional)" placeholder="Contoh: Karies profunda mesial, abses periapikal..." rows="2" />
                        </div>
                        <div class="flex justify-end gap-2 px-5 pb-5">
                            <flux:button variant="filled" wire:click="closeToothModal">Batal</flux:button>
                            <flux:button variant="primary" wire:click="saveToothCondition">Simpan Kondisi</flux:button>
                        </div>
                    </div>
                </div>
                @endif

                <div class="flex justify-end mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    @if ($isEditable)
                        <flux:button size="sm" variant="filled" color="teal" icon="document-check" wire:click="saveOdontogram">Simpan Odontogram</flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Content: Diagnosis -->
        <div x-show="activeTab === 'diagnosis'" class="space-y-6" x-transition>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.hashtag class="w-5 h-5 text-indigo-500" />
                    <flux:heading size="lg" class="font-bold">Kode Diagnosis (ICD-10) & Prosedur (ICD-9)</flux:heading>
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
                        🩺 drg. {{ $record->dokter->nama_petugas ?? '-' }} (SIP: {{ $record->dokter->nomor_sip ?? '-' }})
                    </div>
                    @if ($isEditable)
                        <flux:button size="sm" variant="filled" color="indigo" icon="document-check" wire:click="saveIcd">Simpan Diagnosis</flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Content: Resep -->
        <div x-show="activeTab === 'resep'" class="space-y-6" x-transition>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" /></svg>
                    <flux:heading size="lg" class="font-bold">Rencana Resep Elektronik (E-Resep)</flux:heading>
                </div>

                @if ($isEditable)
                <div class="p-5 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200/60 dark:border-zinc-800/80 rounded-xl mb-6 space-y-4">
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">
                            <input type="radio" wire:model.live="presc_type" value="non-racikan" class="text-indigo-600 focus:ring-indigo-500 border-zinc-300">
                            <span>Non-Racikan (Standar)</span>
                        </label>
                        <label class="flex items-center gap-2 text-sm text-zinc-700 dark:text-zinc-300 cursor-pointer">
                            <input type="radio" wire:model.live="presc_type" value="racikan" class="text-indigo-600 focus:ring-indigo-500 border-zinc-300">
                            <span>Racikan (Compound)</span>
                        </label>
                    </div>

                    @if ($presc_type === 'racikan')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 border-b border-zinc-200/60 dark:border-zinc-800/80 pb-4">
                        <flux:input wire:model="presc_nama_racikan" label="Nama Kelompok Racikan" placeholder="Contoh: Puyer Sakit Gigi" />
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Metode Compounding</label>
                            <select wire:model="presc_metode_racik_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">Pilih Metode</option>
                                @foreach ($metodeRaciks as $m)
                                <option value="{{ $m->id }}" wire:key="metode-opt-gigi-{{ $m->id }}">{{ $m->nama_metode_racik }}</option>
                                @endforeach
                            </select>
                        </div>
                        <flux:input wire:model="presc_jumlah_kemasan" label="Jumlah Kemasan" type="number" placeholder="10" />
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                        <div class="md:col-span-6 relative">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Cari & Pilih Obat</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-zinc-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" /></svg>
                                </div>
                                <input type="text" wire:model.live.debounce.250ms="drugQuery" placeholder="Ketik nama obat..." class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 pl-10 pr-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            </div>
                            @if (count($drugResults) > 0)
                            <div wire:key="drug-dropdown-gigi" class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                                @foreach ($drugResults as $res)
                                <button type="button" wire:click="selectDrug({{ $res['id'] }})" wire:key="drug-item-gigi-{{ $res['id'] }}-{{ $loop->index }}" class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-800 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between items-center transition-colors">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $res['nama_obat'] }}</span>
                                    <span class="inline-flex items-center rounded-md bg-zinc-100 dark:bg-zinc-800 px-2 py-1 text-xs font-medium text-zinc-700 dark:text-zinc-400 ring-1 ring-inset ring-zinc-500/10">{{ $res['satuan'] }} (Stok: {{ $res['stok_saat_ini'] }})</span>
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

                    @if ($presc_type === 'racikan' && count($presc_ingredients) > 0)
                    <div class="bg-zinc-100/50 dark:bg-zinc-950 p-4 rounded-lg border border-zinc-200/50 dark:border-zinc-800">
                        <flux:heading size="sm" class="mb-3 font-semibold text-zinc-800 dark:text-zinc-300">Bahan Racikan Terpilih:</flux:heading>
                        <div class="space-y-2">
                            @foreach ($presc_ingredients as $i => $ing)
                            <div wire:key="ing-row-gigi-{{ $i }}" class="flex justify-between items-center text-xs p-2 bg-white dark:bg-zinc-900 rounded border border-zinc-150 dark:border-zinc-850">
                                <span><strong>{{ $ing['nama_obat'] }}</strong> ({{ $ing['jumlah'] }} {{ $ing['satuan'] }})</span>
                                <flux:button variant="ghost" size="xs" icon="x-mark" class="text-red-500" wire:click="removeIngredient({{ $i }})" />
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Aturan Pakai (Signa)</label>
                            <select wire:model="presc_aturan_pakai" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                                <option value="">Pilih Aturan Pakai</option>
                                @foreach ($aturanPakais as $ap)
                                <option value="{{ $ap->nama_aturan_pakai }}" wire:key="aturan-opt-gigi-{{ $ap->id }}">{{ $ap->nama_aturan_pakai }}</option>
                                @endforeach
                            </select>
                        </div>
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

                <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Tipe Resep</flux:table.column>
                            <flux:table.column>Nama Obat / Racikan</flux:table.column>
                            <flux:table.column>Detail Kandungan</flux:table.column>
                            <flux:table.column>Aturan Pakai</flux:table.column>
                            <flux:table.column>Catatan</flux:table.column>
                            <flux:table.column>Aksi</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @forelse ($prescriptionsList as $index => $presc)
                            <flux:table.row :key="'presc-row-gigi-'.$index">
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
                                        @foreach ($presc['items'] as $itemIndex => $item)
                                        <li wire:key="presc-item-gigi-{{ $index }}-{{ $itemIndex }}">{{ $item['nama_obat'] }} <span class="text-zinc-500 font-mono text-[10px]">({{ $item['jumlah'] }} {{ $item['satuan'] }})</span></li>
                                        @endforeach
                                    </ul>
                                    @else
                                    {{ $presc['items'][0]['jumlah'] }} {{ $presc['items'][0]['satuan'] }}
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="font-medium font-mono text-xs text-zinc-800 dark:text-zinc-200">{{ $presc['aturan_pakai'] }}</flux:table.cell>
                                <flux:table.cell>{{ $presc['catatan'] ?: '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    <div class="flex items-center gap-2">
                                        @if (isset($presc['id']))
                                        <flux:button variant="ghost" icon="printer" size="sm" class="text-zinc-600 dark:text-zinc-400" wire:click="printPrescription({{ $presc['id'] }})" title="Cetak Resep" />
                                        @endif
                                        @if ($isEditable)
                                        <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500" wire:click="removePrescription({{ $index }})" title="Hapus Resep" />
                                        @endif
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                            @empty
                            <flux:table.row>
                                <flux:table.cell colspan="6" class="text-center text-zinc-500 py-8">Belum ada obat yang diresepkan.</flux:table.cell>
                            </flux:table.row>
                            @endforelse
                        </flux:table.rows>
                    </flux:table>
                </div>
                <div class="flex justify-end mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    @if ($isEditable)
                        <flux:button size="sm" variant="filled" color="emerald" icon="document-check" wire:click="savePrescription">Simpan Resep</flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Content: Lab -->
        <div x-show="activeTab === 'lab'" class="space-y-6" x-transition>
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <flux:icon.beaker class="w-5 h-5 text-purple-500" />
                    <flux:heading size="lg" class="font-bold">Permintaan Pemeriksaan Laboratorium</flux:heading>
                    @if ($existingLabOrderId)
                    <flux:badge color="amber" size="sm">Order #{{ $existingLabOrderId }}</flux:badge>
                    @endif
                </div>

                @if ($isEditable)
                {{-- Lab Selector Dropdown --}}
                <div class="mb-5">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Pilih Tes Laboratorium</label>
                    <select wire:model.live="selectedLabTestId" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">-- Cari dan Pilih Tes Laboratorium --</option>
                        @foreach ($allLabTests as $item)
                        <option value="{{ $item->id }}" wire:key="lab-item-option-gigi-{{ $item->id }}">
                            [{{ $item->category }}] {{ $item->test_name }} - Rp {{ number_format($item->tariff, 0, ',', '.') }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Clinical Notes --}}
                <div class="mb-5">
                    <flux:textarea wire:model="labClinicalNotes" label="Catatan Klinis untuk Analis (Opsional)" placeholder="Misal: Puasa 12 jam, curiga DHF, periksa ASAP..." rows="2" />
                </div>
                @endif

                {{-- Selected Tests Table --}}
                @if (count($selectedLabTests) > 0)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Nama Pemeriksaan</flux:table.column>
                            <flux:table.column>Kategori</flux:table.column>
                            <flux:table.column>Nilai Rujukan</flux:table.column>
                            <flux:table.column>Satuan</flux:table.column>
                            <flux:table.column>Tarif</flux:table.column>
                            @if ($isEditable)<flux:table.column></flux:table.column>@endif
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($selectedLabTests as $idx => $test)
                            <flux:table.row wire:key="lab-test-gigi-{{ $idx }}">
                                <flux:table.cell class="font-semibold text-zinc-900 dark:text-white">{{ $test['test_name'] }}</flux:table.cell>
                                <flux:table.cell><flux:badge color="purple" size="sm">{{ $test['category'] }}</flux:badge></flux:table.cell>
                                <flux:table.cell class="font-mono text-xs text-zinc-600 dark:text-zinc-300">{{ $test['default_normal_range'] ?? '-' }}</flux:table.cell>
                                <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $test['default_unit'] ?? '-' }}</flux:table.cell>
                                <flux:table.cell class="font-bold font-mono text-purple-700 dark:text-purple-400">Rp {{ number_format($test['tariff'], 0, ',', '.') }}</flux:table.cell>
                                @if ($isEditable)
                                <flux:table.cell>
                                    <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500" wire:click="removeLabTest({{ $idx }})" />
                                </flux:table.cell>
                                @endif
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>

                {{-- Total Tariff Summary --}}
                <div class="mt-4 flex justify-end">
                    <div class="bg-purple-50 dark:bg-purple-950/30 border border-purple-200 dark:border-purple-800/50 rounded-lg px-5 py-3 flex items-center gap-4">
                        <span class="text-sm font-semibold text-purple-700 dark:text-purple-300">Total Tarif Lab:</span>
                        <span class="text-xl font-extrabold font-mono text-purple-800 dark:text-purple-200">Rp {{ number_format($labTotalTariff, 0, ',', '.') }}</span>
                        <flux:badge color="purple" size="sm">{{ count($selectedLabTests) }} tes</flux:badge>
                    </div>
                </div>

                @if ($existingLabOrderId)
                <div class="mt-3 flex items-center gap-2">
                    <flux:icon.check-circle class="w-4 h-4 text-amber-500" />
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Order laboratorium akan dikirim ke antrian lab saat data disimpan.</p>
                </div>
                @endif
                @else
                <div class="text-center py-6 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                    @if ($isEditable)
                    Cari dan pilih tes laboratorium di atas untuk menambahkan permintaan lab.
                    @else
                    Tidak ada permintaan lab pada kunjungan ini.
                    @endif
                </div>
                @endif
                <div class="flex justify-end mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    @if ($isEditable)
                        <flux:button size="sm" variant="filled" color="purple" icon="document-check" wire:click="saveLabOrder">Simpan Permintaan Lab</flux:button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab Content: Dokumen -->
        <div x-show="activeTab === 'dokumen'" class="space-y-6" x-transition>
            @include('medical_records.partials.pcare-referral-form')

            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <flux:icon.document-text class="w-5 h-5 text-teal-500" />
                        <flux:heading size="lg" class="font-bold">Dokumen Medis & Persetujuan</flux:heading>
                    </div>
                    @if ($isEditable)
                    <flux:dropdown>
                        <flux:button variant="primary" icon="document-text" icon-trailing="chevron-down">Buat Surat / Persetujuan</flux:button>
                        <flux:menu class="min-w-64">
                            <flux:menu.item icon="document" wire:click="openSickLeave">Surat Keterangan Sakit (Sick Leave)</flux:menu.item>
                            <flux:menu.item icon="document-check" wire:click="openHealthCert">Surat Keterangan Sehat (Health Cert.)</flux:menu.item>
                            <flux:menu.item icon="document-arrow-up" wire:click="openReferral">Surat Rujukan Eksternal (Referral)</flux:menu.item>
                            <flux:menu.item icon="share" wire:click="togglePcareReferral">Rujukan Keluar BPJS (PCare)</flux:menu.item>
                            <flux:menu.item icon="document-duplicate" wire:click="openBebasNarkoba">Surat Ket. Bebas Narkoba</flux:menu.item>
                            <flux:menu.item icon="pencil-square" wire:click="openInformedConsent">Informed Consent (Tindakan)</flux:menu.item>
                            <flux:menu.item icon="clipboard-document-check" wire:click="openGeneralConsent">General Consent (Persetujuan Umum)</flux:menu.item>
                        </flux:menu>
                    </flux:dropdown>
                    @endif
                </div>

                @php
                    $generatedLetters = \App\Models\MedicalLetter::where('medical_record_id', $record->id)->get();
                    $generatedReferrals = \App\Models\SuratRujukan::where('pendaftaran_id', $record->pendaftaran_id)->get();
                    $generatedCerts = \App\Models\SuratKeterangan::where('pendaftaran_id', $record->pendaftaran_id)->where('jenis_surat', 'bebas_narkoba')->get();
                    $generatedConsents = \App\Models\SuratPersetujuan::where('pendaftaran_id', $record->pendaftaran_id)->get();

                    // Merge into a single collection
                    $allDocuments = collect();

                    foreach ($generatedLetters as $l) {
                        $allDocuments->push([
                            'id' => $l->id,
                            'type' => $l->jenis_surat,
                            'no_surat' => $l->nomor_surat,
                            'label' => $l->jenis_surat === 'surat_sakit' ? 'Surat Sakit' : 'Surat Sehat',
                            'badge_color' => $l->jenis_surat === 'surat_sakit' ? 'orange' : 'green',
                            'details' => $l->jenis_surat === 'surat_sakit' 
                                ? 'Mulai: ' . \Carbon\Carbon::parse($l->meta_data['dari_tanggal'] ?? '')->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($l->meta_data['sampai_tanggal'] ?? '')->format('d-m-Y') . ' (' . ($l->meta_data['jumlah_hari'] ?? 0) . ' hari) - Alasan: ' . ($l->meta_data['alasan'] ?? '-')
                                : 'TB: ' . ($l->meta_data['tinggi_badan'] ?? '-') . ' cm | BB: ' . ($l->meta_data['berat_badan'] ?? '-') . ' kg | Tensi: ' . ($l->meta_data['tekanan_darah'] ?? '-') . ' | Gol: ' . ($l->meta_data['golongan_darah'] ?? '-') . ' | Buta Warna: ' . ($l->meta_data['buta_warna'] ?? '-') . ' | Keterangan: ' . ($l->meta_data['kesimpulan'] ?? '-'),
                            'print_url' => route('medical-letters.print', $l->id),
                        ]);
                    }

                    foreach ($generatedReferrals as $r) {
                        $allDocuments->push([
                            'id' => $r->id,
                            'type' => 'surat_rujukan',
                            'no_surat' => $r->no_surat,
                            'label' => 'Surat Rujukan',
                            'badge_color' => 'blue',
                            'details' => 'Tujuan: ' . $r->faskes_tujuan . ' - Diagnosa: ' . $r->diagnosa . ($r->catatan ? ' - Catatan: ' . $r->catatan : ''),
                            'print_url' => route('print.referral', $r->id),
                        ]);
                    }

                    foreach ($generatedCerts as $c) {
                        $allDocuments->push([
                            'id' => $c->id,
                            'type' => 'bebas_narkoba',
                            'no_surat' => $c->no_surat,
                            'label' => 'Surat Bebas Narkoba',
                            'badge_color' => 'red',
                            'details' => 'Keperluan: ' . ($c->konten_surat['keperluan'] ?? '-') . ' - Hasil: ' . ($c->konten_surat['hasil_tes'] ?? '-'),
                            'print_url' => route('print.certificate', $c->id),
                        ]);
                    }

                    foreach ($generatedConsents as $g) {
                        $isGeneral = $g->jenis_persetujuan === 'general_consent';
                        $allDocuments->push([
                            'id' => $g->id,
                            'type' => $g->jenis_persetujuan,
                            'no_surat' => $g->no_surat,
                            'label' => $isGeneral ? 'General Consent' : 'Informed Consent',
                            'badge_color' => $isGeneral ? 'indigo' : 'purple',
                            'details' => 'Penanggung Jawab: ' . $g->nama_penanggung_jawab . ' (' . ucfirst(str_replace('_', ' ', $g->hubungan_penanggung_jawab)) . ')' . ($isGeneral ? '' : ' - Tindakan: ' . $g->nama_tindakan_medis) . ' - Pernyataan: ' . strtoupper($g->pernyataan),
                            'print_url' => route('print.consent', $g->id),
                        ]);
                    }

                    $pcareReferrals = \App\Models\PatientReferral::where('medical_record_id', $record->id)->get();
                    foreach ($pcareReferrals as $pr) {
                        $allDocuments->push([
                            'id' => $pr->id,
                            'type' => 'pcare_referral',
                            'no_surat' => $pr->no_rujukan,
                            'label' => 'Rujukan BPJS (PCare)',
                            'badge_color' => 'indigo',
                            'details' => 'Tujuan: ' . $pr->ppk_dirujuk_nama . ' - Spesialis: ' . $pr->spesialis_nama . ($pr->is_tacc ? ' (TACC: ' . $pr->tacc_jenis . ')' : '') . ' - Diagnosa Utama: ' . $pr->diagnosa_utama_kode,
                            'print_url' => '#',
                        ]);
                    }
                @endphp

                @if ($allDocuments->count() > 0)
                <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                    <flux:table>
                        <flux:table.columns>
                            <flux:table.column>Nomor Surat / Dokumen</flux:table.column>
                            <flux:table.column>Jenis Dokumen</flux:table.column>
                            <flux:table.column>Detail / Keterangan</flux:table.column>
                            <flux:table.column>Aksi</flux:table.column>
                        </flux:table.columns>
                        <flux:table.rows>
                            @foreach ($allDocuments as $doc)
                            <flux:table.row wire:key="doc-row-gigi-{{ $doc['type'] }}-{{ $doc['id'] }}">
                                <flux:table.cell class="font-mono font-bold text-zinc-900 dark:text-white">{{ $doc['no_surat'] }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="{{ $doc['badge_color'] }}" size="sm">{{ $doc['label'] }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-300">{{ $doc['details'] }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:button variant="ghost" icon="printer" size="sm" wire:click="printUnified('{{ $doc['print_url'] }}')" title="Cetak Dokumen" />
                                </flux:table.cell>
                            </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
                @else
                <div class="text-center py-6 text-xs text-zinc-400 italic border border-dashed border-zinc-200 dark:border-zinc-800 rounded-lg">
                    Belum ada surat keterangan atau persetujuan yang dibuat untuk kunjungan ini.
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Save & Lock Action Buttons --}}
    <div class="flex justify-end bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
        <div class="flex gap-2">
            @if ($isEditable)
            <flux:button variant="filled" wire:click="saveDraft">Save as Draft</flux:button>
            <flux:button variant="primary" wire:click="finalizeAndLock">Selesai & Kunci Rekam Medis</flux:button>
            @else
            <flux:badge color="zinc" size="md">Terkunci: Tidak dapat diedit</flux:badge>
            @endif
        </div>
    </div>

    {{-- Dentist Comparison Table Footer (Last 3 visits) --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm mt-6">
        <div class="flex items-center gap-2 mb-4 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.clock class="w-5 h-5 text-indigo-500" />
            <flux:heading size="lg" class="font-bold">Dentist Comparison (3 Kunjungan Terakhir)</flux:heading>
        </div>

        @if (count($recentHistory) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800 text-sm">
                <thead>
                    <tr class="bg-zinc-50 dark:bg-zinc-950">
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Tanggal</th>
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Poli / Dokter</th>
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Diagnosa (ICD-10)</th>
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Subjective (Keluhan)</th>
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Objective (Fisik)</th>
                        <th class="px-4 py-3 text-left font-bold text-zinc-700 dark:text-zinc-300">Assessment & Plan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-250 dark:divide-zinc-850">
                    @foreach ($recentHistory as $history)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-950/20">
                        <td class="px-4 py-3 font-mono text-xs whitespace-nowrap text-zinc-600 dark:text-zinc-400">{{ $history->created_at->format('d-m-Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-zinc-900 dark:text-white block">{{ $history->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'Poli Umum' }}</span>
                            <span class="text-xs text-zinc-500">{{ $history->dokter->nama_petugas ?? '-' }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @forelse ($history->icd10s as $diag)
                                <flux:badge size="sm" color="zinc" class="text-[10px] font-mono" title="{{ $diag->icd10_name }}">{{ $diag->icd10_code }}</flux:badge>
                                @empty
                                <span class="text-zinc-400 italic text-xs">-</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400 max-w-xs truncate" title="{{ $history->subjective }}">{{ $history->subjective ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400 max-w-xs truncate" title="{{ $history->objective }}">{{ $history->objective ?? '-' }}</td>
                        <td class="px-4 py-3 text-xs text-zinc-600 dark:text-zinc-400">
                            <strong>A:</strong> {{ Str::limit($history->assessment ?? '-', 40) }}<br>
                            <strong>P:</strong> {{ Str::limit($history->plan ?? '-', 40) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-6 text-xs text-zinc-400 italic">Tidak ada riwayat medis sebelumnya untuk perbandingan.</div>
        @endif
    </div>

    {{-- Letter Modals --}}
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

                <flux:input wire:model="sick_diagnose" label="Diagnosis Ringkasan (Opsional)" placeholder="Contoh: Pulpitis Akut, Gangren Pulpa" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter TTD</label>
                    <select wire:model="sick_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="sick-doc-opt-gigi-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
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
                        <option value="{{ $doc->id }}" wire:key="health-doc-opt-gigi-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
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
                <flux:input wire:model="ref_diagnosa" label="Diagnosis Utama Rujukan" required placeholder="Contoh: Susp. Pulpitis irreversible gigi 36" />
                <flux:textarea wire:model="ref_catatan" label="Catatan Penanganan Awal / Terapi Tindakan" placeholder="Deskripsikan terapi awal atau alasan dirujuk..." rows="3" />

                <div>
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Dokter Merujuk</label>
                    <select wire:model="ref_dokter_id" class="block w-full rounded-lg border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 px-3 py-2 text-sm text-zinc-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" required>
                        <option value="">Pilih Dokter</option>
                        @foreach ($doctors as $doc)
                        <option value="{{ $doc->id }}" wire:key="ref-doc-opt-gigi-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
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
                        <option value="{{ $doc->id }}" wire:key="narkoba-doc-opt-gigi-{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</option>
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
                <flux:input wire:model="consent_nama_tindakan_medis" label="Nama Tindakan Medis" required placeholder="Contoh: Ekstraksi Gigi, Odontektomi" />
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
                            <option value="{{ $st->id }}" wire:key="consent-staff-opt-gigi-{{ $st->id }}">{{ $st->nama_petugas }} ({{ $st->jenis_petugas }})</option>
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
