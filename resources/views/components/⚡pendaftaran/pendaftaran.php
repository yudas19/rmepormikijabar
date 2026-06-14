<?php

use App\Models\MasterPekerjaan;
use App\Models\MasterPetugas;
use App\Models\MedicalRecord;
use App\Models\Pasien;
use App\Models\Pendaftaran;
use App\Models\Poli;
use App\Models\SuratKeterangan;
use App\Models\SuratPersetujuan;
use App\Models\SuratRujukan;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'nama_pasien';

    public $sortDirection = 'asc';

    // State Modals
    public $showPatientModal = false;

    public $showRegisterModal = false;

    public $showConsentModal = false;

    public $showReferralModal = false;

    public $showCertificateModal = false;

    // Active Patient Selection
    public $selectedPasienId = null;

    public $activePendaftaranId = null;

    public $filterDate = '';

    public $reg_tanggal_kunjungan = '';

    public $cancelId = null;

    public $showCancelConfirmation = false;

    public function mount()
    {
        $this->filterDate = date('Y-m-d');
        $this->reg_tanggal_kunjungan = date('Y-m-d');
    }

    // --- FORM PATIENT FIELDS ---
    public $pasien_id = null;

    public $no_rekam_medis = '';

    public $nama_pasien = '';

    public $panggilan = '';

    public $nik = '';

    public $no_bpjs = '';

    public $ihs_number = '';

    public $gelar = '';

    public $tempat_lahir = '';

    public $tanggal_lahir = '';

    public $jenis_kelamin = '';

    public $golongan_darah = 'Tidak Tahu';

    public $nama_orangtua = '';

    public $nrp = '';

    public $keluarga_anggota = 'tidak';

    public $hubungan_keluarga = '';

    public $status_perkawinan = '';

    public $suku = '';

    public $agama = '';

    public $pendidikan = '';

    public $pekerjaan = '';

    public $kewarganegaraan = 'WNI';

    public $bahasa = 'Indonesia';

    public $no_whatsapp = '';

    public $email = '';

    public $alamat = '';

    public $status_pasien = 'aktif';

    // SatuSehat verification status
    public $isIhsSynced = true;

    public $showIhsWarning = false;

    // --- FORM REGISTER OUTPATIENT FIELDS ---
    public $reg_poli_id = '';

    public $reg_dokter_id = '';

    public $reg_cara_bayar = 'Umum';

    public $reg_no_sep = '';

    public $reg_no_rujukan = '';

    public $reg_jenis_kunjungan = 'Baru';

    public $reg_keluhan_awal = '';

    // --- FORM CONSENT FIELDS ---
    public $consent_type = 'general_consent'; // or 'informed_consent_tindakan'

    public $consent_nama_penanggung_jawab = '';

    public $consent_hubungan_penanggung_jawab = 'diri_sendiri';

    public $consent_nik_penanggung_jawab = '';

    public $consent_nama_tindakan_medis = '';

    public $consent_pernyataan = 'setuju';

    public $consent_petugas_id = '';

    // --- FORM REFERRAL FIELDS ---
    public $ref_faskes_tujuan = '';

    public $ref_diagnosa = '';

    public $ref_catatan = '';

    public $ref_dokter_id = '';

    // --- FORM CERTIFICATE FIELDS ---
    public $cert_type = 'sehat'; // sehat, sakit, bebas_narkoba

    // Sakit
    public $cert_sakit_tanggal_mulai = '';

    public $cert_sakit_tanggal_selesai = '';

    public $cert_sakit_diagnosa = '';

    // Sehat
    public $cert_sehat_tinggi = '';

    public $cert_sehat_berat = '';

    public $cert_sehat_tensi = '';

    public $cert_sehat_butawarna = 'tidak';

    public $cert_sehat_catatan = '';

    // Narkoba
    public $cert_narkoba_keperluan = '';

    public $cert_narkoba_hasil = 'Negatif untuk seluruh parameter uji (Amphetamine, THC, Morphine)';

    public $cert_dokter_id = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // MOCK SatuSehat Verification Lookups
    public function verifyNik()
    {
        $this->validate([
            'nik' => 'required|string|size:16',
        ]);

        $mockLookups = [
            '1234567890123456' => [
                'nama' => 'Budi Santoso',
                'ihs' => 'IHS-2026110022',
                'dob' => '1980-01-01',
                'gender' => 'L',
                'tempat_lahir' => 'Bandung',
            ],
            '9876543210987654' => [
                'nama' => 'Siti Rahma',
                'ihs' => 'IHS-2026110033',
                'dob' => '1992-05-12',
                'gender' => 'P',
                'tempat_lahir' => 'Jakarta',
            ],
        ];

        if (array_key_exists($this->nik, $mockLookups)) {
            $data = $mockLookups[$this->nik];
            $this->nama_pasien = $data['nama'];
            $this->ihs_number = $data['ihs'];
            $this->tanggal_lahir = $data['dob'];
            $this->jenis_kelamin = $data['gender'];
            $this->tempat_lahir = $data['tempat_lahir'];
            $this->isIhsSynced = true;
            $this->showIhsWarning = false;
            Flux::toast(variant: 'success', text: 'NIK terverifikasi di SatuSehat Kemenkes!');
        } else {
            $this->ihs_number = '';
            $this->isIhsSynced = false;
            $this->showIhsWarning = true;
            Flux::toast(variant: 'warning', text: 'IHS Not Synced: NIK tidak ditemukan di SatuSehat.');
        }
    }

    public function openAddPatient()
    {
        $this->resetForm();
        // Auto-generate local rekam medis code
        $this->no_rekam_medis = 'RM-'.date('Ymd').'-'.sprintf('%04d', rand(1, 9999));
        $this->showPatientModal = true;
    }

    public function editPatient($id)
    {
        $this->resetForm();
        $record = Pasien::findOrFail($id);
        $this->pasien_id = $record->id;
        $this->no_rekam_medis = $record->no_rekam_medis;
        $this->nama_pasien = $record->nama_pasien;
        $this->panggilan = $record->panggilan;
        $this->nik = $record->nik;
        $this->no_bpjs = $record->no_bpjs;
        $this->ihs_number = $record->ihs_number;
        $this->gelar = $record->gelar;
        $this->tempat_lahir = $record->tempat_lahir;
        $this->tanggal_lahir = $record->tanggal_lahir ? $record->tanggal_lahir->format('Y-m-d') : '';
        $this->jenis_kelamin = $record->jenis_kelamin;
        $this->golongan_darah = $record->golongan_darah;
        $this->nama_orangtua = $record->nama_orangtua;
        $this->nrp = $record->nrp;
        $this->keluarga_anggota = $record->keluarga_anggota;
        $this->hubungan_keluarga = $record->hubungan_keluarga;
        $this->status_perkawinan = $record->status_perkawinan;
        $this->suku = $record->suku;
        $this->agama = $record->agama;
        $this->pendidikan = $record->pendidikan;
        $this->pekerjaan = $record->pekerjaan;
        $this->kewarganegaraan = $record->kewarganegaraan;
        $this->bahasa = $record->bahasa;
        $this->no_whatsapp = $record->no_whatsapp;
        $this->email = $record->email;
        $this->alamat = $record->alamat;
        $this->status_pasien = $record->status_pasien;

        $this->isIhsSynced = ! empty($this->ihs_number);
        $this->showIhsWarning = ! $this->isIhsSynced;
        $this->showPatientModal = true;
    }

    public function savePatient()
    {
        $rules = [
            'no_rekam_medis' => 'required|string|max:20|unique:pasiens,no_rekam_medis,'.($this->pasien_id ?? 'NULL').',id',
            'nama_pasien' => 'required|string|max:100',
            'panggilan' => 'nullable|string|max:50',
            'nik' => 'required|string|size:16|unique:pasiens,nik,'.($this->pasien_id ?? 'NULL').',id',
            'no_bpjs' => 'nullable|string|size:13|unique:pasiens,no_bpjs,'.($this->pasien_id ?? 'NULL').',id',
            'ihs_number' => 'nullable|string|unique:pasiens,ihs_number,'.($this->pasien_id ?? 'NULL').',id',
            'gelar' => 'nullable|string|max:20',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah' => 'required|in:A,B,AB,O,Tidak Tahu',
            'alamat' => 'required|string',
            'no_whatsapp' => 'nullable|string|max:15',
            'email' => 'nullable|email|max:100',
            'status_pasien' => 'required|in:aktif,nonaktif,meninggal',
        ];

        $validated = $this->validate($rules);
        $validated['created_by'] = auth()->id();

        if ($this->pasien_id) {
            $record = Pasien::findOrFail($this->pasien_id);
            $record->update($validated);
            Flux::toast(variant: 'success', text: 'Data pasien berhasil diperbarui.');
        } else {
            Pasien::create($validated);
            Flux::toast(variant: 'success', text: 'Pasien baru berhasil didaftarkan.');
        }

        $this->showPatientModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->pasien_id = null;
        $this->no_rekam_medis = '';
        $this->nama_pasien = '';
        $this->panggilan = '';
        $this->nik = '';
        $this->no_bpjs = '';
        $this->ihs_number = '';
        $this->gelar = '';
        $this->tempat_lahir = '';
        $this->tanggal_lahir = '';
        $this->jenis_kelamin = '';
        $this->golongan_darah = 'Tidak Tahu';
        $this->nama_orangtua = '';
        $this->nrp = '';
        $this->keluarga_anggota = 'tidak';
        $this->hubungan_keluarga = '';
        $this->status_perkawinan = '';
        $this->suku = '';
        $this->agama = '';
        $this->pendidikan = '';
        $this->pekerjaan = '';
        $this->kewarganegaraan = 'WNI';
        $this->bahasa = 'Indonesia';
        $this->no_whatsapp = '';
        $this->email = '';
        $this->alamat = '';
        $this->status_pasien = 'aktif';

        $this->isIhsSynced = true;
        $this->showIhsWarning = false;
        $this->resetErrorBag();
    }

    // --- OUTPATIENT REGISTRATION FLOW ---
    public function openRegisterOutpatient($pasienId)
    {
        $this->selectedPasienId = $pasienId;
        $this->reg_poli_id = '';
        $this->reg_dokter_id = '';
        $this->reg_cara_bayar = 'Umum';
        $this->reg_no_sep = '';
        $this->reg_no_rujukan = '';
        $this->reg_jenis_kunjungan = 'Baru';
        $this->reg_keluhan_awal = '';
        $this->reg_tanggal_kunjungan = date('Y-m-d');
        $this->showRegisterModal = true;
    }

    public function saveOutpatientRegistration()
    {
        $rules = [
            'reg_poli_id' => 'required|exists:master_polis,id',
            'reg_dokter_id' => 'required|exists:master_petugass,id',
            'reg_cara_bayar' => 'required|in:Umum,BPJS,Dinas/Instansi',
            'reg_no_sep' => 'required_if:reg_cara_bayar,BPJS|nullable|string|max:30',
            'reg_no_rujukan' => 'required_if:reg_cara_bayar,BPJS|nullable|string|max:30',
            'reg_jenis_kunjungan' => 'required|in:Baru,Lama,Kontrol',
            'reg_keluhan_awal' => 'required|string',
            'reg_tanggal_kunjungan' => 'required|date',
        ];

        $this->validate($rules);

        $poli = Poli::findOrFail($this->reg_poli_id);

        // Map Poliklinik target to standard types & prefixes
        $poliklinik_type = 'umum';
        $prefix = 'A';
        if (stripos($poli->nama_poli, 'gigi') !== false) {
            $poliklinik_type = 'gigi';
            $prefix = 'B';
        } elseif (stripos($poli->nama_poli, 'kia') !== false || stripos($poli->nama_poli, 'anak') !== false || stripos($poli->nama_poli, 'ibu') !== false) {
            $poliklinik_type = 'kia';
            $prefix = 'C';
        }

        // Daily resetting queue calculation for medical_records based on specific visit date
        $visitDate = $this->reg_tanggal_kunjungan;
        $mrCountToday = MedicalRecord::where('poli_id', $this->reg_poli_id)
            ->whereDate('tanggal_kunjungan', $visitDate)
            ->count();
        $mrSeq = $mrCountToday + 1;
        $nomor_antrean = $prefix.'-'.sprintf('%02d', $mrSeq);

        $no_registrasi = 'REG-'.date('Ymd').'-'.sprintf('%04d', rand(1, 9999));

        $pendaftaran = Pendaftaran::create([
            'no_registrasi' => $no_registrasi,
            'pasien_id' => $this->selectedPasienId,
            'poli_id' => $this->reg_poli_id,
            'dokter_id' => $this->reg_dokter_id,
            'no_antrean' => $nomor_antrean,
            'angka_antrean' => $mrSeq,
            'status_antrean' => 'menunggu',
            'cara_bayar' => $this->reg_cara_bayar,
            'no_sep' => $this->reg_no_sep ?: null,
            'no_rujukan' => $this->reg_no_rujukan ?: null,
            'jenis_kunjungan' => $this->reg_jenis_kunjungan,
            'keluhan_awal' => $this->reg_keluhan_awal,
            'created_by' => auth()->id(),
            'created_at' => Carbon::parse($visitDate.' '.date('H:i:s')),
        ]);

        // Create MedicalRecord representing the Clinical Workspace encounter
        $encounter_id = 'ENC-'.date('Ymd').'-'.sprintf('%04d', rand(1, 9999));
        $medicalRecord = MedicalRecord::create([
            'encounter_id' => $encounter_id,
            'patient_id' => $this->selectedPasienId,
            'pendaftaran_id' => $pendaftaran->id,
            'poli_id' => $this->reg_poli_id,
            'status' => 'waiting',
            'nomor_antrean' => $nomor_antrean,
            'dokter_id' => $this->reg_dokter_id,
            'keluhan_utama' => $this->reg_keluhan_awal,
            'created_by' => auth()->id(),
            'tanggal_kunjungan' => $visitDate,
            'created_at' => Carbon::parse($visitDate.' '.date('H:i:s')),
        ]);

        $this->showRegisterModal = false;
        Flux::toast(variant: 'success', text: 'Kunjungan rawat jalan berhasil didaftarkan! No Antrean: '.$nomor_antrean);

        // Trigger direct print layout stream in a new tab
        $this->dispatch('open-print-tab', ['url' => route('print.queue-ticket', ['id' => $medicalRecord->id])]);
    }

    // --- CONSENT & LETTERS ACTIONS ---
    public function selectPendaftaran($pasienId)
    {
        $latest = Pendaftaran::where('pasien_id', $pasienId)->latest()->first();
        if (! $latest) {
            Flux::toast(variant: 'danger', text: 'Pasien belum memiliki registrasi kunjungan. Daftarkan kunjungan terlebih dahulu!');

            return false;
        }
        $this->selectedPasienId = $pasienId;
        $this->activePendaftaranId = $latest->id;

        return $latest;
    }

    public function openConsentModal($pasienId, $type)
    {
        $pendaftaran = $this->selectPendaftaran($pasienId);
        if (! $pendaftaran) {
            return;
        }

        $pasien = Pasien::findOrFail($pasienId);

        $this->consent_type = $type;
        $this->consent_nama_penanggung_jawab = $pasien->nama_pasien;
        $this->consent_hubungan_penanggung_jawab = 'diri_sendiri';
        $this->consent_nik_penanggung_jawab = $pasien->nik;
        $this->consent_nama_tindakan_medis = '';
        $this->consent_pernyataan = 'setuju';
        $this->consent_petugas_id = '';
        $this->showConsentModal = true;
    }

    public function saveConsent()
    {
        $rules = [
            'consent_nama_penanggung_jawab' => 'required|string|max:100',
            'consent_hubungan_penanggung_jawab' => 'required|string',
            'consent_nik_penanggung_jawab' => 'nullable|string|size:16',
            'consent_nama_tindakan_medis' => 'required_if:consent_type,informed_consent_tindakan|nullable|string|max:100',
            'consent_pernyataan' => 'required|in:setuju,menolak',
            'consent_petugas_id' => 'required|exists:master_petugass,id',
        ];

        $this->validate($rules);

        $no_surat = 'CNT/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratPersetujuan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->activePendaftaranId,
            'jenis_persetujuan' => $this->consent_type,
            'nama_penanggung_jawab' => $this->consent_nama_penanggung_jawab,
            'hubungan_penanggung_jawab' => $this->consent_hubungan_penanggung_jawab,
            'nik_penanggung_jawab' => $this->consent_nik_penanggung_jawab ?: null,
            'nama_tindakan_medis' => $this->consent_nama_tindakan_medis ?: null,
            'pernyataan' => $this->consent_pernyataan,
            'petugas_id' => $this->consent_petugas_id,
        ]);

        $this->showConsentModal = false;
        Flux::toast(variant: 'success', text: 'Surat persetujuan berhasil dibuat! Membuka dokumen cetak...');

        // Stream PDF to browser in new tab
        $this->dispatch('open-print-tab', ['url' => route('print.consent', ['id' => $saved->id])]);
    }

    public function openReferralModal($pasienId)
    {
        $pendaftaran = $this->selectPendaftaran($pasienId);
        if (! $pendaftaran) {
            return;
        }

        $this->ref_faskes_tujuan = '';
        $this->ref_diagnosa = '';
        $this->ref_catatan = '';
        $this->ref_dokter_id = $pendaftaran->dokter_id; // Default to the registered doctor
        $this->showReferralModal = true;
    }

    public function saveReferral()
    {
        $rules = [
            'ref_faskes_tujuan' => 'required|string|max:100',
            'ref_diagnosa' => 'required|string|max:100',
            'ref_catatan' => 'nullable|string',
            'ref_dokter_id' => 'required|exists:master_petugass,id',
        ];

        $this->validate($rules);

        $no_surat = 'RUJ/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratRujukan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->activePendaftaranId,
            'pasien_id' => $this->selectedPasienId,
            'dokter_id' => $this->ref_dokter_id,
            'faskes_tujuan' => $this->ref_faskes_tujuan,
            'diagnosa' => $this->ref_diagnosa,
            'catatan' => $this->ref_catatan ?: null,
            'tanggal_rujukan' => date('Y-m-d'),
        ]);

        $this->showReferralModal = false;
        Flux::toast(variant: 'success', text: 'Surat Rujukan berhasil dibuat! Membuka dokumen cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.referral', ['id' => $saved->id])]);
    }

    public function openCertificateModal($pasienId, $type)
    {
        $pendaftaran = $this->selectPendaftaran($pasienId);
        if (! $pendaftaran) {
            return;
        }

        $this->cert_type = $type;

        // Reset inputs
        $this->cert_sakit_tanggal_mulai = date('Y-m-d');
        $this->cert_sakit_tanggal_selesai = date('Y-m-d');
        $this->cert_sakit_diagnosa = '';

        $this->cert_sehat_tinggi = '';
        $this->cert_sehat_berat = '';
        $this->cert_sehat_tensi = '120/80';
        $this->cert_sehat_butawarna = 'tidak';
        $this->cert_sehat_catatan = '';

        $this->cert_narkoba_keperluan = '';
        $this->cert_narkoba_hasil = 'Negatif untuk seluruh parameter uji (Amphetamine, THC, Morphine)';

        $this->cert_dokter_id = $pendaftaran->dokter_id;
        $this->showCertificateModal = true;
    }

    public function saveCertificate()
    {
        if ($this->cert_type === 'sehat') {
            $rules = [
                'cert_sehat_tinggi' => 'required|numeric',
                'cert_sehat_berat' => 'required|numeric',
                'cert_sehat_tensi' => 'required|string|max:15',
                'cert_sehat_butawarna' => 'required|in:ya,tidak',
                'cert_sehat_catatan' => 'nullable|string',
                'cert_dokter_id' => 'required|exists:master_petugass,id',
            ];
            $this->validate($rules);
            $content = [
                'tinggi_badan' => $this->cert_sehat_tinggi,
                'berat_badan' => $this->cert_sehat_berat,
                'tekanan_darah' => $this->cert_sehat_tensi,
                'buta_warna' => $this->cert_sehat_butawarna,
                'catatan' => $this->cert_sehat_catatan,
            ];
        } elseif ($this->cert_type === 'sakit') {
            $rules = [
                'cert_sakit_tanggal_mulai' => 'required|date',
                'cert_sakit_tanggal_selesai' => 'required|date|after_or_equal:cert_sakit_tanggal_mulai',
                'cert_sakit_diagnosa' => 'nullable|string',
                'cert_dokter_id' => 'required|exists:master_petugass,id',
            ];
            $this->validate($rules);
            $content = [
                'tanggal_mulai' => $this->cert_sakit_tanggal_mulai,
                'tanggal_selesai' => $this->cert_sakit_tanggal_selesai,
                'diagnosa' => $this->cert_sakit_diagnosa,
            ];
        } else {
            $rules = [
                'cert_narkoba_keperluan' => 'required|string|max:100',
                'cert_narkoba_hasil' => 'required|string',
                'cert_dokter_id' => 'required|exists:master_petugass,id',
            ];
            $this->validate($rules);
            $content = [
                'keperluan' => $this->cert_narkoba_keperluan,
                'hasil_tes' => $this->cert_narkoba_hasil,
            ];
        }

        $no_surat = 'SKD/'.strtoupper($this->cert_type).'/'.date('Ymd').'/'.sprintf('%04d', rand(1, 9999));

        $saved = SuratKeterangan::create([
            'no_surat' => $no_surat,
            'pendaftaran_id' => $this->activePendaftaranId,
            'pasien_id' => $this->selectedPasienId,
            'dokter_id' => $this->cert_dokter_id,
            'jenis_surat' => $this->cert_type,
            'konten_surat' => $content,
        ]);

        $this->showCertificateModal = false;
        Flux::toast(variant: 'success', text: 'Surat Keterangan berhasil dibuat! Membuka dokumen cetak...');

        $this->dispatch('open-print-tab', ['url' => route('print.certificate', ['id' => $saved->id])]);
    }

    public function reprintTicket($id)
    {
        $this->dispatch('open-print-tab', ['url' => route('print.queue-ticket', ['id' => $id])]);
    }

    public function confirmCancel($id)
    {
        $this->cancelId = $id;
        $this->showCancelConfirmation = true;
    }

    public function cancelPendaftaran()
    {
        if ($this->cancelId) {
            $record = MedicalRecord::findOrFail($this->cancelId);
            $record->update(['status' => 'batal']);
            if ($record->pendaftaran) {
                $record->pendaftaran->update(['status_antrean' => 'batal']);
            }
            Flux::toast(variant: 'success', text: 'Registrasi antrean berhasil dibatalkan.');
        }
        $this->cancelId = null;
        $this->showCancelConfirmation = false;
    }

    public function render()
    {
        $data = Pasien::query()
            ->when($this->search, function ($query) {
                $query->where('nama_pasien', 'like', '%'.$this->search.'%')
                    ->orWhere('no_rekam_medis', 'like', '%'.$this->search.'%')
                    ->orWhere('nik', 'like', '%'.$this->search.'%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        $todayQueues = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter', 'pendaftaran.poli'])
            ->whereDate('tanggal_kunjungan', $this->filterDate)
            ->where('status', '!=', 'batal')
            ->orderBy('id', 'desc')
            ->get();

        return view('components.⚡pendaftaran.pendaftaran', [
            'pasiens' => $data,
            'polis' => Poli::where('is_active', true)->get(),
            'doctors' => MasterPetugas::where('jenis_petugas', 'Dokter')->where('is_aktif', true)->get(),
            'petugass' => MasterPetugas::where('is_aktif', true)->get(),
            'pekerjaans' => MasterPekerjaan::all(),
            'todayQueues' => $todayQueues,
        ]);
    }
};
