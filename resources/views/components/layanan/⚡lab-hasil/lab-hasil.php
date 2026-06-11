<?php

use App\Models\LabOrder;
use App\Models\LabOrderResult;
use App\Models\MasterPetugas;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $labOrderId;

    public LabOrder $labOrder;

    public string $analisisNama = '';

    public ?int $analisisId = null;

    /** @var array<int, array{id: int, test_name: string, normal_range: string|null, unit: string|null, tariff: int, result_value: string, is_abnormal: bool}> */
    public array $resultRows = [];

    public bool $isFinalized = false;

    public function mount(LabOrder $labOrder): void
    {
        $this->labOrderId = $labOrder->id;
        $this->labOrder = $labOrder->load([
            'results',
            'medicalRecord.pendaftaran.pasien',
            'medicalRecord.pendaftaran.poli',
            'requester',
        ]);

        $this->isFinalized = $labOrder->status === 'completed';

        // Auto-assign current logged-in user as analis
        $analis = MasterPetugas::where('user_id', Auth::id())->first();
        $this->analisisNama = $analis?->nama_petugas ?? Auth::user()->name;
        $this->analisisId = $analis?->id;

        // Load result rows
        foreach ($labOrder->results as $result) {
            $this->resultRows[] = [
                'id' => $result->id,
                'test_name' => $result->test_name_snapshot,
                'normal_range' => $result->normal_range_snapshot,
                'unit' => $result->unit_snapshot,
                'tariff' => $result->tariff_snapshot,
                'result_value' => $result->result_value ?? '',
                'is_abnormal' => (bool) $result->is_abnormal,
            ];
        }
    }

    public function saveDraft(): void
    {
        if ($this->isFinalized) {
            return;
        }

        $this->persistResults(status: 'processing');

        Flux::toast(variant: 'success', text: 'Draft hasil lab disimpan.');
    }

    public function finalize(): void
    {
        if ($this->isFinalized) {
            return;
        }

        $this->persistResults(status: 'completed');

        $this->isFinalized = true;

        Flux::toast(variant: 'success', text: 'Hasil laboratorium telah difinalisasi.');
    }

    private function persistResults(string $status): void
    {
        foreach ($this->resultRows as $row) {
            LabOrderResult::where('id', $row['id'])->update([
                'result_value' => $row['result_value'] ?: null,
                'is_abnormal' => $row['is_abnormal'],
                'analis_id' => $this->analisisId,
            ]);
        }

        $this->labOrder->update(['status' => $status]);
    }

    public function render()
    {
        return view('components.layanan.⚡lab-hasil.lab-hasil')
            ->layout('layouts::app');
    }
};
