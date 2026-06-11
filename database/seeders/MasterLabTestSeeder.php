<?php

namespace Database\Seeders;

use App\Models\MasterLabTest;
use Illuminate\Database\Seeder;

class MasterLabTestSeeder extends Seeder
{
    /**
     * @var array<int, array{test_name: string, category: string, tariff: int, default_normal_range: string, default_unit: string}>
     */
    private array $tests = [
        // Hematologi
        ['test_name' => 'Darah Lengkap (CBC)', 'category' => 'Hematologi', 'tariff' => 45000, 'default_normal_range' => 'Lihat detail panel', 'default_unit' => 'Panel'],
        ['test_name' => 'Hemoglobin', 'category' => 'Hematologi', 'tariff' => 15000, 'default_normal_range' => 'L: 13.5-17.5 / P: 12-15.5', 'default_unit' => 'g/dL'],
        ['test_name' => 'Hematokrit', 'category' => 'Hematologi', 'tariff' => 15000, 'default_normal_range' => 'L: 40-54 / P: 36-48', 'default_unit' => '%'],
        ['test_name' => 'Trombosit', 'category' => 'Hematologi', 'tariff' => 15000, 'default_normal_range' => '150.000-400.000', 'default_unit' => '/µL'],
        ['test_name' => 'Leukosit', 'category' => 'Hematologi', 'tariff' => 15000, 'default_normal_range' => '4.500-11.000', 'default_unit' => '/µL'],
        ['test_name' => 'Eritrosit', 'category' => 'Hematologi', 'tariff' => 15000, 'default_normal_range' => 'L: 4.5-5.9 / P: 3.8-5.2', 'default_unit' => 'juta/µL'],
        ['test_name' => 'LED (Laju Endap Darah)', 'category' => 'Hematologi', 'tariff' => 20000, 'default_normal_range' => 'L: 0-15 / P: 0-20', 'default_unit' => 'mm/jam'],

        // Kimia Darah
        ['test_name' => 'Gula Darah Sewaktu (GDS)', 'category' => 'Kimia Darah', 'tariff' => 20000, 'default_normal_range' => '70-140', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Gula Darah Puasa (GDP)', 'category' => 'Kimia Darah', 'tariff' => 22000, 'default_normal_range' => '70-100', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Gula Darah 2 Jam PP (GD2PP)', 'category' => 'Kimia Darah', 'tariff' => 22000, 'default_normal_range' => '<140', 'default_unit' => 'mg/dL'],
        ['test_name' => 'HbA1c', 'category' => 'Kimia Darah', 'tariff' => 75000, 'default_normal_range' => '<5.7%', 'default_unit' => '%'],
        ['test_name' => 'Kolesterol Total', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '<200', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Trigliserida', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '<150', 'default_unit' => 'mg/dL'],
        ['test_name' => 'HDL Kolesterol', 'category' => 'Kimia Darah', 'tariff' => 30000, 'default_normal_range' => '>40 (L) / >50 (P)', 'default_unit' => 'mg/dL'],
        ['test_name' => 'LDL Kolesterol', 'category' => 'Kimia Darah', 'tariff' => 30000, 'default_normal_range' => '<100', 'default_unit' => 'mg/dL'],
        ['test_name' => 'SGOT (AST)', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '5-40', 'default_unit' => 'U/L'],
        ['test_name' => 'SGPT (ALT)', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '5-41', 'default_unit' => 'U/L'],
        ['test_name' => 'Kreatinin', 'category' => 'Kimia Darah', 'tariff' => 22000, 'default_normal_range' => 'L: 0.7-1.3 / P: 0.6-1.1', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Ureum / BUN', 'category' => 'Kimia Darah', 'tariff' => 22000, 'default_normal_range' => '7-25', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Asam Urat', 'category' => 'Kimia Darah', 'tariff' => 20000, 'default_normal_range' => 'L: 3.5-7.2 / P: 2.6-6.0', 'default_unit' => 'mg/dL'],
        ['test_name' => 'Albumin', 'category' => 'Kimia Darah', 'tariff' => 30000, 'default_normal_range' => '3.5-5.0', 'default_unit' => 'g/dL'],
        ['test_name' => 'Protein Total', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '6.4-8.3', 'default_unit' => 'g/dL'],
        ['test_name' => 'Bilirubin Total', 'category' => 'Kimia Darah', 'tariff' => 25000, 'default_normal_range' => '0.2-1.2', 'default_unit' => 'mg/dL'],

        // Urinalisis
        ['test_name' => 'Urinalisis Lengkap', 'category' => 'Urinalisis', 'tariff' => 30000, 'default_normal_range' => 'Normal', 'default_unit' => 'Panel'],
        ['test_name' => 'Protein Urin Kualitatif', 'category' => 'Urinalisis', 'tariff' => 15000, 'default_normal_range' => 'Negatif', 'default_unit' => 'Kual.'],
        ['test_name' => 'Glukosa Urin', 'category' => 'Urinalisis', 'tariff' => 15000, 'default_normal_range' => 'Negatif', 'default_unit' => 'Kual.'],
        ['test_name' => 'Sedimen Urin', 'category' => 'Urinalisis', 'tariff' => 20000, 'default_normal_range' => 'Normal', 'default_unit' => 'Mikros.'],

        // Serologi
        ['test_name' => 'Anti-HIV', 'category' => 'Serologi', 'tariff' => 50000, 'default_normal_range' => 'Non-Reaktif', 'default_unit' => 'Kual.'],
        ['test_name' => 'HBsAg (Hepatitis B)', 'category' => 'Serologi', 'tariff' => 45000, 'default_normal_range' => 'Non-Reaktif', 'default_unit' => 'Kual.'],
        ['test_name' => 'Anti-HCV (Hepatitis C)', 'category' => 'Serologi', 'tariff' => 60000, 'default_normal_range' => 'Non-Reaktif', 'default_unit' => 'Kual.'],
        ['test_name' => 'Widal (Tifoid)', 'category' => 'Serologi', 'tariff' => 40000, 'default_normal_range' => '<1/160', 'default_unit' => 'Titer'],
        ['test_name' => 'Dengue NS1 Antigen', 'category' => 'Serologi', 'tariff' => 120000, 'default_normal_range' => 'Negatif', 'default_unit' => 'Kual.'],
        ['test_name' => 'CRP (C-Reactive Protein)', 'category' => 'Serologi', 'tariff' => 55000, 'default_normal_range' => '<6', 'default_unit' => 'mg/L'],
        ['test_name' => 'RF (Rheumatoid Factor)', 'category' => 'Serologi', 'tariff' => 55000, 'default_normal_range' => 'Negatif', 'default_unit' => 'IU/mL'],

        // Elektrolit
        ['test_name' => 'Natrium (Na)', 'category' => 'Elektrolit', 'tariff' => 30000, 'default_normal_range' => '135-145', 'default_unit' => 'mEq/L'],
        ['test_name' => 'Kalium (K)', 'category' => 'Elektrolit', 'tariff' => 30000, 'default_normal_range' => '3.5-5.1', 'default_unit' => 'mEq/L'],
        ['test_name' => 'Klorida (Cl)', 'category' => 'Elektrolit', 'tariff' => 30000, 'default_normal_range' => '98-106', 'default_unit' => 'mEq/L'],
        ['test_name' => 'Elektrolit Panel (Na, K, Cl)', 'category' => 'Elektrolit', 'tariff' => 75000, 'default_normal_range' => 'Lihat detail panel', 'default_unit' => 'Panel'],

        // Hormon
        ['test_name' => 'TSH (Thyroid Stimulating Hormone)', 'category' => 'Hormon', 'tariff' => 120000, 'default_normal_range' => '0.4-4.0', 'default_unit' => 'mIU/L'],
        ['test_name' => 'FT4 (Free Thyroxine)', 'category' => 'Hormon', 'tariff' => 130000, 'default_normal_range' => '0.8-1.8', 'default_unit' => 'ng/dL'],
        ['test_name' => 'Beta-HCG (Tes Kehamilan)', 'category' => 'Hormon', 'tariff' => 80000, 'default_normal_range' => 'Negatif (non-hamil)', 'default_unit' => 'mIU/mL'],

        // Mikrobiologi
        ['test_name' => 'BTA Sputum (TBC)', 'category' => 'Mikrobiologi', 'tariff' => 25000, 'default_normal_range' => 'Negatif', 'default_unit' => 'Kual.'],
        ['test_name' => 'Kultur Urin', 'category' => 'Mikrobiologi', 'tariff' => 95000, 'default_normal_range' => 'Steril', 'default_unit' => 'Kual.'],
    ];

    public function run(): void
    {
        // Only seed if table is empty
        if (MasterLabTest::count() > 0) {
            return;
        }

        foreach ($this->tests as $test) {
            MasterLabTest::create([
                'test_name' => $test['test_name'],
                'category' => $test['category'],
                'tariff' => $test['tariff'],
                'default_normal_range' => $test['default_normal_range'],
                'default_unit' => $test['default_unit'],
                'is_aktif' => true,
            ]);
        }

        $this->command->info('Seeded '.count($this->tests).' master lab tests.');
    }
}
