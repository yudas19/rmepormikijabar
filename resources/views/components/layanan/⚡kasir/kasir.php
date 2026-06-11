<?php

namespace App\Livewire\Layanan;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MasterTindakan;
use App\Models\MedicalRecord;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $searchQuery = '';

    public string $statusFilter = 'unpaid'; // 'unpaid' | 'paid' | ''

    public string $activeView = 'queue'; // 'queue' | 'billing'

    public ?int $selectedRecordId = null;

    public ?MedicalRecord $selectedRecord = null;

    public float $adminFee = 15000.00;

    public float $discount = 0.00;

    public string $paymentMethod = '';

    public float $amountTendered = 0.00;

    public float $changeAmount = 0.00;

    public string $activeTindakanSearchQuery = '';

    public array $tindakanSearchResults = [];

    public function mount(): void
    {
        // Default empty mount
    }

    public function selectRecord(int $recordId): void
    {
        $this->selectedRecordId = $recordId;
        $this->selectedRecord = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter', 'tindakans'])->findOrFail($recordId);

        // Migrate doctor's pendaftaran tindakans to medical_record_tindakans if none exists
        if ($this->selectedRecord->tindakans->isEmpty()) {
            $pendaftaranId = $this->selectedRecord->pendaftaran_id;
            $tindakanPasiens = DB::table('tindakan_pasiens')
                ->where('pendaftaran_id', $pendaftaranId)
                ->get();

            foreach ($tindakanPasiens as $tp) {
                $this->selectedRecord->tindakans()->attach($tp->master_tindakan_id, [
                    'qty' => $tp->jumlah,
                    'subtotal' => $tp->jumlah * $tp->tarif_penerapan,
                ]);
            }

            $this->selectedRecord->load('tindakans');
        }

        $this->discount = 0.00;
        $this->paymentMethod = '';
        $this->amountTendered = 0.00;
        $this->changeAmount = 0.00;
        $this->activeTindakanSearchQuery = '';
        $this->tindakanSearchResults = [];
        $this->activeView = 'billing';
    }

    public function closeBilling(): void
    {
        $this->selectedRecordId = null;
        $this->selectedRecord = null;
        $this->activeView = 'queue';
    }

    public function updatedActiveTindakanSearchQuery(): void
    {
        if (strlen($this->activeTindakanSearchQuery) < 2) {
            $this->tindakanSearchResults = [];

            return;
        }

        $this->tindakanSearchResults = MasterTindakan::query()
            ->where('nama_tindakan', 'like', '%'.$this->activeTindakanSearchQuery.'%')
            ->where('is_aktif', true)
            ->limit(10)
            ->get(['id', 'nama_tindakan', 'tarif', 'kategori'])
            ->toArray();
    }

    public function selectTindakan(int $tindakanId): void
    {
        if (! $this->selectedRecord) {
            return;
        }

        $tindakan = MasterTindakan::findOrFail($tindakanId);

        $pivot = DB::table('medical_record_tindakans')
            ->where('medical_record_id', $this->selectedRecordId)
            ->where('master_tindakan_id', $tindakanId)
            ->first();

        if ($pivot) {
            DB::table('medical_record_tindakans')
                ->where('id', $pivot->id)
                ->update([
                    'qty' => $pivot->qty + 1,
                    'subtotal' => ($pivot->qty + 1) * $tindakan->tarif,
                    'updated_at' => now(),
                ]);
        } else {
            $this->selectedRecord->tindakans()->attach($tindakanId, [
                'qty' => 1,
                'subtotal' => $tindakan->tarif,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->selectedRecord->load('tindakans');
        $this->activeTindakanSearchQuery = '';
        $this->tindakanSearchResults = [];

        Flux::toast(variant: 'success', text: 'Tindakan '.$tindakan->nama_tindakan.' ditambahkan.');
    }

    public function removeTindakan(int $tindakanId): void
    {
        if (! $this->selectedRecord) {
            return;
        }

        $this->selectedRecord->tindakans()->detach($tindakanId);
        $this->selectedRecord->load('tindakans');

        Flux::toast(variant: 'info', text: 'Tindakan dihapus.');
    }

    public function getSubtotalProperty(): float
    {
        $total = $this->adminFee;

        if ($this->selectedRecord) {
            foreach ($this->selectedRecord->tindakans as $t) {
                $total += (float) $t->pivot->subtotal;
            }
        }

        if ($this->selectedRecordId) {
            // Lab charges
            $labTests = DB::table('lab_order_results')
                ->join('lab_orders', 'lab_order_results.lab_order_id', '=', 'lab_orders.id')
                ->join('master_lab_tests', 'lab_order_results.master_lab_test_id', '=', 'master_lab_tests.id')
                ->where('lab_orders.medical_record_id', $this->selectedRecordId)
                ->where('lab_orders.status', 'completed')
                ->select(DB::raw('COALESCE(lab_order_results.tariff_snapshot, master_lab_tests.tariff) as price'))
                ->get();

            foreach ($labTests as $lt) {
                $total += (float) $lt->price;
            }

            // Pharmacy charges (dispensed subtotal)
            $medicines = DB::table('medical_record_prescription_items')
                ->join('medical_record_prescriptions', 'medical_record_prescription_items.prescription_id', '=', 'medical_record_prescriptions.id')
                ->where('medical_record_prescriptions.medical_record_id', $this->selectedRecordId)
                ->where('medical_record_prescriptions.dispensing_status', 'dispensed')
                ->select('medical_record_prescription_items.subtotal_price')
                ->get();

            foreach ($medicines as $m) {
                $total += (float) $m->subtotal_price;
            }
        }

        return $total;
    }

    public function getGrandTotalProperty(): float
    {
        return max(0.00, $this->subtotal - $this->discount);
    }

    public function submitPayment(): void
    {
        $grandTotal = $this->grandTotal;

        $rules = [
            'paymentMethod' => 'required|in:tunai,qris,transfer,asuransi',
            'discount' => 'required|numeric|min:0',
        ];

        if ($this->paymentMethod === 'tunai') {
            $rules['amountTendered'] = 'required|numeric|min:'.$grandTotal;
        }

        $this->validate($rules, [
            'paymentMethod.required' => 'Metode pembayaran harus dipilih.',
            'paymentMethod.in' => 'Metode pembayaran tidak valid.',
            'amountTendered.required' => 'Jumlah uang dibayar harus diisi untuk pembayaran tunai.',
            'amountTendered.numeric' => 'Jumlah uang dibayar harus berupa angka.',
            'amountTendered.min' => 'Jumlah uang dibayar kurang dari total tagihan.',
            'discount.required' => 'Diskon harus diisi.',
            'discount.numeric' => 'Diskon harus berupa angka.',
            'discount.min' => 'Diskon tidak boleh kurang dari 0.',
        ]);

        DB::transaction(function () use ($grandTotal) {
            $today = now()->format('Ymd');
            $countToday = DB::table('invoices')
                ->whereDate('created_at', now()->toDateString())
                ->count();
            $invoiceNumber = 'INV-'.$today.'-'.str_pad($countToday + 1, 4, '0', STR_PAD_LEFT);

            $subtotal = $this->subtotal;
            $change = $this->paymentMethod === 'tunai' ? ($this->amountTendered - $grandTotal) : 0.00;

            $invoice = Invoice::create([
                'medical_record_id' => $this->selectedRecordId,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'discount' => $this->discount,
                'grand_total' => $grandTotal,
                'payment_method' => $this->paymentMethod,
                'amount_tendered' => $this->paymentMethod === 'tunai' ? $this->amountTendered : $grandTotal,
                'change_amount' => $change,
                'status' => 'paid',
                'cashier_id' => Auth::id(),
                'paid_at' => now(),
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
                ]);
            }

            // Snapshot Tindakans
            foreach ($this->selectedRecord->tindakans as $t) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'tindakan',
                    'description' => $t->nama_tindakan.' ('.($t->kategori ?: 'Tindakan').')',
                    'qty' => $t->pivot->qty,
                    'unit_price' => $t->tarif,
                    'subtotal' => $t->pivot->subtotal,
                ]);
            }

            // Snapshot Labs
            $labTests = DB::table('lab_order_results')
                ->join('lab_orders', 'lab_order_results.lab_order_id', '=', 'lab_orders.id')
                ->join('master_lab_tests', 'lab_order_results.master_lab_test_id', '=', 'master_lab_tests.id')
                ->where('lab_orders.medical_record_id', $this->selectedRecordId)
                ->where('lab_orders.status', 'completed')
                ->select('master_lab_tests.test_name', DB::raw('COALESCE(lab_order_results.tariff_snapshot, master_lab_tests.tariff) as price'))
                ->get();

            foreach ($labTests as $lt) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'lab',
                    'description' => 'Tes Lab: '.$lt->test_name,
                    'qty' => 1,
                    'unit_price' => $lt->price,
                    'subtotal' => $lt->price,
                ]);
            }

            // Snapshot Medicines
            $medicines = DB::table('medical_record_prescription_items')
                ->join('medical_record_prescriptions', 'medical_record_prescription_items.prescription_id', '=', 'medical_record_prescriptions.id')
                ->join('master_obats', 'medical_record_prescription_items.dispensed_obat_id', '=', 'master_obats.id')
                ->where('medical_record_prescriptions.medical_record_id', $this->selectedRecordId)
                ->where('medical_record_prescriptions.dispensing_status', 'dispensed')
                ->select('master_obats.nama_obat', 'medical_record_prescription_items.dispensed_qty', 'medical_record_prescription_items.subtotal_price', 'master_obats.harga_jual')
                ->get();

            foreach ($medicines as $m) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'item_type' => 'obat',
                    'description' => 'Obat: '.$m->nama_obat,
                    'qty' => (int) $m->dispensed_qty,
                    'unit_price' => $m->harga_jual,
                    'subtotal' => $m->subtotal_price,
                ]);
            }

            $this->selectedRecord->update(['status' => 'completed_all']);

            if ($this->selectedRecord->pendaftaran) {
                $this->selectedRecord->pendaftaran->update(['status_antrean' => 'selesai']);
            }
        });

        $this->activeView = 'queue';
        $this->selectedRecordId = null;
        $this->selectedRecord = null;

        Flux::toast(variant: 'success', text: 'Pembayaran berhasil diproses.');
    }

    public function render()
    {
        $query = MedicalRecord::with(['pasien', 'poli', 'pendaftaran.dokter', 'invoice'])
            ->where(function ($q) {
                // Done polyclinic
                $q->where('status', 'completed')
                    ->orWhere('status', 'completed_all');
            })
            // Check matching criteria for fully finished journey
            ->whereDoesntHave('labOrders', function ($q) {
                $q->whereIn('status', ['pending', 'processing']);
            })
            ->whereDoesntHave('prescriptions', function ($q) {
                $q->where('dispensing_status', 'waiting');
            })
            ->when($this->searchQuery, function ($q) {
                $q->whereHas('pasien', function ($sub) {
                    $sub->where('nama_pasien', 'like', '%'.$this->searchQuery.'%')
                        ->orWhere('no_rekam_medis', 'like', '%'.$this->searchQuery.'%');
                });
            });

        if ($this->statusFilter === 'unpaid') {
            $query->where('status', '!=', 'completed_all');
        } elseif ($this->statusFilter === 'paid') {
            $query->where('status', 'completed_all');
        }

        $records = $query->latest()->paginate(20);

        $labTests = [];
        $medicines = [];
        if ($this->selectedRecordId) {
            $labTests = DB::table('lab_order_results')
                ->join('lab_orders', 'lab_order_results.lab_order_id', '=', 'lab_orders.id')
                ->join('master_lab_tests', 'lab_order_results.master_lab_test_id', '=', 'master_lab_tests.id')
                ->where('lab_orders.medical_record_id', $this->selectedRecordId)
                ->where('lab_orders.status', 'completed')
                ->select('master_lab_tests.test_name', DB::raw('COALESCE(lab_order_results.tariff_snapshot, master_lab_tests.tariff) as price'))
                ->get();

            $medicines = DB::table('medical_record_prescription_items')
                ->join('medical_record_prescriptions', 'medical_record_prescription_items.prescription_id', '=', 'medical_record_prescriptions.id')
                ->join('master_obats', 'medical_record_prescription_items.dispensed_obat_id', '=', 'master_obats.id')
                ->where('medical_record_prescriptions.medical_record_id', $this->selectedRecordId)
                ->where('medical_record_prescriptions.dispensing_status', 'dispensed')
                ->select('master_obats.nama_obat', 'medical_record_prescription_items.dispensed_qty', 'medical_record_prescription_items.subtotal_price', 'master_obats.harga_jual')
                ->get();
        }

        return view('components.layanan.⚡kasir.kasir', [
            'records' => $records,
            'subtotal' => $this->subtotal,
            'grandTotal' => $this->grandTotal,
            'labTests' => $labTests,
            'medicines' => $medicines,
        ])->layout('layouts::app');
    }
};
