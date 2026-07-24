<?php

use App\Models\Booking;
use App\Models\MasterAgama;
use App\Models\MasterPekerjaan;
use App\Models\MasterPendidikan;
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

    public float $adminFee = 15000.00;

    public $search = '';

    public $sortField = 'nama_pasien';

    public $sortDirection = 'asc';

    // State Modals
    public $showPatientModal = false;

    public $showRegisterModal = false;

    public $showConsentModal = false;

    public $showReferralModal = false;

    public $showCertificateModal = false;

    public $showSuccessPrintModal = false;

    public $successPrintUrl = '';

    public $successPrintMessage = '';

    // Active Patient Selection
    public $selectedPasienId = null;

    public $activePendaftaranId = null;

    public $filterStartDate = '';

    public $filterEndDate = '';

    public $reg_tanggal_kunjungan = '';

    public $cancelId = null;

    public $showCancelConfirmation = false;

    // --- BOOKING FIELDS ---
    public $showBookingModal = false;

    public $bookingDate = '';

    public $showBookingList = false;

    public function mount()
    {
        $this->filterStartDate = date('Y-m-d');
        $this->filterEndDate = date('Y-m-d');
        $this->reg_tanggal_kunjungan = date('Y-m-d');
    }

    public function updatedTempatLahirQuery()
    {
        if (strlen($this->tempatLahirQuery) < 2) {
            $this->tempatLahirResults = [];
            return;
        }

        $this->tempatLahirResults = \App\Models\KabupatenKota::where('nama_kabupaten_kota', 'like', '%'.$this->tempatLahirQuery.'%')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function selectTempatLahir($id, $nama)
    {
        $this->tempat_lahir_kabupaten_id = $id;
        $this->tempatLahirQuery = $nama;
        $this->tempatLahirResults = [];
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

    public $tempatLahirQuery = '';

    public $tempatLahirResults = [];

    public $tempat_lahir_kabupaten_id = null;

    public $master_agama_id = null;

    public $master_pendidikan_id = null;

    public $master_pekerjaan_id = null;

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

    public $selectedLabTests = [];

    public function updatedRegPoliId()
    {
        $this->reg_dokter_id = '';
        $this->selectedLabTests = [];
    }

    public function getIsWalkInLabProperty()
    {
        if (! $this->reg_poli_id) {
            return false;
        }
        $poli = Poli::find($this->reg_poli_id);
        return $poli && $poli->jenis_unit === 'penunjang' && stripos($poli->nama_poli, 'laboratorium') !== false;
    }

    public $reg_cara_bayar = 'Umum';

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
        // Auto-generate 6-digit sequential local rekam medis code
        $lastPasien = Pasien::where('no_rekam_medis', '>=', '000001')
            ->where('no_rekam_medis', '<=', '999999')
            ->orderBy('no_rekam_medis', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastPasien) {
            $nextNumber = intval($lastPasien->no_rekam_medis) + 1;
        }

        $this->no_rekam_medis = sprintf('%06d', $nextNumber);
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
        $this->tempat_lahir_kabupaten_id = $record->tempat_lahir_kabupaten_id;
        $this->tempatLahirQuery = $record->tempatLahirKabupaten?->nama_kabupaten_kota ?? '';
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
        $this->master_agama_id = $record->master_agama_id;
        $this->pendidikan = $record->pendidikan;
        $this->master_pendidikan_id = $record->master_pendidikan_id;
        $this->pekerjaan = $record->pekerjaan;
        $this->master_pekerjaan_id = $record->master_pekerjaan_id;
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
            'tempat_lahir_kabupaten_id' => 'required|exists:master_kabupaten_kotas,id',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'golongan_darah' => 'required|in:A,B,AB,O,Tidak Tahu',
            'master_agama_id' => 'nullable|exists:master_agamas,id',
            'master_pendidikan_id' => 'nullable|exists:master_pendidikans,id',
            'master_pekerjaan_id' => 'nullable|exists:master_pekerjaans,id',
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
        $this->tempat_lahir_kabupaten_id = null;
        $this->tempatLahirQuery = '';
        $this->tempatLahirResults = [];
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
        $this->master_agama_id = null;
        $this->pendidikan = '';
        $this->master_pendidikan_id = null;
        $this->pekerjaan = '';
        $this->master_pekerjaan_id = null;
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

    // --- QUEUE CALLING ---
    public function panggilAntrean(int $id): void
    {
        $record = MedicalRecord::findOrFail($id);
        $record->update(['status_panggilan' => 'memanggil']);
        Flux::toast(variant: 'success', text: 'Memanggil nomor antrean '.$record->nomor_antrean.'.');
    }

    // --- OUTPATIENT REGISTRATION FLOW ---
    public function openRegisterOutpatient($pasienId)
    {
        $this->selectedPasienId = $pasienId;
        $this->reg_poli_id = '';
        $this->reg_dokter_id = '';
        $this->reg_cara_bayar = 'Umum';
        $this->reg_jenis_kunjungan = 'Baru';
        $this->reg_keluhan_awal = '';
        $this->reg_tanggal_kunjungan = date('Y-m-d');
        $this->showRegisterModal = true;
    }

    // --- BOOKING FLOW ---
    public function openBookingModal($pasienId)
    {
        $this->selectedPasienId = $pasienId;
        $this->bookingDate = now()->addDay()->format('Y-m-d');
        $this->showBookingModal = true;
    }

    public function saveBooking()
    {
        $this->validate([
            'bookingDate' => 'required|date|after:today',
        ], [
            'bookingDate.after' => 'Tanggal booking harus minimal hari esok.',
        ]);

        // Check for duplicate booking on the same date
        $exists = Booking::where('pasien_id', $this->selectedPasienId)
            ->whereDate('booking_date', $this->bookingDate)
            ->where('status', 'pending')
            ->exists();

        if ($exists) {
            $this->addError('bookingDate', 'Pasien sudah memiliki booking pada tanggal tersebut.');

            return;
        }

        Booking::create([
            'pasien_id' => $this->selectedPasienId,
            'booking_date' => $this->bookingDate,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        $this->showBookingModal = false;
        Flux::toast(variant: 'success', text: 'Booking pendaftaran berhasil disimpan untuk tanggal '.Carbon::parse($this->bookingDate)->translatedFormat('l, d M Y').'.');
    }

    public function confirmBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);

        if (! $booking->booking_date->isToday()) {
            Flux::toast(variant: 'danger', text: 'Booking hanya dapat dikonfirmasi pada tanggal kunjungan yang dipilih.');

            return;
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        // Auto-open outpatient registration for confirmed booking
        $this->selectedPasienId = $booking->pasien_id;
        $this->reg_poli_id = '';
        $this->reg_dokter_id = '';
        $this->reg_cara_bayar = 'Umum';
        $this->reg_jenis_kunjungan = 'Baru';
        $this->reg_keluhan_awal = '';
        $this->reg_tanggal_kunjungan = $booking->booking_date->format('Y-m-d');
        $this->showRegisterModal = true;

        Flux::toast(variant: 'success', text: 'Kedatangan pasien terkonfirmasi! Silakan lengkapi registrasi rawat jalan.');
    }

    public function cancelBooking(int $bookingId)
    {
        $booking = Booking::findOrFail($bookingId);
        $booking->update(['status' => 'cancelled']);
        Flux::toast(variant: 'success', text: 'Booking pendaftaran berhasil dibatalkan.');
    }

    public function saveOutpatientRegistration()
    {
        $isWalkInLab = $this->isWalkInLab;

        $rules = [
            'reg_poli_id' => 'required|exists:master_polis,id',
            'reg_dokter_id' => $isWalkInLab ? 'nullable' : 'required|exists:master_petugass,id',
            'reg_cara_bayar' => 'required|in:Umum,BPJS,Dinas/Instansi',
            'reg_jenis_kunjungan' => 'required|in:Baru,Lama,Kontrol',
            'reg_keluhan_awal' => $isWalkInLab ? 'nullable|string' : 'required|string',
            'reg_tanggal_kunjungan' => 'required|date',
        ];

        if ($isWalkInLab) {
            $rules['selectedLabTests'] = 'required|array|min:1';
        }

        $this->validate($rules);

        $poli = Poli::findOrFail($this->reg_poli_id);
        $visitDate = $this->reg_tanggal_kunjungan;

        // 1. Verify scheduled doctor if NOT walk-in lab
        if (! $isWalkInLab) {
            $dayMap = [
                0 => 'Minggu',
                1 => 'Senin',
                2 => 'Selasa',
                3 => 'Rabu',
                4 => 'Kamis',
                5 => 'Jumat',
                6 => 'Sabtu',
            ];
            $currentDay = $dayMap[Carbon::parse($visitDate)->dayOfWeek];
            $isScheduled = DB::table('master_jadwal_dokters')
                ->where('petugas_id', $this->reg_dokter_id)
                ->where('poli_id', $this->reg_poli_id)
                ->where('hari', $currentDay)
                ->exists();

            if (! $isScheduled) {
                $this->addError('reg_dokter_id', 'Dokter tersebut tidak memiliki jadwal pada hari ' . $currentDay . '.');
                return;
            }
        }

        if ($isWalkInLab) {
            // Lab Walk-In flow
            DB::transaction(function () use ($visitDate, $poli) {
                $no_registrasi = 'REG-'.date('Ymd').'-'.sprintf('%04d', rand(1, 9999));
                
                $pendaftaran = Pendaftaran::create([
                    'no_registrasi' => $no_registrasi,
                    'pasien_id' => $this->selectedPasienId,
                    'poli_id' => $this->reg_poli_id,
                    'dokter_id' => null,
                    'no_antrean' => 'L-WLK',
                    'angka_antrean' => 0,
                    'status_antrean' => 'menunggu kasir',
                    'cara_bayar' => $this->reg_cara_bayar,
                    'jenis_kunjungan' => $this->reg_jenis_kunjungan,
                    'keluhan_awal' => $this->reg_keluhan_awal ?: 'Pemeriksaan Lab Mandiri',
                    'created_by' => auth()->id(),
                    'created_at' => Carbon::parse($visitDate.' '.date('H:i:s')),
                ]);

                // Calculate Lab Total Tariff
                $totalTariff = 0;
                $tests = \App\Models\MasterLabTest::whereIn('id', $this->selectedLabTests)->get();
                $isBpjs = $this->reg_cara_bayar === 'BPJS';

                foreach ($tests as $t) {
                    $totalTariff += $isBpjs ? $t->tarif_bpjs : $t->tarif_umum;
                }

                $grandTotal = $totalTariff + $this->adminFee;

                $today = now()->format('Ymd');
                $countToday = DB::table('invoices')->whereDate('created_at', now()->toDateString())->count();
                $invoiceNumber = 'INV-'.$today.'-'.str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

                // Upfront unpaid invoice
                $invoice = Invoice::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'medical_record_id' => null,
                    'invoice_number' => $invoiceNumber,
                    'subtotal' => $grandTotal,
                    'discount' => 0,
                    'grand_total' => $grandTotal,
                    'status' => 'unpaid',
                ]);

                // Snapshot Admin Fee
                if ($this->adminFee > 0) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'admin',
                        'description' => 'Biaya Administrasi Pendaftaran',
                        'qty' => 1,
                        'unit_price' => $this->adminFee,
                        'subtotal' => $this->adminFee,
                        'cara_bayar_item' => $isBpjs ? 'bpjs' : 'umum',
                    ]);
                }

                // Snapshot Lab Items
                foreach ($tests as $t) {
                    $price = $isBpjs ? $t->tarif_bpjs : $t->tarif_umum;
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'item_type' => 'lab',
                        'description' => 'Tes Lab: '.$t->test_name,
                        'qty' => 1,
                        'unit_price' => $price,
                        'subtotal' => $price,
                        'cara_bayar_item' => $isBpjs ? 'bpjs' : 'umum',
                    ]);
                }

                // Create Lab Order in pending status
                $labOrder = LabOrder::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'medical_record_id' => null,
                    'status' => 'pending',
                    'total_tariff' => $totalTariff,
                    'clinical_notes' => 'Direct Lab Walk-In',
                ]);

                foreach ($tests as $t) {
                    $price = $isBpjs ? $t->tarif_bpjs : $t->tarif_umum;
                    \App\Models\LabOrderResult::create([
                        'lab_order_id' => $labOrder->id,
                        'master_lab_test_id' => $t->id,
                        'test_name_snapshot' => $t->test_name,
                        'tariff_snapshot' => $price,
                        'normal_range_snapshot' => $t->default_normal_range,
                        'unit_snapshot' => $t->default_unit,
                    ]);
                }
            });

            $this->showRegisterModal = false;
            Flux::toast(variant: 'success', text: 'Pendaftaran Lab Walk-In berhasil! Tagihan dibuat di kasir.');
            $this->successPrintUrl = '';
            $this->successPrintMessage = 'Pendaftaran Lab Walk-In berhasil! Silakan lakukan pembayaran di kasir.';
            $this->showSuccessPrintModal = true;
        } else {
            // Outpatient Flow (Poliklinik Medis)
            $prefix = 'A';
            if (stripos($poli->nama_poli, 'gigi') !== false) {
                $prefix = 'B';
            } elseif (stripos($poli->nama_poli, 'kia') !== false || stripos($poli->nama_poli, 'anak') !== false || stripos($poli->nama_poli, 'ibu') !== false) {
                $prefix = 'C';
            }

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
                'jenis_kunjungan' => $this->reg_jenis_kunjungan,
                'keluhan_awal' => $this->reg_keluhan_awal,
                'created_by' => auth()->id(),
                'created_at' => Carbon::parse($visitDate.' '.date('H:i:s')),
            ]);

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

            $this->successPrintUrl = route('print.queue-ticket', ['id' => $medicalRecord->id]);
            $this->successPrintMessage = 'Pendaftaran rawat jalan berhasil didaftarkan! No Antrean: '.$nomor_antrean.'.';
            $this->showSuccessPrintModal = true;
        }
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
        Flux::toast(variant: 'success', text: 'Surat persetujuan berhasil dibuat!');

        $this->successPrintUrl = route('print.consent', ['id' => $saved->id]);
        $this->successPrintMessage = 'Surat persetujuan berhasil dibuat!';
        $this->showSuccessPrintModal = true;
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
        Flux::toast(variant: 'success', text: 'Surat Rujukan berhasil dibuat!');

        $this->successPrintUrl = route('print.referral', ['id' => $saved->id]);
        $this->successPrintMessage = 'Surat Rujukan berhasil dibuat!';
        $this->showSuccessPrintModal = true;
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
        Flux::toast(variant: 'success', text: 'Surat Keterangan berhasil dibuat!');

        $this->successPrintUrl = route('print.certificate', ['id' => $saved->id]);
        $this->successPrintMessage = 'Surat Keterangan berhasil dibuat!';
        $this->showSuccessPrintModal = true;
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
            ->paginate(5);

        $todayQueues = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter', 'pendaftaran.poli'])
            ->whereDate('tanggal_kunjungan', '>=', $this->filterStartDate)
            ->whereDate('tanggal_kunjungan', '<=', $this->filterEndDate)
            ->where('status', '!=', 'batal')
            ->orderBy('id', 'desc')
            ->get();

        $clinicId = \App\Models\FaskesProfile::first()->id ?? null;

        $agamas = MasterAgama::where(function ($q) use ($clinicId) {
            $q->whereNull('clinic_id')->orWhere('clinic_id', $clinicId);
        })->where('is_active', true)->get();

        $pendidikans = MasterPendidikan::where(function ($q) use ($clinicId) {
            $q->whereNull('clinic_id')->orWhere('clinic_id', $clinicId);
        })->get();

        $pekerjaans = MasterPekerjaan::where(function ($q) use ($clinicId) {
            $q->whereNull('clinic_id')->orWhere('clinic_id', $clinicId);
        })->get();

        $bookings = Booking::with('pasien')
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('booking_date', 'asc')
            ->get();

        return view('components.⚡pendaftaran.pendaftaran', [
            'pasiens' => $data,
            'polis' => Poli::where('is_active', true)->get(),
            'doctors' => MasterPetugas::where('jenis_petugas', 'Dokter')->where('is_aktif', true)->get(),
            'petugass' => MasterPetugas::where('is_aktif', true)->get(),
            'agamas' => $agamas,
            'pendidikans' => $pendidikans,
            'pekerjaans' => $pekerjaans,
            'labTests' => \App\Models\MasterLabTest::where('is_aktif', true)->get(),
            'todayQueues' => $todayQueues,
            'bookings' => $bookings,
        ]);
    }
};
