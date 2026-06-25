<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSpesialisBpjsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialists = [
            ['kode_spesialis' => '001', 'nama_spesialis' => 'Penyakit Dalam'],
            ['kode_spesialis' => '002', 'nama_spesialis' => 'Kesehatan Anak'],
            ['kode_spesialis' => '003', 'nama_spesialis' => 'Kebidanan & Kandungan'],
            ['kode_spesialis' => '004', 'nama_spesialis' => 'Bedah'],
            ['kode_spesialis' => '005', 'nama_spesialis' => 'Penyakit Mata'],
            ['kode_spesialis' => '006', 'nama_spesialis' => 'THT-KL'],
            ['kode_spesialis' => '007', 'nama_spesialis' => 'Kulit & Kelamin'],
            ['kode_spesialis' => '008', 'nama_spesialis' => 'Penyakit Saraf'],
            ['kode_spesialis' => '009', 'nama_spesialis' => 'Jiwa / Psikiatri'],
            ['kode_spesialis' => '010', 'nama_spesialis' => 'Jantung & Pembuluh Darah'],
            ['kode_spesialis' => '011', 'nama_spesialis' => 'Paru'],
            ['kode_spesialis' => '012', 'nama_spesialis' => 'Gigi & Mulut'],
        ];

        foreach ($specialists as $sp) {
            DB::table('master_spesialis_bpjs')->updateOrInsert(
                ['kode_spesialis' => $sp['kode_spesialis']],
                ['nama_spesialis' => $sp['nama_spesialis'], 'is_active' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
