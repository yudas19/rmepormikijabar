<?php

use App\Models\MasterAturanPakai;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\MasterMetodeRacik;
use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\MedicalRecordIcd10;
use App\Models\MedicalRecordIcd9;
use App\Models\MedicalRecordPrescription;
use App\Models\MedicalRecordPrescriptionItem;
use App\Models\Pendaftaran;
use App\Models\SuratKeterangan;
use App\Models\SuratRujukan;
use Flux\Flux;
use Livewire\Component;

new class extends Component
{
    public $recordId;

    public $record;

    public $poliklinik;

    // TTV / Vital Signs
    public $tensi_sistole;

    public $tensi_diastole;

    public $pulse_rate;

    public $respiratory_rate;

    public $temperature;

    public $weight;

    public $height;

    public $bmi;

    public $keadaan_umum = 'Good';

    public $kesadaran_gcs = 'Compos Mentis';

    public $gcs_eye = 4;

    public $gcs_verbal = 5;

    public $gcs_motor = 6;

    public $gcs_score = 15;

    // SOAPE
    public $subjective;

    public $objective;

    public $assessment;

    public $plan;

    public $evaluation;

    // Status & Edit state
    public $status;

    public $isEditable = true;

    // Autocomplete states
    public $icd10Query = '';

    public $icd10Results = [];

    public $selectedIcd10s = [];

    public $icd9Query = '';

    public $icd9Results = [];

    public $selectedIcd9s = [];

    // Prescription form states
    public $presc_type = 'non-racikan';

    public $presc_nama_racikan = '';

    public $presc_metode_racik_id = '';

    public $presc_jumlah_kemasan = '';

    public $presc_aturan_pakai = '';

    public $presc_catatan = '';

    public $drugQuery = '';

    public $drugResults = [];

    public $selectedDrug = null;

    public $drugQty = '';

    // Prescription compilation list
    public $prescriptionsList = [];

    // Letter Modal states
    public $showSickLeaveModal = false;

    public $showHealthCertModal = false;

    public $showReferralModal = false;

    // Letter form inputs
    public $sick_start_date;

    public $sick_end_date;

    public $sick_diagnose = '';

    public $sick_dokter_id = '';

    public $health_height = '';

    public $health_weight = '';

    public $health_tensi = '';

    public $health_butawarna = 'tidak';

    public $health_catatan = 'Fisik dalam batas normal';

    public $health_dokter_id = '';

    public $ref_faskes_tujuan = '';

    public $ref_diagnosa = '';

    public $ref_catatan = '';

    public $ref_dokter_id = '';

    public function mount($record)
    {
        $this->recordId = $record->id;
        $this->record = $record;
        $this->poliklinik = $record->poliklinik_type;

        // Load TTV values
        $this->tensi_sistole = $record->tensi_sistole;
        $this->tensi_diastole = $record->tensi_diastole;
        $this->pulse_rate = $record->pulse_rate;
        $this->respiratory_rate = $record->respiratory_rate;
        $this->temperature = $record->temperature;
        $this->weight = $record->weight;
        $this->height = $record->height;
        $this->bmi = $record->bmi;
        $this->keadaan_umum = $record->keadaan_umum ?? 'Good';
        $this->kesadaran_gcs = $record->kesadaran_gcs ?? 'Compos Mentis';
        $this->gcs_eye = $record->gcs_eye ?? 4;
        $this->gcs_verbal = $record->gcs_verbal ?? 5;
        $this->gcs_motor = $record->gcs_motor ?? 6;
        $this->gcs_score = $record->gcs_score ?? 15;

        // Load SOAPE values
        $this->subjective = $record->subjective;
        $this->objective = $record->objective;
        $this->assessment = $record->assessment;
        $this->plan = $record->plan;
        $this->evaluation = $record->evaluation;

        // Load status
        $this->status = $record->status;
        $this->isEditable = ($this->status !== 'completed');

        // Automatically set status to anamnesis or examination based on current status
        if ($this->status === 'waiting') {
            $this->status = 'anamnesis';
            $record->update(['status' => 'anamnesis']);
            $record->pendaftaran->update(['status_antrean' => 'pemeriksaan ttv']);
        }

        // Preload ICD-10s
        foreach ($record->icd10s as $icd10) {
            $this->selectedIcd10s[] = [
                'id' => $icd10->master_icd10_id,
                'kode' => $icd10->icd10_code,
                'nama_penyakit' => $icd10->icd10_name,
                'is_primary' => $icd10->is_primary,
            ];
        }

        // Preload ICD-9s
        foreach ($record->icd9s as $icd9) {
            $this->selectedIcd9s[] = [
                'id' => $icd9->master_icd9_id,
                'kode' => $icd9->icd9_code,
                'nama' => $icd9->icd9_name,
            ];
        }

        // Preload Prescriptions
        foreach ($record->prescriptions as $presc) {
            $items = [];
            foreach ($presc->items as $item) {
                $items[] = [
                    'master_obat_id' => $item->master_obat_id,
                    'nama_obat' => $item->masterObat->nama_obat,
                    'jumlah' => $item->jumlah,
                    'satuan' => $item->satuan,
                ];
            }

            $this->prescriptionsList[] = [
                'type' => $presc->type,
                'nama_racikan' => $presc->nama_racikan,
                'metode_racik_id' => $presc->metode_racik_id,
                'metode_racik_nama' => $presc->metodeRacik ? $presc->metodeRacik->nama_metode_racik : '',
                'jumlah_kemasan' => $presc->jumlah_kemasan,
                'aturan_pakai' => $presc->aturan_pakai,
                'catatan' => $presc->catatan,
                'items' => $items,
            ];
        }

        // Set default letter inputs
        $this->sick_start_date = date('Y-m-d');
        $this->sick_end_date = date('Y-m-d', strtotime('+3 days'));
        $this->sick_dokter_id = $record->pendaftaran->dokter_id ?? '';
        $this->health_dokter_id = $record->pendaftaran->dokter_id ?? '';
        $this->ref_dokter_id = $record->pendaftaran->dokter_id ?? '';
        $this->health_tensi = ($this->tensi_sistole && $this->tensi_diastole) ? $this->tensi_sistole.'/'.$this->tensi_diastole : '120/80';
        $this->health_height = $this->height;
        $this->health_weight = $this->weight;
    }

    public function updatedWeight()
    {
        $this->calculateBmi();
    }

    public function updatedHeight()
    {
        $this->calculateBmi();
    }

    public function calculateBmi()
    {
        if ($this->weight > 0 && $this->height > 0) {
            $heightInMeters = $this->height / 100;
            $this->bmi = round($this->weight / ($heightInMeters * $heightInMeters), 1);
        } else {
            $this->bmi = null;
        }
    }

    public function updatedGcsEye()
    {
        $this->calculateGcs();
    }

    public function updatedGcsVerbal()
    {
        $this->calculateGcs();
    }

    public function updatedGcsMotor()
    {
        $this->calculateGcs();
    }

    public function calculateGcs()
    {
        $this->gcs_score = intval($this->gcs_eye) + intval($this->gcs_verbal) + intval($this->gcs_motor);
    }

    // ICD-10 Autocomplete
    public function updatedIcd10Query()
    {
        if (strlen($this->icd10Query) >= 2) {
            $this->icd10Results = MasterIcd10::where('kode', 'like', '%'.$this->icd10Query.'%')
                ->orWhere('nama_penyakit', 'like', '%'.$this->icd10Query.'%')
                ->take(8)
                ->get()
                ->toArray();
        } else {
            $this->icd10Results = [];
        }
    }

    public function selectIcd10($id)
    {
        $icd10 = MasterIcd10::findOrFail($id);

        // Check if already selected
        foreach ($this->selectedIcd10s as $selected) {
            if ($selected['id'] == $icd10->id) {
                $this->icd10Query = '';
                $this->icd10Results = [];

                return;
            }
        }

        // First item is primary by default if none are primary
        $hasPrimary = false;
        foreach ($this->selectedIcd10s as $selected) {
            if ($selected['is_primary']) {
                $hasPrimary = true;
                break;
            }
        }

        $this->selectedIcd10s[] = [
            'id' => $icd10->id,
            'kode' => $icd10->kode,
            'nama_penyakit' => $icd10->nama_penyakit,
            'is_primary' => ! $hasPrimary,
        ];

        $this->icd10Query = '';
        $this->icd10Results = [];
    }

    public function setPrimaryIcd10($index)
    {
        foreach ($this->selectedIcd10s as $i => &$selected) {
            $selected['is_primary'] = ($i === $index);
        }
    }

    public function removeIcd10($index)
    {
        array_splice($this->selectedIcd10s, $index, 1);

        // If primary was deleted, assign primary to the first item
        if (count($this->selectedIcd10s) > 0) {
            $hasPrimary = false;
            foreach ($this->selectedIcd10s as $selected) {
                if ($selected['is_primary']) {
                    $hasPrimary = true;
                    break;
                }
            }
            if (! $hasPrimary) {
                $this->selectedIcd10s[0]['is_primary'] = true;
            }
        }
    }

    // ICD-9 Autocomplete
    public function updatedIcd9Query()
    {
        if (strlen($this->icd9Query) >= 2) {
            $this->icd9Results = MasterIcd9::where('kode', 'like', '%'.$this->icd9Query.'%')
                ->orWhere('nama', 'like', '%'.$this->icd9Query.'%')
                ->take(8)
                ->get()
                ->toArray();
        } else {
            $this->icd9Results = [];
        }
    }

    public function selectIcd9($id)
    {
        $icd9 = MasterIcd9::findOrFail($id);

        foreach ($this->selectedIcd9s as $selected) {
            if ($selected['id'] == $icd9->id) {
                $this->icd9Query = '';
                $this->icd9Results = [];

                return;
            }
        }

        $this->selectedIcd9s[] = [
            'id' => $icd9->id,
            'kode' => $icd9->kode,
            'nama' => $icd9->nama,
        ];

        $this->icd9Query = '';
        $this->icd9Results = [];
    }

    public function removeIcd9($index)
    {
        array_splice($this->selectedIcd9s, $index, 1);
    }

    // Prescription Add Flow
    public function updatedDrugQuery()
    {
        if (strlen($this->drugQuery) >= 2) {
            $this->drugResults = MasterObat::where('nama_obat', 'like', '%'.$this->drugQuery.'%')
                ->where('is_aktif', true)
                ->take(8)
                ->get()
                ->toArray();
        } else {
            $this->drugResults = [];
        }
    }

    public function selectDrug($id)
    {
        $this->selectedDrug = MasterObat::findOrFail($id)->toArray();
        $this->drugQuery = $this->selectedDrug['nama_obat'];
        $this->drugResults = [];
    }

    public function addIngredient()
    {
        if (! $this->selectedDrug) {
            Flux::toast(variant: 'danger', text: 'Pilih obat terlebih dahulu.');

            return;
        }

        if (! $this->drugQty || floatval($this->drugQty) <= 0) {
            Flux::toast(variant: 'danger', text: 'Jumlah obat tidak valid.');

            return;
        }

        // If non-racikan, we only have one item, so reset other ingredients
        if ($this->presc_type === 'non-racikan') {
            $this->prescriptionsList[] = [
                'type' => 'non-racikan',
                'nama_racikan' => null,
                'metode_racik_id' => null,
                'metode_racik_nama' => '',
                'jumlah_kemasan' => null,
                'aturan_pakai' => $this->presc_aturan_pakai,
                'catatan' => $this->presc_catatan,
                'items' => [
                    [
                        'master_obat_id' => $this->selectedDrug['id'],
                        'nama_obat' => $this->selectedDrug['nama_obat'],
                        'jumlah' => floatval($this->drugQty),
                        'satuan' => $this->selectedDrug['satuan'],
                    ],
                ],
            ];
            $this->resetPrescriptionForm();
            Flux::toast(variant: 'success', text: 'Obat berhasil ditambahkan ke resep.');
        } else {
            // Racikan: compilation of ingredients
            $this->presc_ingredients[] = [
                'master_obat_id' => $this->selectedDrug['id'],
                'nama_obat' => $this->selectedDrug['nama_obat'],
                'jumlah' => floatval($this->drugQty),
                'satuan' => $this->selectedDrug['satuan'],
            ];

            $this->selectedDrug = null;
            $this->drugQuery = '';
            Flux::toast(variant: 'success', text: 'Bahan racikan berhasil ditambahkan.');
        }
    }

    public $presc_ingredients = [];

    public function removeIngredient($index)
    {
        array_splice($this->presc_ingredients, $index, 1);
    }

    public function addRacikanPrescription()
    {
        $this->validate([
            'presc_nama_racikan' => 'required|string|max:100',
            'presc_metode_racik_id' => 'required|exists:master_metode_raciks,id',
            'presc_jumlah_kemasan' => 'required|integer|min:1',
            'presc_aturan_pakai' => 'required|string',
        ]);

        if (count($this->presc_ingredients) === 0) {
            Flux::toast(variant: 'danger', text: 'Tambahkan minimal 1 bahan obat untuk racikan.');

            return;
        }

        $metode = MasterMetodeRacik::find($this->presc_metode_racik_id);

        $this->prescriptionsList[] = [
            'type' => 'racikan',
            'nama_racikan' => $this->presc_nama_racikan,
            'metode_racik_id' => $this->presc_metode_racik_id,
            'metode_racik_nama' => $metode->nama_metode_racik,
            'jumlah_kemasan' => intval($this->presc_jumlah_kemasan),
            'aturan_pakai' => $this->presc_aturan_pakai,
            'catatan' => $this->presc_catatan,
            'items' => $this->presc_ingredients,
        ];

        $this->resetPrescriptionForm();
        Flux::toast(variant: 'success', text: 'Obat racikan berhasil ditambahkan ke resep.');
    }

    public function resetPrescriptionForm()
    {
        $this->selectedDrug = null;
        $this->drugQuery = '';
        $this->drugQty = '';
        $this->presc_nama_racikan = '';
        $this->presc_metode_racik_id = '';
        $this->presc_jumlah_kemasan = '';
        $this->presc_aturan_pakai = '';
        $this->presc_catatan = '';
        $this->presc_ingredients = [];
    }

    public function removePrescription($index)
    {
        array_splice($this->prescriptionsList, $index, 1);
        Flux::toast(variant: 'info', text: 'Resep dihapus.');
    }

    // Save Logic
    public function saveDraft()
    {
        if (! $this->isEditable) {
            return;
        }

        $this->saveData();

        Flux::toast(variant: 'success', text: 'Draft rekam medis berhasil disimpan.');
    }

    public function changeStatus($newStatus)
    {
        if (! $this->isEditable) {
            return;
        }

        if (! in_array($newStatus, ['anamnesis', 'waiting_doctor', 'examination'])) {
            return;
        }

        $this->status = $newStatus;
        $this->record->update(['status' => $newStatus]);

        // Map status to pendaftaran status antrean
        $antreanStatus = 'pemeriksaan ttv';
        if ($newStatus === 'waiting_doctor') {
            $antreanStatus = 'dipanggil';
        } elseif ($newStatus === 'examination') {
            $antreanStatus = 'diperiksa';
        }

        $this->record->pendaftaran->update(['status_antrean' => $antreanStatus]);
        $this->saveData();

        Flux::toast(variant: 'success', text: 'Status kunjungan berhasil diubah ke: '.ucfirst($newStatus));
    }

    public function finalizeAndLock()
    {
        if (! $this->isEditable) {
            return;
        }

        // Validate mandatory medical details
        $this->validate([
            'tensi_sistole' => 'required|integer|min:50|max:250',
            'tensi_diastole' => 'required|integer|min:30|max:150',
            'pulse_rate' => 'required|integer|min:30|max:220',
            'respiratory_rate' => 'required|integer|min:8|max:60',
            'temperature' => 'required|numeric|min:30|max:45',
            'weight' => 'required|numeric|min:1|max:300',
            'height' => 'required|integer|min:30|max:250',
            'subjective' => 'required|string',
            'objective' => 'required|string',
            'assessment' => 'required|string',
            'plan' => 'required|string',
        ]);

        if (count($this->selectedIcd10s) === 0) {
            Flux::toast(variant: 'danger', text: 'Wajib mengisi minimal 1 Diagnosis ICD-10 sebelum melakukan finalisasi.');

            return;
        }

        $this->status = 'completed';
        $this->isEditable = false;

        $this->saveData();

        $this->record->update(['status' => 'completed']);
        $this->record->pendaftaran->update(['status_antrean' => 'selesai']);

        Flux::toast(variant: 'success', text: 'Pemeriksaan Medis telah difinalisasi dan rekam medis dikunci.');
    }

    protected function saveData()
    {
        // 1. Update Medical Record Fields
        $this->record->update([
            'tensi_sistole' => $this->tensi_sistole ? intval($this->tensi_sistole) : null,
            'tensi_diastole' => $this->tensi_diastole ? intval($this->tensi_diastole) : null,
            'pulse_rate' => $this->pulse_rate ? intval($this->pulse_rate) : null,
            'respiratory_rate' => $this->respiratory_rate ? intval($this->respiratory_rate) : null,
            'temperature' => $this->temperature ? floatval($this->temperature) : null,
            'weight' => $this->weight ? floatval($this->weight) : null,
            'height' => $this->height ? intval($this->height) : null,
            'bmi' => $this->bmi ? floatval($this->bmi) : null,
            'keadaan_umum' => $this->keadaan_umum,
            'kesadaran_gcs' => $this->kesadaran_gcs,
            'gcs_eye' => $this->gcs_eye ? intval($this->gcs_eye) : null,
            'gcs_verbal' => $this->gcs_verbal ? intval($this->gcs_verbal) : null,
            'gcs_motor' => $this->gcs_motor ? intval($this->gcs_motor) : null,
            'gcs_score' => $this->gcs_score ? intval($this->gcs_score) : null,
            'subjective' => $this->subjective,
            'objective' => $this->objective,
            'assessment' => $this->assessment,
            'plan' => $this->plan,
            'evaluation' => $this->evaluation,
            'updated_by' => auth()->id(),
        ]);

        // 2. Synchronize ICD-10 Diagnoses
        MedicalRecordIcd10::where('medical_record_id', $this->recordId)->delete();
        foreach ($this->selectedIcd10s as $icd10) {
            MedicalRecordIcd10::create([
                'medical_record_id' => $this->recordId,
                'master_icd10_id' => $icd10['id'],
                'icd10_code' => $icd10['kode'],
                'icd10_name' => $icd10['nama_penyakit'],
                'is_primary' => $icd10['is_primary'],
            ]);
        }

        // 3. Synchronize ICD-9 Procedures
        MedicalRecordIcd9::where('medical_record_id', $this->recordId)->delete();
        foreach ($this->selectedIcd9s as $icd9) {
            MedicalRecordIcd9::create([
                'medical_record_id' => $this->recordId,
                'master_icd9_id' => $icd9['id'],
                'icd9_code' => $icd9['kode'],
                'icd9_name' => $icd9['nama'],
            ]);
        }

        // 4. Synchronize Prescriptions
        $existingPrescIds = MedicalRecordPrescription::where('medical_record_id', $this->recordId)->pluck('id')->toArray();
        MedicalRecordPrescriptionItem::whereIn('prescription_id', $existingPrescIds)->delete();
        MedicalRecordPrescription::where('medical_record_id', $this->recordId)->delete();

        foreach ($this->prescriptionsList as $prescData) {
            $prescription = MedicalRecordPrescription::create([
                'medical_record_id' => $this->recordId,
                'type' => $prescData['type'],
                'nama_racikan' => $prescData['nama_racikan'],
                'metode_racik_id' => $prescData['metode_racik_id'] ?: null,
                'jumlah_kemasan' => $prescData['jumlah_kemasan'] ?: null,
                'aturan_pakai' => $prescData['aturan_pakai'],
                'catatan' => $prescData['catatan'],
            ]);

            foreach ($prescData['items'] as $item) {
                MedicalRecordPrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'master_obat_id' => $item['master_obat_id'],
                    'jumlah' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                ]);
            }
        }
    }

    // Letters Generation Logic
    public function openSickLeave()
    {
        $this->sick_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        $this->showSickLeaveModal = true;
    }

    public function generateSickLeave()
    {
        $this->validate([
            'sick_start_date' => 'required|date',
            'sick_end_date' => 'required|date|after_or_equal:sick_start_date',
            'sick_diagnose' => 'nullable|string',
            'sick_dokter_id' => 'required|exists:master_petugass,id',
        ]);

        $no_surat = 'SKD/SAKIT/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratKeterangan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->record->pendaftaran_id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->sick_dokter_id,
            'jenis_surat' => 'sakit',
            'konten_surat' => [
                'tanggal_mulai' => $this->sick_start_date,
                'tanggal_selesai' => $this->sick_end_date,
                'diagnosa' => $this->sick_diagnose,
            ],
        ]);

        $this->showSickLeaveModal = false;
        Flux::toast(variant: 'success', text: 'Surat Keterangan Sakit berhasil dibuat. Membuka tab cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.certificate', ['id' => $saved->id])]);
    }

    public function openHealthCert()
    {
        $this->health_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        $this->health_tensi = ($this->tensi_sistole && $this->tensi_diastole) ? $this->tensi_sistole.'/'.$this->tensi_diastole : '120/80';
        $this->health_height = $this->height;
        $this->health_weight = $this->weight;
        $this->showHealthCertModal = true;
    }

    public function generateHealthCert()
    {
        $this->validate([
            'health_height' => 'required|numeric',
            'health_weight' => 'required|numeric',
            'health_tensi' => 'required|string|max:15',
            'health_butawarna' => 'required|in:ya,tidak',
            'health_catatan' => 'nullable|string',
            'health_dokter_id' => 'required|exists:master_petugass,id',
        ]);

        $no_surat = 'SKD/SEHAT/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratKeterangan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->record->pendaftaran_id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->health_dokter_id,
            'jenis_surat' => 'sehat',
            'konten_surat' => [
                'tinggi_badan' => $this->health_height,
                'berat_badan' => $this->health_weight,
                'tekanan_darah' => $this->health_tensi,
                'buta_warna' => $this->health_butawarna,
                'catatan' => $this->health_catatan,
            ],
        ]);

        $this->showHealthCertModal = false;
        Flux::toast(variant: 'success', text: 'Surat Keterangan Sehat berhasil dibuat. Membuka tab cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.certificate', ['id' => $saved->id])]);
    }

    public function openReferral()
    {
        $this->ref_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        // Pull primary diagnosis if any
        $primaryDiag = '';
        foreach ($this->selectedIcd10s as $selected) {
            if ($selected['is_primary']) {
                $primaryDiag = $selected['kode'].' - '.$selected['nama_penyakit'];
                break;
            }
        }
        $this->ref_diagnosa = $primaryDiag;
        $this->showReferralModal = true;
    }

    public function generateReferral()
    {
        $this->validate([
            'ref_faskes_tujuan' => 'required|string|max:100',
            'ref_diagnosa' => 'required|string|max:100',
            'ref_catatan' => 'nullable|string',
            'ref_dokter_id' => 'required|exists:master_petugass,id',
        ]);

        $no_surat = 'RUJ/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratRujukan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->record->pendaftaran_id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->ref_dokter_id,
            'faskes_tujuan' => $this->ref_faskes_tujuan,
            'diagnosa' => $this->ref_diagnosa,
            'catatan' => $this->ref_catatan ?: null,
            'tanggal_rujukan' => date('Y-m-d'),
        ]);

        $this->showReferralModal = false;
        Flux::toast(variant: 'success', text: 'Surat Rujukan berhasil dibuat. Membuka tab cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.referral', ['id' => $saved->id])]);
    }

    public function render()
    {
        return view('components.⚡medical-record.⚡poli-umum.poli-umum', [
            'metodeRaciks' => MasterMetodeRacik::all(),
            'aturanPakais' => MasterAturanPakai::all(),
            'doctors' => MasterPetugas::where('jenis_petugas', 'Dokter')->where('is_aktif', true)->get(),
        ]);
    }
};
