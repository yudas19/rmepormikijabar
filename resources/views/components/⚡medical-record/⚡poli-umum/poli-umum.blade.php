<div class="space-y-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    @php
                        $workspaceLabel = match($poliklinik) {
                            'gigi' => 'KLINIK GIGI',
                            'kia'  => 'KLINIK KIA / KEBIDANAN',
                            default => 'POLIKLINIK UMUM',
                        };
                        $workspaceBadgeColor = match($poliklinik) {
                            'gigi' => 'teal',
                            'kia'  => 'pink',
                            default => 'blue',
                        };
                    @endphp
                    <flux:heading size="xl" class="font-extrabold tracking-tight">{{ $workspaceLabel }}</flux:heading>
                    <flux:badge color="{{ $workspaceBadgeColor }}" size="md">Workspace</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">No. Antrean: <span class="font-bold text-zinc-900 dark:text-white">{{ $record->nomor_antrean }}</span> | Encounter ID: <span class="font-mono text-xs">{{ $record->encounter_id }}</span></flux:subheading>
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
                @endif
            </div>
        </div>
    </div>

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

            <flux:select wire:model="keadaan_umum" label="Keadaan Umum" :disabled="!$isEditable">
                <flux:select.option value="Good">Baik (Good)</flux:select.option>
                <flux:select.option value="Moderate">Sedang (Moderate)</flux:select.option>
                <flux:select.option value="Weak">Lemah (Weak)</flux:select.option>
            </flux:select>
        </div>

        <div class="bg-zinc-50 dark:bg-zinc-950/40 p-5 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 mt-6 space-y-4">
            <flux:heading size="md" class="font-bold">Skala Koma Glasgow (GCS) & Kesadaran</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <flux:select wire:model="kesadaran_gcs" label="Tingkat Kesadaran" :disabled="!$isEditable">
                    <flux:select.option value="Compos Mentis">Compos Mentis (Sadar Penuh)</flux:select.option>
                    <flux:select.option value="Apatis">Apatis (Acuh tak acuh)</flux:select.option>
                    <flux:select.option value="Somnolen">Somnolen (Mengantuk)</flux:select.option>
                    <flux:select.option value="Sopor">Sopor (Setengah Koma)</flux:select.option>
                    <flux:select.option value="Coma">Koma (Coma)</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="gcs_eye" label="Eye (E) - Refleks Mata" :disabled="!$isEditable">
                    <flux:select.option value="4">4 - Spontan membuka mata</flux:select.option>
                    <flux:select.option value="3">3 - Membuka mata terhadap suara</flux:select.option>
                    <flux:select.option value="2">2 - Membuka mata terhadap nyeri</flux:select.option>
                    <flux:select.option value="1">1 - Tidak merespons</flux:select.option>
                </flux:select>

                <flux:select wire:model.live="gcs_verbal" label="Verbal (V) - Respons Suara" :disabled="!$isEditable">
                    <flux:select.option value="5">5 - Orientasi baik & lancar</flux:select.option>
                    <flux:select.option value="4">4 - Bingung, bicara kacau</flux:select.option>
                    <flux:select.option value="3">3 - Kata-kata tidak teratur</flux:select.option>
                    <flux:select.option value="2">2 - Suara menggumam/mengerang</flux:select.option>
                    <flux:select.option value="1">1 - Tidak ada suara</flux:select.option>
                </flux:select>

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
    </div>

    {{-- ─── ODONTOGRAM SECTION (Poli Gigi only) ──────────────────────────────────────── --}}
    @if ($poliklinik === 'gigi')
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.sparkles class="w-5 h-5 text-teal-500" />
            <flux:heading size="lg" class="font-bold">2b. Pemeriksaan Odontogram</flux:heading>
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
    </div>
    @endif

    {{-- ─── ANC SECTION (KIA only) ─────────────────────────────────────────────────── --}}
    @if ($poliklinik === 'kia')
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.heart class="w-5 h-5 text-pink-500" />
            <flux:heading size="lg" class="font-bold">2b. Pemeriksaan ANC (Antenatal Care)</flux:heading>
            <flux:badge color="pink" size="sm">KIA / Kebidanan</flux:badge>
        </div>

        {{-- HPHT + TP with Naegele auto-calc --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-pink-50 dark:bg-pink-950/20 rounded-lg border border-pink-200 dark:border-pink-800/40">
            <flux:input wire:model.live="anc_hpht" type="date" label="HPHT (Hari Pertama Haid Terakhir)" :disabled="!$isEditable" />
            <flux:input wire:model="anc_tp" type="date" label="Taksiran Persalinan (Naegele)" :disabled="!$isEditable" description="Otomatis saat HPHT diisi" />
            <div>
                <flux:label>Usia Kehamilan</flux:label>
                <div class="flex items-center h-10 px-3 border border-zinc-200 dark:border-zinc-800 rounded-lg bg-zinc-50 dark:bg-zinc-950 font-semibold font-mono">
                    @if ($anc_uk_minggu !== null)
                        <span class="text-pink-600 dark:text-pink-400">{{ $anc_uk_minggu }} minggu</span>
                    @else
                        <span class="text-zinc-400 text-sm">—</span>
                    @endif
                </div>
            </div>
            <flux:select wire:model="anc_presentasi" label="Presentasi Janin" :disabled="!$isEditable">
                <flux:select.option value="">Pilih...</flux:select.option>
                <flux:select.option value="Kepala">Kepala (Cephalic)</flux:select.option>
                <flux:select.option value="Bokong">Bokong (Breech)</flux:select.option>
                <flux:select.option value="Lintang">Lintang (Transverse)</flux:select.option>
                <flux:select.option value="Oblique">Oblique</flux:select.option>
            </flux:select>
        </div>

        {{-- Vital Measurements --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <flux:input wire:model="anc_tfu" type="number" step="0.1" label="TFU (Tinggi Fundus Uteri)" suffix="cm" placeholder="28.0" :disabled="!$isEditable" />
            <flux:input wire:model="anc_lila" type="number" step="0.1" label="LILA (Lingkar Lengan Atas)" suffix="cm" placeholder="23.5" :disabled="!$isEditable" />
            <flux:input wire:model="anc_djj" type="number" label="DJJ (Denyut Jantung Janin)" suffix="bpm" placeholder="145" :disabled="!$isEditable" />
        </div>

        {{-- Leopold Palpation --}}
        <div class="bg-zinc-50 dark:bg-zinc-950/40 p-5 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 space-y-4 mb-6">
            <flux:heading size="md" class="font-bold">Pemeriksaan Leopold (Palpasi Abdomen)</flux:heading>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:textarea wire:model="anc_leopold_1" label="Leopold I — Fundus" placeholder="Deskripsi bagian teratas uterus (kepala/bokong)..." rows="2" :disabled="!$isEditable" />
                <flux:textarea wire:model="anc_leopold_2" label="Leopold II — Samping" placeholder="Posisi punggung dan bagian kecil janin..." rows="2" :disabled="!$isEditable" />
                <flux:textarea wire:model="anc_leopold_3" label="Leopold III — Terbawah" placeholder="Bagian terbawah janin (presentasi)..." rows="2" :disabled="!$isEditable" />
                <flux:textarea wire:model="anc_leopold_4" label="Leopold IV — Masuk PAP" placeholder="Penurunan bagian terbawah / konvergen-divergen..." rows="2" :disabled="!$isEditable" />
            </div>
        </div>

        {{-- Additional ANC Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <flux:select wire:model="anc_golongan_darah" label="Golongan Darah" :disabled="!$isEditable">
                <flux:select.option value="">Tidak Diketahui</flux:select.option>
                <flux:select.option value="A">A</flux:select.option>
                <flux:select.option value="B">B</flux:select.option>
                <flux:select.option value="AB">AB</flux:select.option>
                <flux:select.option value="O">O</flux:select.option>
            </flux:select>
            <div class="flex flex-col gap-2">
                <flux:label>Riwayat Sectio Caesarea</flux:label>
                <div class="flex items-center h-10">
                    <flux:checkbox wire:model="anc_riwayat_sc" label="Pernah SC sebelumnya" :disabled="!$isEditable" />
                </div>
            </div>
            <flux:textarea wire:model="anc_catatan_bidan" label="Catatan Bidan / Advice" placeholder="Saran atau rencana follow-up bidan..." rows="2" :disabled="!$isEditable" />
        </div>
    </div>
    @endif

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
                    <flux:input wire:model.live.debounce.250ms="icd10Query" placeholder="Cari Kode atau Nama Diagnosa ICD-10..." icon="magnifying-glass" />

                    @if (count($icd10Results) > 0)
                    <div wire:key="icd10-dropdown-list" class="absolute z-20 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                        @foreach ($icd10Results as $res)
                        <button type="button" wire:mousedown.prevent="selectIcd10({{ $res['id'] }})" wire:key="icd10-opt-{{ $res['id'] }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2">
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
                    <div wire:key="icd10-selected-row-{{ $icd['id'] }}-{{ $index }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
                        <div class="flex items-center gap-3">
                            <span class="font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $icd['kode'] }}</span>
                            <span class="text-zinc-700 dark:text-zinc-300 text-xs">{{ $icd['nama_penyakit'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if (isset($icd['is_primary']) && $icd['is_primary'])
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

            <div class="space-y-4">
                <flux:heading size="md" class="font-bold">Prosedur Medis ICD-9</flux:heading>

                @if ($isEditable)
                <div class="relative">
                    <flux:input wire:model.live.debounce.250ms="icd9Query" placeholder="Cari Kode atau Nama Prosedur ICD-9..." icon="magnifying-glass" />

                    @if (count($icd9Results) > 0)
                    <div wire:key="icd9-dropdown-list" class="absolute z-20 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                        @foreach ($icd9Results as $res)
                        <button type="button" wire:mousedown.prevent="selectIcd9({{ $res['id'] }})" wire:key="icd9-opt-{{ $res['id'] }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between gap-2">
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
                    <div wire:key="icd9-selected-row-{{ $icd['id'] }}-{{ $index }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-950 rounded-lg border border-zinc-200/60 dark:border-zinc-800/80 text-sm">
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

    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.beaker class="w-5 h-5 text-emerald-500" />
            <flux:heading size="lg" class="font-bold">4. Rencana Resep Elektronik (E-Resep)</flux:heading>
        </div>

        @if ($isEditable)
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

            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-6 relative">
                    <flux:input wire:model.live.debounce.250ms="drugQuery" label="Cari & Pilih Obat" placeholder="Ketik nama obat..." />
                    @if (count($drugResults) > 0)
                    <div wire:key="drug-dropdown-list" class="absolute z-30 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                        @foreach ($drugResults as $res)
                        <button type="button" wire:mousedown.prevent="selectDrug({{ $res['id'] }})" wire:key="drug-opt-{{ $res['id'] }}" class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between items-center">
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

            @if ($presc_type === 'racikan' && count($presc_ingredients) > 0)
            <div class="bg-zinc-100/50 dark:bg-zinc-950 p-4 rounded-lg border border-zinc-200/50 dark:border-zinc-800">
                <flux:heading size="sm" class="mb-3 font-semibold text-zinc-800 dark:text-zinc-300">Bahan Racikan Terpilih:</flux:heading>
                <div class="space-y-2">
                    @foreach ($presc_ingredients as $i => $ing)
                    <div wire:key="ing-row-{{ $i }}" class="flex justify-between items-center text-xs p-2 bg-white dark:bg-zinc-900 rounded border border-zinc-150 dark:border-zinc-850">
                        <span><strong>{{ $ing['nama_obat'] }}</strong> ({{ $ing['jumlah'] }} {{ $ing['satuan'] }})</span>
                        <flux:button variant="ghost" size="xs" icon="x-mark" class="text-red-500" wire:click="removeIngredient({{ $i }})" />
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

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
                    <flux:table.row :key="'presc-row-'.$index">
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
                                <li wire:key="presc-item-{{ $index }}-{{ $itemIndex }}">{{ $item['nama_obat'] }} <span class="text-zinc-500 font-mono text-[10px]">({{ $item['jumlah'] }} {{ $item['satuan'] }})</span></li>
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

    {{-- ─── SECTION 5: Lab Ordering ─────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-100 dark:border-zinc-800">
            <flux:icon.beaker class="w-5 h-5 text-purple-500" />
            <flux:heading size="lg" class="font-bold">5. Permintaan Pemeriksaan Laboratorium</flux:heading>
            @if ($existingLabOrderId)
            <flux:badge color="amber" size="sm">Order #{{ $existingLabOrderId }}</flux:badge>
            @endif
        </div>

        @if ($isEditable)
        {{-- Test Search --}}
        <div class="mb-5 relative">
            <flux:input
                wire:model.live="labQuery"
                label="Cari Tes Laboratorium"
                placeholder="Ketik nama tes atau kategori (min. 2 karakter)..."
                icon="magnifying-glass"
            />

            @if (count($labResults) > 0)
            <div class="absolute z-30 top-full mt-1 left-0 right-0 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl overflow-hidden">
                <div class="max-h-60 overflow-y-auto divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach ($labResults as $item)
                    <button type="button"
                        wire:click="addLabTest({{ $item['id'] }})"
                        class="w-full text-left px-4 py-3 hover:bg-purple-50 dark:hover:bg-purple-950/30 transition-colors group">
                        <div class="flex justify-between items-center">
                            <div>
                                <span class="font-semibold text-zinc-900 dark:text-white group-hover:text-purple-700 dark:group-hover:text-purple-300">{{ $item['test_name'] }}</span>
                                <span class="ml-2 text-xs text-zinc-400 font-mono">{{ $item['category'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-purple-600 dark:text-purple-400 font-mono">Rp {{ number_format($item['tariff'], 0, ',', '.') }}</span>
                        </div>
                        @if ($item['default_normal_range'])
                        <div class="text-xs text-zinc-400 mt-0.5">Nilai rujukan: {{ $item['default_normal_range'] }} {{ $item['default_unit'] }}</div>
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
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
                    <flux:table.row wire:key="lab-test-{{ $idx }}">
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
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4 bg-zinc-50 dark:bg-zinc-950 p-6 rounded-xl border border-zinc-200/60 dark:border-zinc-800/80">
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