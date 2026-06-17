<?php

use App\Http\Controllers\MedicalLetterController;
use App\Models\KiaAncRecord;
use App\Models\LabOrder;
use App\Models\LabOrderResult;
use App\Models\MasterAturanPakai;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\MasterLabTest;
use App\Models\MasterMetodeRacik;
use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\MedicalRecordIcd10;
use App\Models\MedicalRecordIcd9;
use App\Models\MedicalRecordPrescription;
use App\Models\MedicalRecordPrescriptionItem;
use App\Models\OdontogramRecord;
use App\Models\Pendaftaran;
use App\Models\SuratKeterangan;
use App\Models\SuratPersetujuan;
use App\Models\SuratRujukan;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public $keluhan_utama;

    public $riwayat_alergi;

    // Status & Edit state
    public $status;

    public $isEditable = true;

    public $tanggal_kunjungan;

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

    // ─── Odontogram (Poli Gigi) ─────────────────────────────────────────
    /** @var array<int, array{condition_code: string, notes: string}> tooth_number => data */
    public array $teethMap = [];

    public ?int $activeTooth = null;

    public string $activeToothCondition = 'SOU';

    public string $activeToothNotes = '';

    public bool $showToothModal = false;

    // ─── KIA ANC ────────────────────────────────────────────────────────
    public string $anc_hpht = '';

    public string $anc_tp = '';

    public ?int $anc_uk_minggu = null;

    public string $anc_tfu = '';

    public string $anc_lila = '';

    public string $anc_djj = '';

    public string $anc_presentasi = '';

    public string $anc_leopold_1 = '';

    public string $anc_leopold_2 = '';

    public string $anc_leopold_3 = '';

    public string $anc_leopold_4 = '';

    public string $anc_golongan_darah = '';

    public bool $anc_riwayat_sc = false;

    public string $anc_catatan_bidan = '';

    // ─── Lab Ordering ───────────────────────────────────────────────────────
    /** @var array<int, array{id: int, test_name: string, category: string, tariff: int, default_normal_range: string|null, default_unit: string|null}> */
    public array $selectedLabTests = [];

    public string $labQuery = '';

    public string $selectedLabTestId = '';

    /** @var array<int, mixed> */
    public array $labResults = [];

    public string $labClinicalNotes = '';

    public ?int $existingLabOrderId = null;

    public int $labTotalTariff = 0;

    // Letter form inputs
    public $sick_start_date;

    public $sick_end_date;

    public $sick_diagnose = '';

    public $sick_dokter_id = '';

    public $health_height = '';

    public $health_weight = '';

    public $health_tensi = '';

    public $health_butawarna = 'Tidak';

    public $health_golongan_darah = 'O';

    public $health_catatan = 'Sehat';

    public $health_dokter_id = '';

    public $ref_faskes_tujuan = '';

    public $ref_diagnosa = '';

    public $ref_catatan = '';

    public $ref_dokter_id = '';

    // Keterangan Bebas Narkoba
    public $showNarkobaModal = false;

    public $narkoba_keperluan = '';

    public $narkoba_hasil = 'Negatif untuk seluruh parameter uji (Amphetamine, THC, Morphine)';

    public $narkoba_dokter_id = '';

    // Informed / General Consent
    public $showConsentModal = false;

    public $consent_type = 'general_consent';

    public $consent_nama_penanggung_jawab = '';

    public $consent_hubungan_penanggung_jawab = 'diri_sendiri';

    public $consent_nik_penanggung_jawab = '';

    public $consent_nama_tindakan_medis = '';

    public $consent_pernyataan = 'setuju';

    public $consent_petugas_id = '';

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

        $this->keluhan_utama = $record->keluhan_utama ?? $record->pendaftaran->keluhan_awal ?? '';
        $this->riwayat_alergi = $record->riwayat_alergi ?? '';

        // Load status & Smart Locking Logic
        $this->status = $record->status;

        $this->tanggal_kunjungan = $record->tanggal_kunjungan ? Carbon::parse($record->tanggal_kunjungan)->format('Y-m-d') : date('Y-m-d');

        $isAdminOrRekamMedis = auth()->user()->hasRole('admin') || auth()->user()->hasRole('rekam_medis');

        $isCompleted = in_array($this->status, ['completed', 'completed_all']);
        $lockTime = Carbon::parse($this->tanggal_kunjungan)->addDays(3);
        $isPastLimit = now()->greaterThanOrEqualTo($lockTime);

        if ($isAdminOrRekamMedis) {
            if ($isCompleted || $isPastLimit) {
                $this->isEditable = false;
            } else {
                $this->isEditable = true;
            }
        } else {
            $this->isEditable = ! ($isCompleted || $isPastLimit);
        }

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
                    'master_obat_id' => $item->requested_obat_id,
                    'nama_obat' => $item->requestedObat?->nama_obat ?? '-',
                    'jumlah' => $item->requested_qty,
                    'satuan' => $item->satuan,
                ];
            }

            $this->prescriptionsList[] = [
                'id' => $presc->id,
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

        // Preload Odontogram (Poli Gigi)
        if ($this->poliklinik === 'gigi') {
            foreach ($record->odontogramRecords as $tooth) {
                $this->teethMap[$tooth->tooth_number] = [
                    'condition_code' => $tooth->condition_code,
                    'notes' => $tooth->notes ?? '',
                ];
            }
        }

        // Preload KIA ANC
        if ($this->poliklinik === 'kia') {
            $anc = $record->kiaAncRecord;
            if ($anc) {
                $this->anc_hpht = $anc->hpht ? $anc->hpht->format('Y-m-d') : '';
                $this->anc_tp = $anc->tp ? $anc->tp->format('Y-m-d') : '';
                $this->anc_uk_minggu = $anc->uk_minggu;
                $this->anc_tfu = (string) ($anc->tfu ?? '');
                $this->anc_lila = (string) ($anc->lila ?? '');
                $this->anc_djj = (string) ($anc->djj ?? '');
                $this->anc_presentasi = $anc->presentasi ?? '';
                $this->anc_leopold_1 = $anc->leopold_1 ?? '';
                $this->anc_leopold_2 = $anc->leopold_2 ?? '';
                $this->anc_leopold_3 = $anc->leopold_3 ?? '';
                $this->anc_leopold_4 = $anc->leopold_4 ?? '';
                $this->anc_golongan_darah = $anc->golongan_darah ?? '';
                $this->anc_riwayat_sc = (bool) $anc->riwayat_sc;
                $this->anc_catatan_bidan = $anc->catatan_bidan ?? '';
            }
        }

        // Preload Lab Order
        $labOrder = $record->labOrders()->with('results.masterLabTest')->latest()->first();
        if ($labOrder) {
            $this->existingLabOrderId = $labOrder->id;
            $this->labClinicalNotes = $labOrder->clinical_notes ?? '';
            foreach ($labOrder->results as $result) {
                $this->selectedLabTests[] = [
                    'id' => $result->master_lab_test_id,
                    'test_name' => $result->test_name_snapshot,
                    'category' => $result->masterLabTest?->category ?? '',
                    'tariff' => $result->tariff_snapshot,
                    'default_normal_range' => $result->normal_range_snapshot,
                    'default_unit' => $result->unit_snapshot,
                ];
            }
            $this->recalcLabTotal();
        }
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
        foreach ($this->selectedIcd10s as $i => $selected) {
            $this->selectedIcd10s[$i]['is_primary'] = ($i === $index);
        }
    }

    public function removeIcd10($index)
    {
        array_splice($this->selectedIcd10s, $index, 1);

        $this->selectedIcd10s = array_values($this->selectedIcd10s);

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

        $this->selectedIcd9s = array_values($this->selectedIcd9s);
    }

    // ─── Odontogram Methods ──────────────────────────────────────────────

    public function openTooth(int $toothNumber): void
    {
        if (! $this->isEditable) {
            return;
        }

        $this->activeTooth = $toothNumber;
        $existing = $this->teethMap[$toothNumber] ?? null;
        $this->activeToothCondition = $existing['condition_code'] ?? 'SOU';
        $this->activeToothNotes = $existing['notes'] ?? '';
        $this->showToothModal = true;
    }

    public function saveToothCondition(): void
    {
        if (! $this->activeTooth || ! $this->isEditable) {
            return;
        }

        $this->teethMap[$this->activeTooth] = [
            'condition_code' => $this->activeToothCondition,
            'notes' => $this->activeToothNotes,
        ];

        $this->showToothModal = false;
        $this->activeTooth = null;
        $this->activeToothNotes = '';
    }

    public function closeToothModal(): void
    {
        $this->showToothModal = false;
        $this->activeTooth = null;
    }

    public function clearTooth(int $toothNumber): void
    {
        if (! $this->isEditable) {
            return;
        }

        unset($this->teethMap[$toothNumber]);
    }

    // ─── ANC (KIA) Methods ──────────────────────────────────────────────

    public function updatedAncHpht(): void
    {
        if (! $this->anc_hpht) {
            $this->anc_tp = '';
            $this->anc_uk_minggu = null;

            return;
        }

        $hpht = Carbon::parse($this->anc_hpht);

        // Naegele's Rule: +7 days, -3 months, +1 year
        $this->anc_tp = $hpht->copy()->addDays(7)->subMonths(3)->addYear()->format('Y-m-d');

        // Calculate gestational age in weeks
        $this->anc_uk_minggu = (int) $hpht->diffInWeeks(now());
    }

    // ─── Lab Ordering Methods ────────────────────────────────────────────

    private function recalcLabTotal(): void
    {
        $this->labTotalTariff = (int) array_sum(array_column($this->selectedLabTests, 'tariff'));
    }

    public function updatedLabQuery(): void
    {
        if (strlen($this->labQuery) >= 2) {
            $this->labResults = MasterLabTest::where('is_aktif', true)
                ->where(function ($q) {
                    $q->where('test_name', 'like', '%'.$this->labQuery.'%')
                        ->orWhere('category', 'like', '%'.$this->labQuery.'%');
                })
                ->orderBy('category')
                ->orderBy('test_name')
                ->take(10)
                ->get()
                ->toArray();
        } else {
            $this->labResults = [];
        }
    }

    public function addLabTest(int $id): void
    {
        if (! $this->isEditable) {
            return;
        }

        // Prevent duplicates
        foreach ($this->selectedLabTests as $t) {
            if ($t['id'] == $id) {
                $this->labQuery = '';
                $this->labResults = [];

                return;
            }
        }

        $test = MasterLabTest::findOrFail($id);

        $this->selectedLabTests[] = [
            'id' => $test->id,
            'test_name' => $test->test_name,
            'category' => $test->category,
            'tariff' => $test->tariff,
            'default_normal_range' => $test->default_normal_range,
            'default_unit' => $test->default_unit,
        ];

        $this->selectedLabTests = array_values($this->selectedLabTests);
        $this->labQuery = '';
        $this->labResults = [];
        $this->recalcLabTotal();
    }

    public function removeLabTest(int $index): void
    {
        if (! $this->isEditable) {
            return;
        }

        array_splice($this->selectedLabTests, $index, 1);
        $this->selectedLabTests = array_values($this->selectedLabTests);
        $this->recalcLabTotal();
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
        $petugas = MasterPetugas::where('user_id', Auth::id())->first();
        $updateData = [
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
            'keluhan_utama' => $this->keluhan_utama,
            'riwayat_alergi' => $this->riwayat_alergi,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'updated_by' => Auth::id(),
        ];

        if ($petugas) {
            if ($petugas->jenis_petugas === 'Dokter') {
                $updateData['dokter_id'] = $petugas->id;
            } elseif ($petugas->jenis_petugas === 'Perawat') {
                $updateData['perawat_id'] = $petugas->id;
            }
        }

        $this->record->update($updateData);

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
                    'requested_obat_id' => $item['master_obat_id'],
                    'requested_qty' => $item['jumlah'],
                    'satuan' => $item['satuan'],
                    'requested_signa' => $prescData['aturan_pakai'],
                ]);
            }
        }

        // 5. Synchronize Odontogram (Poli Gigi only)
        if ($this->poliklinik === 'gigi') {
            OdontogramRecord::where('medical_record_id', $this->recordId)->delete();

            foreach ($this->teethMap as $toothNumber => $data) {
                OdontogramRecord::create([
                    'medical_record_id' => $this->recordId,
                    'tooth_number' => $toothNumber,
                    'condition_code' => $data['condition_code'],
                    'notes' => $data['notes'] ?: null,
                ]);
            }
        }

        // 6. Upsert KIA ANC Record (KIA only)
        if ($this->poliklinik === 'kia') {
            KiaAncRecord::updateOrCreate(
                ['medical_record_id' => $this->recordId],
                [
                    'hpht' => $this->anc_hpht ?: null,
                    'tp' => $this->anc_tp ?: null,
                    'uk_minggu' => $this->anc_uk_minggu,
                    'tfu' => $this->anc_tfu !== '' ? floatval($this->anc_tfu) : null,
                    'lila' => $this->anc_lila !== '' ? floatval($this->anc_lila) : null,
                    'djj' => $this->anc_djj !== '' ? intval($this->anc_djj) : null,
                    'presentasi' => $this->anc_presentasi ?: null,
                    'leopold_1' => $this->anc_leopold_1 ?: null,
                    'leopold_2' => $this->anc_leopold_2 ?: null,
                    'leopold_3' => $this->anc_leopold_3 ?: null,
                    'leopold_4' => $this->anc_leopold_4 ?: null,
                    'golongan_darah' => $this->anc_golongan_darah ?: null,
                    'riwayat_sc' => $this->anc_riwayat_sc,
                    'catatan_bidan' => $this->anc_catatan_bidan ?: null,
                ]
            );
        }

        // 7. Synchronize Lab Order
        if (count($this->selectedLabTests) > 0) {
            $dokter = MasterPetugas::where('user_id', Auth::id())->first();
            $totalTariff = $this->labTotalTariff;

            $labOrder = LabOrder::updateOrCreate(
                ['id' => $this->existingLabOrderId ?? 0],
                [
                    'medical_record_id' => $this->recordId,
                    'requested_by_id' => $dokter?->id,
                    'total_tariff' => $totalTariff,
                    'clinical_notes' => $this->labClinicalNotes ?: null,
                    // Only override status if it's still pending (don't downgrade a completed order)
                    'status' => $this->existingLabOrderId
                        ? LabOrder::find($this->existingLabOrderId)?->status ?? 'pending'
                        : 'pending',
                ]
            );

            $this->existingLabOrderId = $labOrder->id;

            // Delete existing results and recreate (snapshot pattern)
            if ($labOrder->status === 'pending') {
                $labOrder->results()->delete();

                foreach ($this->selectedLabTests as $test) {
                    LabOrderResult::create([
                        'lab_order_id' => $labOrder->id,
                        'master_lab_test_id' => $test['id'],
                        'test_name_snapshot' => $test['test_name'],
                        'normal_range_snapshot' => $test['default_normal_range'],
                        'unit_snapshot' => $test['default_unit'],
                        'tariff_snapshot' => $test['tariff'],
                    ]);
                }
            }
        } elseif ($this->existingLabOrderId) {
            // Doctor removed all tests — delete the order if still pending
            $labOrder = LabOrder::find($this->existingLabOrderId);
            if ($labOrder && $labOrder->status === 'pending') {
                $labOrder->results()->delete();
                $labOrder->delete();
                $this->existingLabOrderId = null;
            }
        }

        $this->record->load(['perawat', 'dokter']);

        // Re-load the prescriptions from DB so that they have valid IDs for printing
        $this->record->load(['prescriptions.items.requestedObat', 'prescriptions.metodeRacik']);
        $this->prescriptionsList = [];
        foreach ($this->record->prescriptions as $presc) {
            $items = [];
            foreach ($presc->items as $item) {
                $items[] = [
                    'master_obat_id' => $item->requested_obat_id,
                    'nama_obat' => $item->requestedObat?->nama_obat ?? '-',
                    'jumlah' => $item->requested_qty,
                    'satuan' => $item->satuan,
                ];
            }
            $this->prescriptionsList[] = [
                'id' => $presc->id,
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
    }

    public function printPrescription($id)
    {
        $this->dispatch('open-print-tab', ['url' => route('print.resep', ['id' => $id])]);
    }

    // Letters Generation Logic
    public function openSickLeave()
    {
        $this->sick_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        $this->sick_start_date = date('Y-m-d');
        $this->sick_end_date = date('Y-m-d');
        $this->sick_diagnose = '';
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

        $startDate = Carbon::parse($this->sick_start_date);
        $endDate = Carbon::parse($this->sick_end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        $requestData = [
            'medical_record_id' => $this->record->id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->sick_dokter_id,
            'jenis_surat' => 'surat_sakit',
            'meta_data' => [
                'jumlah_hari' => $days,
                'dari_tanggal' => $this->sick_start_date,
                'sampai_tanggal' => $this->sick_end_date,
                'alasan' => $this->sick_diagnose ?: 'Sakit Rest',
            ],
        ];

        $request = new Request($requestData);
        $controller = new MedicalLetterController;
        $response = $controller->store($request);
        $result = json_decode($response->getContent(), true);

        if (isset($result['success']) && $result['success']) {
            $this->showSickLeaveModal = false;
            Flux::toast(variant: 'success', text: 'Surat Keterangan Sakit berhasil dibuat. Membuka tab cetak...');
            $this->dispatch('open-print-tab', ['url' => $result['print_url']]);
        }
    }

    public function openHealthCert()
    {
        $this->health_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        $this->health_tensi = ($this->tensi_sistole && $this->tensi_diastole) ? $this->tensi_sistole.'/'.$this->tensi_diastole : '120/80';
        $this->health_height = $this->height ?: '';
        $this->health_weight = $this->weight ?: '';
        $this->health_golongan_darah = $this->record->pasien->golongan_darah ?? 'O';
        $this->health_butawarna = 'Tidak';
        $this->health_catatan = 'Sehat';
        $this->showHealthCertModal = true;
    }

    public function generateHealthCert()
    {
        $this->validate([
            'health_height' => 'required|numeric',
            'health_weight' => 'required|numeric',
            'health_tensi' => 'required|string|max:15',
            'health_butawarna' => 'required|in:Ya,Tidak',
            'health_golongan_darah' => 'required|in:A,B,AB,O',
            'health_catatan' => 'required|string',
            'health_dokter_id' => 'required|exists:master_petugass,id',
        ]);

        $requestData = [
            'medical_record_id' => $this->record->id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->health_dokter_id,
            'jenis_surat' => 'surat_sehat',
            'meta_data' => [
                'tinggi_badan' => (int) $this->health_height,
                'berat_badan' => (int) $this->health_weight,
                'tekanan_darah' => $this->health_tensi,
                'golongan_darah' => $this->health_golongan_darah,
                'buta_warna' => $this->health_butawarna,
                'kesimpulan' => $this->health_catatan,
            ],
        ];

        $request = new Request($requestData);
        $controller = new MedicalLetterController;
        $response = $controller->store($request);
        $result = json_decode($response->getContent(), true);

        if (isset($result['success']) && $result['success']) {
            $this->showHealthCertModal = false;
            Flux::toast(variant: 'success', text: 'Surat Keterangan Sehat berhasil dibuat. Membuka tab cetak...');
            $this->dispatch('open-print-tab', ['url' => $result['print_url']]);
        }
    }

    public function printLetter(int $id)
    {
        $this->dispatch('open-print-tab', ['url' => route('medical-letters.print', ['id' => $id])]);
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

    public function openBebasNarkoba()
    {
        $this->narkoba_keperluan = '';
        $this->narkoba_hasil = 'Negatif untuk seluruh parameter uji (Amphetamine, THC, Morphine)';
        $this->narkoba_dokter_id = $this->record->pendaftaran->dokter_id ?? '';
        $this->showNarkobaModal = true;
    }

    public function generateNarkoba()
    {
        $this->validate([
            'narkoba_keperluan' => 'required|string|max:100',
            'narkoba_hasil' => 'required|string',
            'narkoba_dokter_id' => 'required|exists:master_petugass,id',
        ]);

        $no_surat = 'SKD/BEBAS_NARKOBA/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratKeterangan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->record->pendaftaran_id,
            'pasien_id' => $this->record->patient_id,
            'dokter_id' => $this->narkoba_dokter_id,
            'jenis_surat' => 'bebas_narkoba',
            'konten_surat' => [
                'keperluan' => $this->narkoba_keperluan,
                'hasil_tes' => $this->narkoba_hasil,
            ],
        ]);

        $this->showNarkobaModal = false;
        Flux::toast(variant: 'success', text: 'Surat Keterangan Bebas Narkoba berhasil dibuat. Membuka tab cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.certificate', ['id' => $saved->id])]);
    }

    public function openGeneralConsent()
    {
        $this->consent_type = 'general_consent';
        $this->consent_nama_penanggung_jawab = $this->record->pasien->nama_pasien ?? '';
        $this->consent_hubungan_penanggung_jawab = 'diri_sendiri';
        $this->consent_nik_penanggung_jawab = $this->record->pasien->nik ?? '';
        $this->consent_nama_tindakan_medis = '';
        $this->consent_pernyataan = 'setuju';
        $petugas = MasterPetugas::where('user_id', Auth::id())->first();
        $this->consent_petugas_id = $petugas ? $petugas->id : '';
        $this->showConsentModal = true;
    }

    public function openInformedConsent()
    {
        $this->consent_type = 'informed_consent_tindakan';
        $this->consent_nama_penanggung_jawab = $this->record->pasien->nama_pasien ?? '';
        $this->consent_hubungan_penanggung_jawab = 'diri_sendiri';
        $this->consent_nik_penanggung_jawab = $this->record->pasien->nik ?? '';
        $this->consent_nama_tindakan_medis = '';
        $this->consent_pernyataan = 'setuju';
        $petugas = MasterPetugas::where('user_id', Auth::id())->first();
        $this->consent_petugas_id = $petugas ? $petugas->id : '';
        $this->showConsentModal = true;
    }

    public function generateConsent()
    {
        $rules = [
            'consent_nama_penanggung_jawab' => 'required|string|max:100',
            'consent_hubungan_penanggung_jawab' => 'required|in:diri_sendiri,suami,istri,ayah,ibu,anak,lainnya',
            'consent_nik_penanggung_jawab' => 'nullable|string|max:16',
            'consent_nama_tindakan_medis' => 'required_if:consent_type,informed_consent_tindakan|nullable|string|max:100',
            'consent_pernyataan' => 'required|in:setuju,menolak',
            'consent_petugas_id' => 'required|exists:master_petugass,id',
        ];

        $this->validate($rules);

        $no_surat = 'CNT/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratPersetujuan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->record->pendaftaran_id,
            'jenis_persetujuan' => $this->consent_type,
            'nama_penanggung_jawab' => $this->consent_nama_penanggung_jawab,
            'hubungan_penanggung_jawab' => $this->consent_hubungan_penanggung_jawab,
            'nik_penanggung_jawab' => $this->consent_nik_penanggung_jawab ?: null,
            'nama_tindakan_medis' => $this->consent_nama_tindakan_medis ?: null,
            'pernyataan' => $this->consent_pernyataan,
            'petugas_id' => $this->consent_petugas_id,
        ]);

        $this->showConsentModal = false;
        Flux::toast(variant: 'success', text: 'Surat Persetujuan berhasil dibuat. Membuka tab cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.consent', ['id' => $saved->id])]);
    }

    public function updatedSelectedLabTestId($value)
    {
        if ($value) {
            $this->addLabTest(intval($value));
            $this->selectedLabTestId = '';
        }
    }

    public function saveTtv()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Tanda-tanda Vital & Pemeriksaan Fisik berhasil disimpan.');
    }

    public function saveSoape()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Deskripsi Medis (SOAPE) berhasil disimpan.');
    }

    public function saveIcd()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Kode Diagnosis & Prosedur (ICD-10 & ICD-9) berhasil disimpan.');
    }

    public function savePrescription()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Rencana Resep Elektronik berhasil disimpan.');
    }

    public function saveLabOrder()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Permintaan Pemeriksaan Laboratorium berhasil disimpan.');
    }

    public function saveOdontogram()
    {
        if (! $this->isEditable) {
            return;
        }
        $this->saveData();
        Flux::toast(variant: 'success', text: 'Pemeriksaan Odontogram berhasil disimpan.');
    }

    public function printUnified($url)
    {
        $this->dispatch('open-print-tab', ['url' => $url]);
    }

    public function render()
    {
        return view('components.⚡medical-record.⚡poli-umum.poli-umum', [
            'metodeRaciks' => MasterMetodeRacik::all(),
            'aturanPakais' => MasterAturanPakai::all(),
            'doctors' => MasterPetugas::where('jenis_petugas', 'Dokter')->where('is_aktif', true)->get(),
            'staff' => MasterPetugas::where('is_aktif', true)->get(),
            'allLabTests' => MasterLabTest::where('is_aktif', true)->orderBy('category')->orderBy('test_name')->get(),
        ]);
    }
};