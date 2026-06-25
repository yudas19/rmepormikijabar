<?php

use App\Models\MasterObat;
use App\Models\MasterPetugas;
use App\Models\MedicalRecordPrescription;
use App\Models\StockMovement;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;

new class extends Component
{
    #[Locked]
    public int $prescriptionId;

    public MedicalRecordPrescription $prescription;

    public string $apotekerNama = '';

    public ?int $apotekerId = null;

    public bool $isFinalized = false;

    /**
     * Each element: [
     *   'item_id' => int,            // prescription_item id
     *   'requested_obat_name' => string,
     *   'requested_qty' => float,
     *   'requested_satuan' => string,
     *   'dispensed_obat_id' => int,
     *   'dispensed_obat_name' => string,
     *   'dispensed_qty' => float,
     *   'dispensed_signa' => string,
     *   'harga_jual' => float,
     *   'subtotal' => float,
     *   'stok_available' => int,
     * ]
     *
     * @var array<int, array<string, mixed>>
     */
    public array $dispensingRows = [];

    public float $grandTotal = 0;

    // Drug substitution search
    public string $drugSubQuery = '';

    /** @var array<int, mixed> */
    public array $drugSubResults = [];

    public ?int $editingRowIndex = null;

    public function mount(MedicalRecordPrescription $prescription): void
    {
        $this->prescriptionId = $prescription->id;
        $this->prescription = $prescription->load([
            'medicalRecord.pendaftaran.pasien',
            'medicalRecord.pendaftaran.poli',
            'medicalRecord.pendaftaran.dokter',
            'metodeRacik',
            'apoteker',
        ]);

        $medicalRecordId = $this->prescription->medical_record_id;
        $prescriptions = MedicalRecordPrescription::with(['items.requestedObat', 'items.dispensedObat', 'metodeRacik'])
            ->where('medical_record_id', $medicalRecordId)
            ->get();

        $this->isFinalized = ! $prescriptions->contains(fn ($p) => $p->dispensing_status !== 'dispensed');

        // Auto-assign pharmacist
        $apoteker = MasterPetugas::where('user_id', Auth::id())->first();
        $this->apotekerNama = $apoteker?->nama_petugas ?? Auth::user()->name;
        $this->apotekerId = $apoteker?->id;

        // Build dispensing rows
        foreach ($prescriptions as $presc) {
            foreach ($presc->items as $item) {
                $obat = $item->requestedObat;
                $dispensedObat = $item->dispensedObat ?? $obat;
                $dispensedQty = $item->dispensed_qty ?? $item->requested_qty;
                $hargaJual = $dispensedObat?->harga_jual ?? 0;

                $this->dispensingRows[] = [
                    'prescription_id' => $presc->id,
                    'item_id' => $item->id,
                    'requested_obat_name' => $obat?->nama_obat ?? '-',
                    'requested_qty' => (float) $item->requested_qty,
                    'requested_satuan' => $item->satuan ?? $obat?->satuan ?? '-',
                    'dispensed_obat_id' => $item->dispensed_obat_id ?? $obat?->id,
                    'dispensed_obat_name' => $dispensedObat?->nama_obat ?? $obat?->nama_obat ?? '-',
                    'dispensed_qty' => (float) $dispensedQty,
                    'dispensed_signa' => $item->dispensed_signa ?? $item->requested_signa ?? $presc->aturan_pakai ?? '',
                    'harga_jual' => (float) $hargaJual,
                    'subtotal' => (float) ($item->subtotal_price > 0 ? $item->subtotal_price : $hargaJual * $dispensedQty),
                    'stok_available' => $dispensedObat?->stok_saat_ini ?? 0,
                    'prescription_type' => $presc->type,
                    'prescription_nama_racikan' => $presc->nama_racikan,
                ];
            }
        }

        $this->recalcGrandTotal();
    }

    public function updatedDispensingRows(): void
    {
        // Recalculate subtotals when qty changes
        foreach ($this->dispensingRows as $idx => $row) {
            $this->dispensingRows[$idx]['subtotal'] = round((float) $row['harga_jual'] * (float) $row['dispensed_qty'], 2);
        }

        $this->recalcGrandTotal();
    }

    public function openDrugSubstitution(int $index): void
    {
        $this->editingRowIndex = $index;
        $this->drugSubQuery = '';
        $this->drugSubResults = [];
    }

    public function updatedDrugSubQuery(): void
    {
        if (strlen($this->drugSubQuery) >= 2) {
            $this->drugSubResults = MasterObat::where('is_aktif', true)
                ->where('nama_obat', 'like', '%'.$this->drugSubQuery.'%')
                ->orderBy('nama_obat')
                ->take(8)
                ->get()
                ->toArray();
        } else {
            $this->drugSubResults = [];
        }
    }

    public function selectSubstitute(int $obatId): void
    {
        if ($this->editingRowIndex === null || $this->isFinalized) {
            return;
        }

        $obat = MasterObat::findOrFail($obatId);
        $idx = $this->editingRowIndex;

        $this->dispensingRows[$idx]['dispensed_obat_id'] = $obat->id;
        $this->dispensingRows[$idx]['dispensed_obat_name'] = $obat->nama_obat;
        $this->dispensingRows[$idx]['harga_jual'] = (float) $obat->harga_jual;
        $this->dispensingRows[$idx]['stok_available'] = $obat->stok_saat_ini;
        $this->dispensingRows[$idx]['subtotal'] = round((float) $obat->harga_jual * (float) $this->dispensingRows[$idx]['dispensed_qty'], 2);

        $this->editingRowIndex = null;
        $this->drugSubQuery = '';
        $this->drugSubResults = [];
        $this->recalcGrandTotal();
    }

    public function cancelSubstitution(): void
    {
        $this->editingRowIndex = null;
        $this->drugSubQuery = '';
        $this->drugSubResults = [];
    }

    public function saveDraft(): void
    {
        if ($this->isFinalized) {
            return;
        }

        $this->persistItems();

        Flux::toast(variant: 'success', text: 'Draft dispensing disimpan.');
    }

    public function finalize(): void
    {
        if ($this->isFinalized) {
            return;
        }

        // Validate stock sufficiency
        foreach ($this->dispensingRows as $idx => $row) {
            $obat = MasterObat::find($row['dispensed_obat_id']);
            if (! $obat) {
                Flux::toast(variant: 'danger', text: 'Obat pada baris '.($idx + 1).' tidak ditemukan.');

                return;
            }
            if ($obat->stok_saat_ini < $row['dispensed_qty']) {
                Flux::toast(variant: 'danger', text: 'Stok '.$obat->nama_obat.' tidak cukup. Tersedia: '.$obat->stok_saat_ini.', Diminta: '.$row['dispensed_qty']);

                return;
            }
        }

        DB::transaction(function () {
            $this->persistItems();

            // Deduct stock and log movements
            foreach ($this->dispensingRows as $row) {
                $obat = MasterObat::find($row['dispensed_obat_id']);
                $previousStock = $obat->stok_saat_ini;
                $qty = (int) $row['dispensed_qty'];

                $obat->decrement('stok_saat_ini', $qty);

                StockMovement::create([
                    'master_obat_id' => $obat->id,
                    'type' => 'out',
                    'quantity' => -$qty,
                    'previous_stock' => $previousStock,
                    'current_stock' => $previousStock - $qty,
                    'notes' => 'Dispensing resep #'.$row['prescription_id'].' untuk '.$this->prescription->medicalRecord?->pendaftaran?->pasien?->nama_pasien,
                    'user_id' => Auth::id(),
                    'prescription_id' => $row['prescription_id'],
                ]);
            }

            // Flip all prescriptions of this medical record to dispensed
            $medicalRecordId = $this->prescription->medical_record_id;
            MedicalRecordPrescription::where('medical_record_id', $medicalRecordId)->update([
                'dispensing_status' => 'dispensed',
                'apoteker_id' => $this->apotekerId,
                'dispensed_at' => now(),
            ]);
        });

        $this->isFinalized = true;

        Flux::toast(variant: 'success', text: 'Obat telah diserahkan dan stok berkurang.');
    }

    private function persistItems(): void
    {
        foreach ($this->dispensingRows as $row) {
            DB::table('medical_record_prescription_items')
                ->where('id', $row['item_id'])
                ->update([
                    'dispensed_obat_id' => $row['dispensed_obat_id'],
                    'dispensed_qty' => $row['dispensed_qty'],
                    'dispensed_signa' => $row['dispensed_signa'],
                    'subtotal_price' => $row['subtotal'],
                    'apoteker_id' => Auth::id(),
                ]);
        }
    }

    private function recalcGrandTotal(): void
    {
        $this->grandTotal = round(array_sum(array_column($this->dispensingRows, 'subtotal')), 2);
    }

    public function render()
    {
        return view('components.layanan.⚡farmasi-dispensing.farmasi-dispensing')
            ->layout('layouts::app');
    }
};
