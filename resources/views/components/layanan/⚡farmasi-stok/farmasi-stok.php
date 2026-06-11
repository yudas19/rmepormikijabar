<?php

use App\Models\MasterObat;
use App\Models\StockMovement;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $searchQuery = '';

    public string $stockFilter = ''; // 'habis', 'hampir_habis', ''

    public string $expiryFilter = ''; // 'expired', 'expiring_soon', ''

    // Movement history filters
    public string $movementDateStart = '';

    public string $movementDateEnd = '';

    public string $movementObatFilter = '';

    // Opname modal
    public bool $showOpnameModal = false;

    public ?int $opnameObatId = null;

    public string $opnameObatName = '';

    public int $opnameCurrentStock = 0;

    public int $opnamePhysicalQty = 0;

    public int $opnameVariance = 0;

    public string $opnameNotes = '';

    // Restock modal & form
    public bool $showRestockModal = false;

    public ?int $restockObatId = null;

    public string $restockObatQuery = '';

    public array $restockObatResults = [];

    public ?int $restockQuantity = null;

    public string $restockExpiryDate = '';

    public string $restockNotes = '';

    // Restock filters
    public string $restockDateStart = '';

    public string $restockDateEnd = '';

    public string $restockObatFilter = '';

    public string $activeTab = 'inventory'; // 'inventory' | 'movements' | 'restock'

    public function updatedOpnamePhysicalQty(): void
    {
        $this->opnameVariance = $this->opnamePhysicalQty - $this->opnameCurrentStock;
    }

    public function openOpname(int $obatId): void
    {
        $obat = MasterObat::findOrFail($obatId);
        $this->opnameObatId = $obat->id;
        $this->opnameObatName = $obat->nama_obat;
        $this->opnameCurrentStock = $obat->stok_saat_ini;
        $this->opnamePhysicalQty = $obat->stok_saat_ini;
        $this->opnameVariance = 0;
        $this->opnameNotes = '';
        $this->showOpnameModal = true;
    }

    public function submitOpname(): void
    {
        if (! $this->opnameObatId) {
            return;
        }

        $obat = MasterObat::findOrFail($this->opnameObatId);
        $previousStock = $obat->stok_saat_ini;
        $variance = $this->opnamePhysicalQty - $previousStock;

        if ($variance === 0) {
            Flux::toast(variant: 'info', text: 'Tidak ada selisih stok, tidak perlu adjustment.');
            $this->showOpnameModal = false;

            return;
        }

        $obat->update(['stok_saat_ini' => $this->opnamePhysicalQty]);

        StockMovement::create([
            'master_obat_id' => $obat->id,
            'type' => 'opname_adjustment',
            'quantity' => $variance,
            'previous_stock' => $previousStock,
            'current_stock' => $this->opnamePhysicalQty,
            'notes' => 'Opname: '.($this->opnameNotes ?: 'Penyesuaian stok fisik').'. Selisih: '.($variance > 0 ? '+'.$variance : $variance),
            'opname_date' => now()->toDateString(),
            'user_id' => Auth::id(),
        ]);

        $this->showOpnameModal = false;

        Flux::toast(variant: 'success', text: 'Stok '.$obat->nama_obat.' diperbarui. Selisih: '.($variance > 0 ? '+'.$variance : $variance));
    }

    public function openRestockModal(): void
    {
        $this->resetRestockForm();
        $this->showRestockModal = true;
    }

    public function updatedRestockObatQuery(): void
    {
        if (strlen($this->restockObatQuery) < 2) {
            $this->restockObatResults = [];

            return;
        }

        $this->restockObatResults = MasterObat::query()
            ->where('nama_obat', 'like', '%'.$this->restockObatQuery.'%')
            ->limit(10)
            ->get(['id', 'nama_obat', 'satuan', 'stok_saat_ini'])
            ->toArray();
    }

    public function selectRestockObat(int $id, string $name): void
    {
        $this->restockObatId = $id;
        $this->restockObatQuery = $name;
        $this->restockObatResults = [];
    }

    public function resetRestockForm(): void
    {
        $this->restockObatId = null;
        $this->restockObatQuery = '';
        $this->restockObatResults = [];
        $this->restockQuantity = null;
        $this->restockExpiryDate = '';
        $this->restockNotes = '';
    }

    public function submitRestock(): void
    {
        $this->validate([
            'restockObatId' => 'required|exists:master_obats,id',
            'restockQuantity' => 'required|integer|min:1',
            'restockExpiryDate' => 'required|date',
            'restockNotes' => 'nullable|string',
        ], [
            'restockObatId.required' => 'Pilih obat terlebih dahulu.',
            'restockQuantity.required' => 'Jumlah masuk harus diisi.',
            'restockQuantity.integer' => 'Jumlah masuk harus berupa angka.',
            'restockQuantity.min' => 'Jumlah masuk harus lebih besar dari 0.',
            'restockExpiryDate.required' => 'Tanggal kadaluarsa harus diisi.',
            'restockExpiryDate.date' => 'Format tanggal kadaluarsa tidak valid.',
        ]);

        DB::transaction(function () {
            $obat = MasterObat::lockForUpdate()->findOrFail($this->restockObatId);
            $previousStock = $obat->stok_saat_ini;
            $newStock = $previousStock + $this->restockQuantity;

            $obat->update([
                'stok_saat_ini' => $newStock,
                'tanggal_kadaluarsa' => $this->restockExpiryDate,
            ]);

            StockMovement::create([
                'master_obat_id' => $obat->id,
                'type' => 'in',
                'quantity' => $this->restockQuantity,
                'previous_stock' => $previousStock,
                'current_stock' => $newStock,
                'notes' => $this->restockNotes ?: 'Penerimaan Stok Baru',
                'user_id' => Auth::id(),
            ]);
        });

        $this->showRestockModal = false;
        $this->resetRestockForm();

        Flux::toast(variant: 'success', text: 'Stok obat berhasil ditambahkan.');
    }

    public function render()
    {
        // Inventory tab data
        $medicines = MasterObat::query()
            ->when($this->searchQuery, fn ($q) => $q->where('nama_obat', 'like', '%'.$this->searchQuery.'%'))
            ->when($this->stockFilter === 'habis', fn ($q) => $q->where('stok_saat_ini', '<=', 0))
            ->when($this->stockFilter === 'hampir_habis', fn ($q) => $q->where('stok_saat_ini', '>', 0)->whereColumn('stok_saat_ini', '<=', 'stok_minimal'))
            ->when($this->expiryFilter === 'expired', fn ($q) => $q->whereNotNull('tanggal_kadaluarsa')->where('tanggal_kadaluarsa', '<', now()))
            ->when($this->expiryFilter === 'expiring_soon', fn ($q) => $q->whereNotNull('tanggal_kadaluarsa')->where('tanggal_kadaluarsa', '>=', now())->where('tanggal_kadaluarsa', '<=', now()->addMonths(6)))
            ->orderBy('nama_obat')
            ->paginate(20, pageName: 'inv');

        // Movement history tab data
        $movements = StockMovement::with(['masterObat', 'user'])
            ->when($this->movementDateStart, fn ($q) => $q->whereDate('created_at', '>=', $this->movementDateStart))
            ->when($this->movementDateEnd, fn ($q) => $q->whereDate('created_at', '<=', $this->movementDateEnd))
            ->when($this->movementObatFilter, function ($q) {
                $q->whereHas('masterObat', fn ($sub) => $sub->where('nama_obat', 'like', '%'.$this->movementObatFilter.'%'));
            })
            ->latest()
            ->paginate(20, pageName: 'mov');

        // Restock history tab data
        $restocks = StockMovement::with(['masterObat', 'user'])
            ->where('type', 'in')
            ->when($this->restockDateStart, fn ($q) => $q->whereDate('created_at', '>=', $this->restockDateStart))
            ->when($this->restockDateEnd, fn ($q) => $q->whereDate('created_at', '<=', $this->restockDateEnd))
            ->when($this->restockObatFilter, function ($q) {
                $q->whereHas('masterObat', fn ($sub) => $sub->where('nama_obat', 'like', '%'.$this->restockObatFilter.'%'));
            })
            ->latest()
            ->paginate(20, pageName: 'rst');

        return view('components.layanan.⚡farmasi-stok.farmasi-stok', [
            'medicines' => $medicines,
            'movements' => $movements,
            'restocks' => $restocks,
        ])->layout('layouts::app');
    }
};
