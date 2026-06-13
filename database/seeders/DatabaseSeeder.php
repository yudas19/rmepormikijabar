<?php

namespace Database\Seeders;

use App\Models\MasterAturanPakai;
use App\Models\MasterIcd10;
use App\Models\MasterIcd9;
use App\Models\MasterMetodeRacik;
use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\Poli;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles
        $roles = [
            'admin',
            'rekam_medis',
            'dokter_umum',
            'dokter_gigi',
            'perawat',
            'bidan',
            'analis_lab',
            'apoteker',
            'kasir',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // 2. Users & Petugas
        $adminUser = User::factory()->create([
            'name' => 'Admin System',
            'email' => 'admin@rmepormikijabar.com',
            'password' => bcrypt('password'),
        ]);
        $adminUser->assignRole('admin');

        // Doctor
        $docUser = User::factory()->create([
            'name' => 'dr. Andi Wijaya',
            'email' => 'dokter@rmepormikijabar.com',
            'password' => bcrypt('password'),
        ]);
        $docUser->assignRole('dokter_umum');

        MasterPetugas::create([
            'user_id' => $docUser->id,
            'nama_petugas' => 'dr. Andi Wijaya',
            'nik' => '3201010101010001',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '1985-05-12',
            'alamat' => 'Jl. Kebon Jati No. 12',
            'telepon' => '022-123456',
            'no_hp' => '081234567890',
            'bekerja_sejak' => '2015-01-01',
            'jenis_petugas' => 'Dokter',
            'nomor_str' => 'STR-12345-ANDI',
            'nomor_sip' => 'SIP-67890-ANDI',
            'ihs_number_practitioner' => '1000234567',
            'is_aktif' => true,
        ]);

        // Nurse
        $nurseUser = User::factory()->create([
            'name' => 'Ns. Rina Astuti, S.Kep',
            'email' => 'perawat@rmepormikijabar.com',
            'password' => bcrypt('password'),
        ]);
        $nurseUser->assignRole('perawat');

        MasterPetugas::create([
            'user_id' => $nurseUser->id,
            'nama_petugas' => 'Ns. Rina Astuti, S.Kep',
            'nik' => '3201010101010002',
            'tempat_lahir' => 'Sleman',
            'tanggal_lahir' => '1990-09-20',
            'alamat' => 'Jl. Merdeka No. 45',
            'telepon' => '022-765432',
            'no_hp' => '087654321098',
            'bekerja_sejak' => '2018-03-01',
            'jenis_petugas' => 'Perawat',
            'nomor_str' => 'STR-54321-RINA',
            'nomor_sip' => 'SIP-09876-RINA',
            'ihs_number_practitioner' => '2000765432',
            'is_aktif' => true,
        ]);

        // 3. Polis (Poliklinik)
        $polis = [
            [
                'kode_poli' => 'UMU',
                'nama_poli' => 'Poli Umum',
                'kode_poli_bpjs' => '001',
                'satu_sehat_location_id' => 'LOC-UMUM-01',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'GIG',
                'nama_poli' => 'Poli Gigi',
                'kode_poli_bpjs' => '002',
                'satu_sehat_location_id' => 'LOC-GIGI-02',
                'is_active' => true,
            ],
            [
                'kode_poli' => 'KIA',
                'nama_poli' => 'Klinik KIA (Kesehatan Ibu & Anak)',
                'kode_poli_bpjs' => '003',
                'satu_sehat_location_id' => 'LOC-KIA-03',
                'is_active' => true,
            ],
        ];

        foreach ($polis as $poli) {
            Poli::create($poli);
        }

        // 4. Master Obat
        $obats = [
            ['kode_obat' => 'OBT-001', 'nama_obat' => 'Paracetamol 500 mg', 'satuan' => 'Tablet', 'stok_saat_ini' => 1000, 'stok_minimal' => 100, 'tanggal_kadaluarsa' => '2027-12-31', 'harga_beli' => 200, 'harga_jual' => 500, 'kode_kfa' => 'KFA-930001', 'nama_kfa' => 'Paracetamol 500mg Oral Tablet', 'is_aktif' => true],
            ['kode_obat' => 'OBT-002', 'nama_obat' => 'Amoxicillin 500 mg', 'satuan' => 'Tablet', 'stok_saat_ini' => 500, 'stok_minimal' => 50, 'tanggal_kadaluarsa' => '2027-06-30', 'harga_beli' => 400, 'harga_jual' => 800, 'kode_kfa' => 'KFA-930002', 'nama_kfa' => 'Amoxicillin 500mg Oral Tablet', 'is_aktif' => true],
            ['kode_obat' => 'OBT-003', 'nama_obat' => 'Ibuprofen 400 mg', 'satuan' => 'Tablet', 'stok_saat_ini' => 800, 'stok_minimal' => 100, 'tanggal_kadaluarsa' => '2028-03-15', 'harga_beli' => 300, 'harga_jual' => 600, 'kode_kfa' => 'KFA-930003', 'nama_kfa' => 'Ibuprofen 400mg Oral Tablet', 'is_aktif' => true],
            ['kode_obat' => 'OBT-004', 'nama_obat' => 'Cetirizine 10 mg', 'satuan' => 'Tablet', 'stok_saat_ini' => 1200, 'stok_minimal' => 100, 'tanggal_kadaluarsa' => '2027-09-01', 'harga_beli' => 150, 'harga_jual' => 400, 'kode_kfa' => 'KFA-930004', 'nama_kfa' => 'Cetirizine 10mg Oral Tablet', 'is_aktif' => true],
            ['kode_obat' => 'OBT-005', 'nama_obat' => 'Metformin 500 mg', 'satuan' => 'Tablet', 'stok_saat_ini' => 1500, 'stok_minimal' => 200, 'tanggal_kadaluarsa' => '2028-01-15', 'harga_beli' => 250, 'harga_jual' => 600, 'kode_kfa' => 'KFA-930005', 'nama_kfa' => 'Metformin Hcl 500mg Tablet', 'is_aktif' => true],
            ['kode_obat' => 'OBT-006', 'nama_obat' => 'Ambroxol Sirup 15mg/5ml', 'satuan' => 'Botol', 'stok_saat_ini' => 200, 'stok_minimal' => 20, 'tanggal_kadaluarsa' => '2026-11-30', 'harga_beli' => 3500, 'harga_jual' => 5000, 'kode_kfa' => 'KFA-930006', 'nama_kfa' => 'Ambroxol Sirup 60ml', 'is_aktif' => true],
        ];

        foreach ($obats as $obat) {
            MasterObat::create($obat);
        }

        // 5. Master Metode Racik
        $metodes = [
            ['kode_metode_racik' => 'PULV', 'nama_metode_racik' => 'Pulveres (Puyer)'],
            ['kode_metode_racik' => 'CAPS', 'nama_metode_racik' => 'Kapsul'],
            ['kode_metode_racik' => 'OINT', 'nama_metode_racik' => 'Salep / Cream'],
            ['kode_metode_racik' => 'SYRP', 'nama_metode_racik' => 'Sirup Campuran'],
        ];

        foreach ($metodes as $metode) {
            MasterMetodeRacik::create($metode);
        }

        // 6. Master Aturan Pakai (Aturan Pakai Obat)
        $aturans = [
            ['nama_aturan_pakai' => '3 x 1 Sehari (Tiap 8 Jam)'],
            ['nama_aturan_pakai' => '2 x 1 Sehari (Tiap 12 Jam)'],
            ['nama_aturan_pakai' => '1 x 1 Sehari (Pagi)'],
            ['nama_aturan_pakai' => '1 x 1 Sehari (Malam)'],
            ['nama_aturan_pakai' => '3 x 1/2 Sehari'],
            ['nama_aturan_pakai' => 'Kalo Perlu (Sesuai Kebutuhan)'],
        ];

        foreach ($aturans as $aturan) {
            MasterAturanPakai::create($aturan);
        }

        // 7. Master ICD-10 (Diagnosa)
        $icd10s = [
            ['kode' => 'J00', 'nama_penyakit' => 'Acute nasopharyngitis [common cold]', 'nama_penyakit_indonesia' => 'Nasofaringitis Akut (Flu Biasa)'],
            ['kode' => 'K04.0', 'nama_penyakit' => 'Pulpitis', 'nama_penyakit_indonesia' => 'Radang Pulpa Gigi (Pulpitis)'],
            ['kode' => 'I10', 'nama_penyakit' => 'Essential (primary) hypertension', 'nama_penyakit_indonesia' => 'Hipertensi Esensial (Tekanan Darah Tinggi)'],
            ['kode' => 'E11.9', 'nama_penyakit' => 'Type 2 diabetes mellitus without complications', 'nama_penyakit_indonesia' => 'Diabetes Melitus Tipe 2 Tanpa Komplikasi'],
            ['kode' => 'A09.9', 'nama_penyakit' => 'Gastroenteritis and colitis of unspecified origin', 'nama_penyakit_indonesia' => 'Gastroenteritis dan Kolitis Non-infeksi'],
        ];

        foreach ($icd10s as $icd10) {
            MasterIcd10::create($icd10);
        }

        // 8. Master ICD-9 (Prosedur / Tindakan)
        $icd9s = [
            ['kode' => '89.52', 'nama' => 'Electrocardiogram'],
            ['kode' => '93.57', 'nama' => 'Application of other wound dressing'],
            ['kode' => '23.09', 'nama' => 'Extraction of other tooth'],
            ['kode' => '23.2', 'nama' => 'Restoration of tooth by filling'],
            ['kode' => '96.59', 'nama' => 'Other irrigation of wound'],
        ];

        foreach ($icd9s as $icd9) {
            MasterIcd9::create($icd9);
        }

        // 9. Master Lab Tests
        $this->call(MasterLabTestSeeder::class);

        // 10. Permissions
        $this->call(PermissionsSeeder::class);
    }
}
