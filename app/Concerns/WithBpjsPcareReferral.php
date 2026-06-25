<?php

namespace App\Concerns;

use App\Models\MasterPcare;
use App\Models\MasterPetugas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait WithBpjsPcareReferral
{
    /**
     * Toggles visibility of the BPJS PCare Outward Referral form.
     *
     * @var bool
     */
    public $showPcareReferralForm = false;

    /**
     * BPJS Specialist Code.
     *
     * @var string
     */
    public $rujuk_spesialis = '';

    /**
     * BPJS Subspecialist Code (Nullable).
     *
     * @var string
     */
    public $rujuk_subspesialis = '';

    /**
     * BPJS Facilities Code.
     *
     * @var string
     */
    public $rujuk_sarana = '';

    /**
     * Estimated referral date.
     *
     * @var string
     */
    public $rujuk_tanggal_est = '';

    /**
     * Code for target hospital (PPK Rujukan).
     *
     * @var string
     */
    public $rujuk_ppk_kode = '';

    /**
     * Checkbox to toggle TACC (Time, Age, Comorbidity, Complication) constraints.
     *
     * @var bool
     */
    public $rujuk_is_tacc = false;

    /**
     * BPJS TACC type/category.
     *
     * @var string
     */
    public $rujuk_tacc_jenis = '';

    /**
     * Reasoning for TACC.
     *
     * @var string
     */
    public $rujuk_tacc_alasan = '';

    /**
     * Target Hospital input query for autocomplete search.
     *
     * @var string
     */
    public $faskesQuery = '';

    /**
     * Query autocomplete matching results.
     *
     * @var array
     */
    public $faskesResults = [];

    /**
     * Initialize BPJS PCare Outward Referral traits properties.
     */
    public function initializeWithBpjsPcareReferral(): void
    {
        $this->rujuk_tanggal_est = date('Y-m-d');
    }

    /**
     * Handle search input queries dynamically.
     */
    public function updatedFaskesQuery(): void
    {
        if (strlen($this->faskesQuery) < 2) {
            $this->faskesResults = [];

            return;
        }

        if (Schema::hasTable('master_faskes_rujukans')) {
            $this->faskesResults = DB::table('master_faskes_rujukans')
                ->where('nama_faskes', 'like', '%'.$this->faskesQuery.'%')
                ->orWhere('kode_faskes', 'like', '%'.$this->faskesQuery.'%')
                ->limit(10)
                ->get()
                ->map(fn ($item) => (array) $item)
                ->toArray();
        } else {
            $this->faskesResults = MasterPcare::where('nama_faskes', 'like', '%'.$this->faskesQuery.'%')
                ->orWhere('kode_faskes', 'like', '%'.$this->faskesQuery.'%')
                ->orWhere('nama_pcare', 'like', '%'.$this->faskesQuery.'%')
                ->limit(10)
                ->get()
                ->map(function ($item) {
                    return [
                        'kode_faskes' => $item->kode_faskes ?: $item->kode_pcare,
                        'nama_faskes' => $item->nama_faskes ?: $item->nama_pcare,
                    ];
                })
                ->toArray();
        }
    }

    /**
     * Select a hospital from the search results.
     */
    public function selectFaskes(string $kode, string $nama): void
    {
        $this->rujuk_ppk_kode = $kode;
        $this->faskesQuery = $nama.' ('.$kode.')';
        $this->faskesResults = [];
    }

    /**
     * Generate the BPJS PCare Outward Referral API Payload.
     *
     * @return array<string, mixed>
     */
    public function generateReferralPayload(): array
    {
        $primaryIcd10 = collect($this->selectedIcd15s ?? $this->selectedIcd10s ?? [])
            ->first(fn ($icd) => ! empty($icd['is_primary']));

        $primaryIcd10Code = $primaryIcd10 ? $primaryIcd10['kode'] : '';
        $primaryIcd10Name = $primaryIcd10 ? $primaryIcd10['nama_penyakit'] : '';

        $dokter = MasterPetugas::find($this->record->dokter_id);
        $dokterNama = $dokter ? $dokter->nama_petugas : '';

        return [
            'encounter_id' => $this->record->encounter_id,
            'medical_record_id' => $this->record->id,
            'patient' => [
                'nik' => $this->record->pasien?->nik,
                'no_bpjs' => $this->record->pasien?->no_bpjs,
                'nama' => $this->record->pasien?->nama_pasien,
            ],
            'ttv' => [
                'sistole' => $this->tensi_sistole,
                'diastole' => $this->tensi_diastole,
                'pulse_rate' => $this->pulse_rate,
                'respiratory_rate' => $this->respiratory_rate,
                'temperature' => $this->temperature,
                'weight' => $this->weight,
                'height' => $this->height,
                'bmi' => $this->bmi,
            ],
            'soape' => [
                'subjective' => $this->subjective,
                'objective' => $this->objective,
                'assessment' => $this->assessment,
                'plan' => $this->plan,
            ],
            'diagnosis' => [
                'primary_code' => $primaryIcd10Code,
                'primary_name' => $primaryIcd10Name,
            ],
            'doctor' => [
                'id' => $this->record->dokter_id,
                'name' => $dokterNama,
            ],
            'referral_parameters' => [
                'spesialis_code' => $this->rujuk_spesialis,
                'subspesialis_code' => $this->rujuk_subspesialis ?: null,
                'sarana_code' => $this->rujuk_sarana,
                'tanggal_est' => $this->rujuk_tanggal_est,
                'ppk_kode' => $this->rujuk_ppk_kode,
                'is_tacc' => $this->rujuk_is_tacc,
                'tacc_jenis' => $this->rujuk_is_tacc ? $this->rujuk_tacc_jenis : null,
                'tacc_alasan' => $this->rujuk_is_tacc ? $this->rujuk_tacc_alasan : null,
            ],
        ];
    }
}
