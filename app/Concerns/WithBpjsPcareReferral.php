<?php

namespace App\Concerns;

use App\Models\MasterPcare;
use App\Models\MasterPetugas;
use App\Models\MasterSpesialisBpjs;
use App\Models\PatientReferral;
use App\Services\BpjsBridgeService;
use Flux\Flux;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait WithBpjsPcareReferral
{
    /**
     * Toggles visibility of the BPJS PCare Outward Referral form.
     */
    public bool $showPcareReferralForm = false;

    /**
     * BPJS Specialist Code.
     */
    public string $rujuk_spesialis = '';

    /**
     * BPJS Specialist Name.
     */
    public string $rujuk_spesialis_nama = '';

    /**
     * BPJS Subspecialist Code (Nullable).
     */
    public string $rujuk_subspesialis = '';

    /**
     * BPJS Facilities Code.
     */
    public string $rujuk_sarana = '';

    /**
     * Estimated referral date.
     */
    public string $rujuk_tanggal_est = '';

    /**
     * Code for target hospital (PPK Rujukan).
     */
    public string $rujuk_ppk_kode = '';

    /**
     * Name for target hospital.
     */
    public string $rujuk_ppk_nama = '';

    /**
     * Checkbox to toggle TACC (Time, Age, Comorbidity, Complication) constraints.
     */
    public bool $rujuk_is_tacc = false;

    /**
     * BPJS TACC type/category.
     */
    public string $rujuk_tacc_jenis = '';

    /**
     * Reasoning for TACC.
     */
    public string $rujuk_tacc_alasan = '';

    /**
     * Real-time fetched matrices.
     */
    public array $availableHospitals = [];

    public array $availableSaranas = [];

    public array $selected_secondary_icd10 = [];

    /**
     * Target Hospital input query for autocomplete search.
     */
    public string $faskesQuery = '';

    /**
     * Query autocomplete matching results.
     */
    public array $faskesResults = [];

    /**
     * Initialize BPJS PCare Outward Referral traits properties.
     */
    public function initializeWithBpjsPcareReferral(): void
    {
        $this->rujuk_tanggal_est = date('Y-m-d');
    }

    /**
     * Toggles the form visibility.
     */
    public function togglePcareReferral(): void
    {
        $this->showPcareReferralForm = ! $this->showPcareReferralForm;
        if ($this->showPcareReferralForm) {
            $this->checkIfNonSpesialistik();
            $this->loadDefaultSecondaryIcd10s();
        }
    }

    /**
     * Load default secondary ICD10s from selected diagnoses list.
     */
    public function loadDefaultSecondaryIcd10s(): void
    {
        $this->selected_secondary_icd10 = [];
        foreach ($this->selectedIcd10s ?? [] as $icd) {
            if (empty($icd['is_primary'])) {
                $this->selected_secondary_icd10[] = $icd['kode'];
            }
        }
    }

    /**
     * Check if a diagnosis falls under the "Non-Spesialistik" band.
     */
    public function isNonSpesialistik(string $code): bool
    {
        $code = strtoupper(trim($code));
        $cleanCode = str_replace('.', '', $code);

        $nonSpesialistikPrefixes = [
            'A09', 'J00', 'J01', 'J02', 'J03', 'J06', 'K29', 'L30', 'H10', 'M13',
            'I10', 'E11', 'A00', 'A01', 'A02', 'A03', 'A05', 'A06', 'A08', 'A15',
            'A16', 'B00', 'B01', 'B02', 'B05', 'B07', 'B08', 'B15', 'B26', 'B30',
            'B37', 'B50', 'B51', 'B52', 'B53', 'B54', 'B85', 'B86', 'D64', 'D50',
            'E10', 'E73', 'E86', 'F00', 'F01', 'F03', 'F05', 'F10', 'F20', 'F30',
            'F32', 'F40', 'F41', 'F43', 'F45', 'F48', 'G40', 'G43', 'G44', 'G47',
            'H00', 'H01', 'H02', 'H04', 'H11', 'H16', 'H52', 'H60', 'H61', 'H65',
            'H66', 'I11', 'I15', 'I95', 'J04', 'J05', 'J10', 'J11', 'J18', 'J20',
            'J21', 'J30', 'J40', 'J44', 'J45', 'K20', 'K21', 'K25', 'K30', 'K52',
            'K59', 'K60', 'K64', 'L20', 'L21', 'L22', 'L23', 'L24', 'L40', 'L50',
            'L70', 'L73', 'L80', 'M05', 'M06', 'M10', 'M15', 'M17', 'M19', 'M35',
            'M54', 'M79', 'N10', 'N12', 'N30', 'N34', 'N39', 'O00', 'R10', 'R11',
            'R50', 'R51', 'R52', 'R53', 'R56', 'S00', 'S01', 'S09', 'T14', 'Z00',
            'Z01', 'Z02', 'Z30', 'Z33', 'Z34', 'Z35',
        ];

        foreach ($nonSpesialistikPrefixes as $prefix) {
            if (str_starts_with($cleanCode, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check primary diagnosis and auto-flag TACC if non-spesialistik.
     */
    public function checkIfNonSpesialistik(): bool
    {
        $primaryIcd10 = collect($this->selectedIcd10s ?? [])
            ->first(fn ($icd) => ! empty($icd['is_primary']));

        if ($primaryIcd10 && $this->isNonSpesialistik($primaryIcd10['kode'])) {
            $this->rujuk_is_tacc = true;

            return true;
        }

        $this->rujuk_is_tacc = false;

        return false;
    }

    /**
     * Reactive cascade: updated rujuk_spesialis.
     */
    public function updatedRujukSpesialis($value): void
    {
        if (empty($this->faskesQuery)) {
            $this->rujuk_ppk_kode = '';
            $this->rujuk_ppk_nama = '';
        }
        $this->availableHospitals = [];
        $this->availableSaranas = [];

        if (empty($value)) {
            return;
        }

        $specialty = MasterSpesialisBpjs::where('kode_spesialis', $value)->first();
        $this->rujuk_spesialis_nama = $specialty ? $specialty->nama_spesialis : '';

        $this->fetchHospitalsFromApi();
    }

    /**
     * Reactive cascade: updated date estimate.
     */
    public function updatedRujukTanggalEst($value): void
    {
        if (! empty($this->rujuk_spesialis)) {
            $this->fetchHospitalsFromApi();
        }
    }

    /**
     * Reactive cascade: updated target hospital.
     */
    public function updatedRujukPpkKode($value): void
    {
        $this->availableSaranas = [];
        $this->rujuk_sarana = '';

        if (empty($value)) {
            $this->rujuk_ppk_nama = '';

            return;
        }

        $hospital = collect($this->availableHospitals)->firstWhere('kode_faskes', $value);
        $this->rujuk_ppk_nama = $hospital ? $hospital['nama_faskes'] : '';

        $this->fetchSaranasFromApi();
    }

    /**
     * Fetch Hospitals real-time from PCare BPJS API or mock fallback.
     */
    protected function fetchHospitalsFromApi(): void
    {
        $bridge = new BpjsBridgeService;
        $consId = $bridge->getConsId();
        $secretKey = $bridge->getSecretKey();
        $baseUrl = $bridge->getBaseUrl();

        $tglEst = date('d-m-Y', strtotime($this->rujuk_tanggal_est ?: date('Y-m-d')));

        if (empty($consId) || empty($secretKey)) {
            $this->availableHospitals = $this->getMockHospitals($this->rujuk_spesialis);

            return;
        }

        try {
            $headers = $bridge->getSecurityHeaders();
            $url = "{$baseUrl}/Rujukan/ListSpesialis/Faskes/kdSpesialis/{$this->rujuk_spesialis}/tglEstRujuk/{$tglEst}";
            $response = Http::withHeaders($headers)->timeout(5)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                $list = $json['response']['list'] ?? [];

                $this->availableHospitals = collect($list)->map(fn ($item) => [
                    'kode_faskes' => $item['faskes']['kdPPK'] ?? '',
                    'nama_faskes' => $item['faskes']['nmPPK'] ?? '',
                    'jarak' => ($item['jarak'] ?? '0').' km',
                    'kuota' => $item['persentase'] ?? 0,
                    'kapasitas' => $item['kapasitas'] ?? 100,
                ])->toArray();
            } else {
                Log::warning('BPJS API error in fetchHospitalsFromApi: '.$response->body());
                $this->availableHospitals = $this->getMockHospitals($this->rujuk_spesialis);
            }
        } catch (\Throwable $e) {
            Log::error('BPJS API Exception in fetchHospitalsFromApi: '.$e->getMessage());
            $this->availableHospitals = $this->getMockHospitals($this->rujuk_spesialis);
        }
    }

    /**
     * Fetch Facilities (Sarana) real-time from PCare BPJS API or mock fallback.
     */
    protected function fetchSaranasFromApi(): void
    {
        $bridge = new BpjsBridgeService;
        $consId = $bridge->getConsId();
        $secretKey = $bridge->getSecretKey();
        $baseUrl = $bridge->getBaseUrl();

        if (empty($consId) || empty($secretKey)) {
            $this->availableSaranas = $this->getMockSaranas();

            return;
        }

        try {
            $headers = $bridge->getSecurityHeaders();
            $url = "{$baseUrl}/Rujukan/ListSarana/Faskes/kdSarana/{$this->rujuk_ppk_kode}";
            $response = Http::withHeaders($headers)->timeout(5)->get($url);

            if ($response->successful()) {
                $json = $response->json();
                $list = $json['response']['list'] ?? [];

                $this->availableSaranas = collect($list)->map(fn ($item) => [
                    'kode_sarana' => $item['kdSarana'] ?? '',
                    'nama_sarana' => $item['nmSarana'] ?? '',
                ])->toArray();
            } else {
                Log::warning('BPJS API error in fetchSaranasFromApi: '.$response->body());
                $this->availableSaranas = $this->getMockSaranas();
            }
        } catch (\Throwable $e) {
            Log::error('BPJS API Exception in fetchSaranasFromApi: '.$e->getMessage());
            $this->availableSaranas = $this->getMockSaranas();
        }
    }

    /**
     * Mock hospitals for testing/UAT.
     */
    protected function getMockHospitals(string $spesialisCode): array
    {
        return [
            [
                'kode_faskes' => '0112R001',
                'nama_faskes' => 'RSUD Kota Bandung',
                'jarak' => '1.2 km',
                'kuota' => 15,
                'kapasitas' => 20,
            ],
            [
                'kode_faskes' => '0112R002',
                'nama_faskes' => 'RSUP Dr. Hasan Sadikin',
                'jarak' => '3.5 km',
                'kuota' => 8,
                'kapasitas' => 10,
            ],
            [
                'kode_faskes' => '0112R003',
                'nama_faskes' => 'RS Immanuel Bandung',
                'jarak' => '4.8 km',
                'kuota' => 12,
                'kapasitas' => 15,
            ],
            [
                'kode_faskes' => '0112R004',
                'nama_faskes' => 'RS Advent Bandung',
                'jarak' => '5.1 km',
                'kuota' => 5,
                'kapasitas' => 10,
            ],
        ];
    }

    /**
     * Mock facilities for testing/UAT.
     */
    protected function getMockSaranas(): array
    {
        return [
            ['kode_sarana' => 'SAR-01', 'nama_sarana' => 'Poli Jantung'],
            ['kode_sarana' => 'SAR-02', 'nama_sarana' => 'Poli Bedah'],
            ['kode_sarana' => 'SAR-03', 'nama_sarana' => 'Poli Penyakit Dalam'],
            ['kode_sarana' => 'SAR-04', 'nama_sarana' => 'Poli Gigi Spesialistik'],
        ];
    }

    /**
     * Submit outward referral to PCare API and log transaction in DB.
     */
    public function saveAndSubmitRujukan()
    {
        $primaryIcd10 = collect($this->selectedIcd10s ?? [])
            ->first(fn ($icd) => ! empty($icd['is_primary']));

        $primaryIcd10Code = $primaryIcd10 ? $primaryIcd10['kode'] : '';
        $primaryIcd10Name = $primaryIcd10 ? $primaryIcd10['nama_penyakit'] : '';
        $isNonSpesialistik = $primaryIcd10 ? $this->isNonSpesialistik($primaryIcd10Code) : false;

        $validationRules = [
            'rujuk_spesialis' => 'required|string',
            'rujuk_ppk_kode' => 'required|string',
            'rujuk_tanggal_est' => 'required|date|after_or_equal:today',
        ];

        if ($isNonSpesialistik) {
            $validationRules['rujuk_tacc_jenis'] = 'required|string|in:1,2,3,4,Time,Age,Comorbidity,Complication';
            $validationRules['rujuk_tacc_alasan'] = 'required|string|min:5';
        }

        $this->validate($validationRules);

        $this->loadDefaultSecondaryIcd10s();
        $secondaryIcd10s = $this->selected_secondary_icd10;

        $bridge = new BpjsBridgeService;
        $consId = $bridge->getConsId();
        $secretKey = $bridge->getSecretKey();
        $baseUrl = $bridge->getBaseUrl();

        $payload = [
            'noKunjungan' => 'KUNJ-'.$this->record->id,
            'tglRujukan' => date('d-m-Y'),
            'ppkDirujuk' => $this->rujuk_ppk_kode,
            'jnsPoli' => $this->rujuk_spesialis,
            'tglEstRujukan' => date('d-m-Y', strtotime($this->rujuk_tanggal_est)),
            'catatan' => $this->rujuk_tacc_alasan ?: 'Rujukan Keluar Rawat Jalan',
            'diagRujukan' => $primaryIcd10Code,
            'diagSekunder' => $secondaryIcd10s,
            'isTacc' => $isNonSpesialistik ? 1 : 0,
            'tacc' => $isNonSpesialistik ? [
                'taccJenis' => $this->rujuk_tacc_jenis,
                'taccAlasan' => $this->rujuk_tacc_alasan,
            ] : null,
        ];

        $noRujukan = '';
        $apiResponse = [];

        if (empty($consId) || empty($secretKey)) {
            $noRujukan = '1014R001'.date('Ymd').str_pad((string) rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $apiResponse = [
                'metaData' => ['code' => 201, 'message' => 'Created'],
                'response' => ['noRujukan' => $noRujukan],
            ];
        } else {
            try {
                $headers = $bridge->getSecurityHeaders();
                $url = "{$baseUrl}/Rujukan/insert";
                $response = Http::withHeaders($headers)->timeout(5)->post($url, $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    $noRujukan = $json['response']['noRujukan'] ?? '';
                    $apiResponse = $json;
                } else {
                    $errorMsg = $response->json()['metaData']['message'] ?? $response->body();
                    $this->addError('rujuk_spesialis', 'BPJS Bridge Error: '.$errorMsg);

                    return;
                }
            } catch (\Throwable $e) {
                $this->addError('rujuk_spesialis', 'BPJS Connection Error: '.$e->getMessage());

                return;
            }
        }

        if (empty($noRujukan)) {
            $this->addError('rujuk_spesialis', 'Nomor Rujukan BPJS kosong/tidak valid.');

            return;
        }

        DB::transaction(function () use ($noRujukan, $primaryIcd10Code, $primaryIcd10Name, $secondaryIcd10s, $apiResponse) {
            PatientReferral::create([
                'medical_record_id' => $this->record->id,
                'no_rujukan' => $noRujukan,
                'ppk_dirujuk_kode' => $this->rujuk_ppk_kode,
                'ppk_dirujuk_nama' => $this->rujuk_ppk_nama ?: 'Faskes Tujuan Rujukan',
                'spesialis_kode' => $this->rujuk_spesialis,
                'spesialis_nama' => $this->rujuk_spesialis_nama ?: 'Spesialis Rujukan',
                'subspesialis_kode' => $this->rujuk_subspesialis ?: null,
                'sarana_kode' => $this->rujuk_sarana ?: null,
                'sarana_nama' => $this->getSaranaName($this->rujuk_sarana),
                'tgl_est_rujukan' => $this->rujuk_tanggal_est,
                'is_tacc' => $this->rujuk_is_tacc,
                'tacc_jenis' => $this->rujuk_is_tacc ? $this->rujuk_tacc_jenis : null,
                'tacc_alasan' => $this->rujuk_is_tacc ? $this->rujuk_tacc_alasan : null,
                'diagnosa_utama_kode' => $primaryIcd10Code,
                'diagnosa_utama_nama' => $primaryIcd10Name,
                'diagnosa_sekunder' => $secondaryIcd10s,
                'response_json' => $apiResponse,
            ]);
        });

        $this->showPcareReferralForm = false;
        $this->resetPcareForm();

        if (method_exists($this, 'dispatch')) {
            $this->dispatch('refresh-page');
        }

        Flux::toast(variant: 'success', text: 'Rujukan Keluar BPJS berhasil dibuat dan disubmit!');
    }

    /**
     * Retrieve name of selected facility (Sarana).
     */
    protected function getSaranaName(?string $kode): ?string
    {
        if (empty($kode)) {
            return null;
        }
        $sarana = collect($this->availableSaranas)->firstWhere('kode_sarana', $kode);

        return $sarana ? $sarana['nama_sarana'] : $kode;
    }

    /**
     * Reset form states.
     */
    protected function resetPcareForm(): void
    {
        $this->rujuk_spesialis = '';
        $this->rujuk_spesialis_nama = '';
        $this->rujuk_subspesialis = '';
        $this->rujuk_sarana = '';
        $this->rujuk_ppk_kode = '';
        $this->rujuk_ppk_nama = '';
        $this->rujuk_is_tacc = false;
        $this->rujuk_tacc_jenis = '';
        $this->rujuk_tacc_alasan = '';
        $this->availableHospitals = [];
        $this->availableSaranas = [];
    }

    /**
     * Autocomplete search input query handling (Legacy support for test suite).
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
     * Select a hospital from search results (Legacy support for test suite).
     */
    public function selectFaskes(string $kode, string $nama): void
    {
        $this->rujuk_ppk_kode = $kode;
        $this->rujuk_ppk_nama = $nama;
        $this->faskesQuery = $nama.' ('.$kode.')';
        $this->faskesResults = [];
        $this->fetchSaranasFromApi();
    }

    /**
     * Generate the BPJS PCare Outward Referral API Payload (Legacy support for test suite).
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
