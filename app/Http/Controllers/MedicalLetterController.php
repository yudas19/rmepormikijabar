<?php

namespace App\Http\Controllers;

use App\Models\FaskesProfile;
use App\Models\MedicalLetter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalLetterController extends Controller
{
    /**
     * Store a newly created medical letter in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'pasien_id' => 'required|exists:pasiens,id',
            'dokter_id' => 'required|exists:master_petugass,id',
            'jenis_surat' => 'required|in:surat_sakit,surat_sehat',
            'meta_data' => 'required|array',
        ]);

        $year = date('Y');
        $romanMonth = $this->getRomanMonth((int) date('n'));

        // Count existing letters for current year to get the sequential number
        $count = MedicalLetter::whereYear('created_at', $year)->count() + 1;
        $sequence = sprintf('%03d', $count);

        // Format: e.g. 001/SKU/Klinik/VI/2026
        $nomorSurat = "{$sequence}/SKU/Klinik/{$romanMonth}/{$year}";

        $letter = MedicalLetter::create([
            'medical_record_id' => $validated['medical_record_id'],
            'pasien_id' => $validated['pasien_id'],
            'dokter_id' => $validated['dokter_id'],
            'nomor_surat' => $nomorSurat,
            'jenis_surat' => $validated['jenis_surat'],
            'meta_data' => $validated['meta_data'],
        ]);

        return response()->json([
            'success' => true,
            'letter' => $letter,
            'print_url' => route('medical-letters.print', $letter->id),
        ]);
    }

    /**
     * Print/render the medical letter layout.
     */
    public function print(int $id): View
    {
        $letter = MedicalLetter::with(['pasien', 'dokter', 'medicalRecord'])->findOrFail($id);
        $profile = FaskesProfile::find(1) ?? new FaskesProfile;

        return view('print.medical_letter', compact('letter', 'profile'));
    }

    /**
     * Get Roman numeral representation of a month number.
     */
    private function getRomanMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];

        return $map[$month] ?? 'I';
    }
}
