<div class="py-6 px-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">KASIR & BILLING PASIEN</flux:heading>
                    <flux:badge color="indigo" size="md">Kasir</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">Proses pembayaran invoice, kalkulasi tagihan tindakan, lab, dan obat.</flux:subheading>
            </div>
            @if ($activeView === 'billing')
                <flux:button variant="ghost" icon="arrow-left" size="sm" wire:click="closeBilling">Kembali ke Antrean</flux:button>
            @endif
        </div>
    </div>

    {{-- ═══════ VIEW 1: QUEUE LIST ═══════ --}}
    @if ($activeView === 'queue')
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
            {{-- Filters --}}
            <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52">
                    <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari nama pasien atau No. RM..." icon="magnifying-glass" size="sm" />
                </div>
                <div>
                    <flux:input type="date" wire:model.live="filterDate" size="sm" class="min-w-40" />
                </div>
                <div>
                    <flux:select wire:model.live="statusFilter" class="min-w-44" size="sm">
                        <flux:select.option value="unpaid">🟡 Menunggu Pembayaran</flux:select.option>
                        <flux:select.option value="paid">🟢 Lunas (Paid)</flux:select.option>
                        <flux:select.option value="">Semua Kunjungan</flux:select.option>
                    </flux:select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>No. RM</flux:table.column>
                        <flux:table.column>Nama Pasien</flux:table.column>
                        <flux:table.column>Poliklinik</flux:table.column>
                        <flux:table.column>Dokter Pemeriksa</flux:table.column>
                        <flux:table.column>Status Tagihan</flux:table.column>
                        <flux:table.column>Invoice No.</flux:table.column>
                        <flux:table.column class="text-right">Aksi</flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @forelse ($records as $record)
                            <flux:table.row wire:key="rx-{{ $record->id }}">
                                <flux:table.cell class="font-mono text-xs font-semibold text-zinc-500">{{ $record->pasien?->no_rekam_medis ?? '-' }}</flux:table.cell>
                                <flux:table.cell class="font-bold text-zinc-900 dark:text-white">{{ $record->pasien?->nama_pasien ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge color="zinc" size="sm" class="font-semibold">{{ $record->poli?->nama_poli ?? '-' }}</flux:badge>
                                </flux:table.cell>
                                <flux:table.cell class="text-zinc-650 dark:text-zinc-300 text-xs">{{ $record->pendaftaran?->dokter?->nama_petugas ?? '-' }}</flux:table.cell>
                                <flux:table.cell>
                                    @if ($record->status === 'completed_all')
                                        <flux:badge color="green" size="sm" class="font-bold">LUNAS</flux:badge>
                                    @else
                                        <flux:badge color="amber" size="sm" class="font-bold">BELUM BAYAR</flux:badge>
                                    @endif
                                </flux:table.cell>
                                <flux:table.cell class="font-mono text-xs">
                                    {{ $record->invoice?->invoice_number ?? '-' }}
                                </flux:table.cell>
                                <flux:table.cell class="text-right">
                                    @if ($record->status === 'completed_all')
                                        <flux:badge color="zinc" size="sm" class="font-medium">Selesai</flux:badge>
                                    @else
                                        <flux:button variant="primary" size="sm" icon="credit-card" wire:click="selectRecord({{ $record->id }})">
                                            Proses Bayar
                                        </flux:button>
                                    @endif
                                </flux:table.cell>
                            </flux:table.row>
                        @empty
                            <flux:table.row>
                                <flux:table.cell colspan="7" class="text-center py-16 text-zinc-400">
                                    <flux:icon.credit-card class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-650 mb-3" />
                                    <div class="text-sm font-semibold">Tidak ada antrean billing kasir saat ini.</div>
                                    <div class="text-xs text-zinc-350 dark:text-zinc-550 mt-1">Antrean billing kasir akan muncul setelah proses poli / dispensing apotek selesai.</div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforelse
                    </flux:table.rows>
                </flux:table>
            </div>

            @if ($records->hasPages())
                <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════ VIEW 2: BILLING PROCESS ═══════ --}}
    @if ($activeView === 'billing' && $selectedRecord)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Column 1: Detailed Items Breakdown (Left 2/3) --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm space-y-4">
                    <flux:heading size="lg" class="font-bold flex items-center gap-2">
                        <flux:icon.document-text class="w-5 h-5 text-indigo-500" />
                        Rincian Tagihan Medis
                    </flux:heading>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 bg-zinc-50 dark:bg-zinc-950/40 border border-zinc-200/50 dark:border-zinc-800/80 rounded-lg text-xs">
                        <div>
                            <span class="text-zinc-500 font-semibold block">Nama Pasien:</span>
                            <span class="font-bold text-zinc-800 dark:text-white">{{ $selectedRecord->pasien?->nama_pasien }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 font-semibold block">No. Rekam Medis:</span>
                            <span class="font-bold font-mono text-zinc-800 dark:text-white">{{ $selectedRecord->pasien?->no_rekam_medis }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 font-semibold block">Poliklinik:</span>
                            <span class="font-bold text-zinc-850 dark:text-white">{{ $selectedRecord->poli?->nama_poli }}</span>
                        </div>
                        <div>
                            <span class="text-zinc-500 font-semibold block">Cara Bayar Pendaftaran:</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-450">{{ $selectedRecord->pendaftaran?->cara_bayar }}</span>
                        </div>
                    </div>

                    {{-- Admin Fee Section --}}
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                        <div class="bg-zinc-50 dark:bg-zinc-950/20 px-4 py-2 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-500">1. Administrasi & Registrasi</span>
                            <flux:badge color="zinc" size="sm">Sistem</flux:badge>
                        </div>
                        <div class="p-4 flex justify-between items-center text-sm font-medium gap-4">
                            <div class="flex-1">
                                <span class="text-zinc-800 dark:text-zinc-200">Biaya Administrasi & Pendaftaran Pasien</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <select wire:model.live="adminFeeCaraBayar" class="text-xs rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-1 px-2">
                                    <option value="umum">Umum</option>
                                    <option value="bpjs">BPJS</option>
                                </select>
                                <span class="font-mono font-bold text-zinc-900 dark:text-white min-w-32 text-right">
                                    @if ($adminFeeCaraBayar === 'bpjs')
                                        <span class="text-[10px] text-emerald-600 dark:text-emerald-450 font-sans font-bold">[BPJS]</span> Rp 0
                                    @else
                                        Rp {{ number_format($adminFee, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Polyclinic Actions / Tindakans --}}
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                        <div class="bg-zinc-50 dark:bg-zinc-950/20 px-4 py-2 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-500">2. Tindakan & Prosedur Dokter</span>
                            <flux:badge color="indigo" size="sm">Tindakan</flux:badge>
                        </div>
                        <div class="p-4 space-y-4">
                            {{-- Autocomplete Procedure Input --}}
                            <div class="relative">
                                <flux:input
                                    wire:model.live.debounce.250ms="activeTindakanSearchQuery"
                                    placeholder="Cari & tambahkan tindakan/tarif tambahan..."
                                    icon="magnifying-glass"
                                    size="sm"
                                />

                                @if (count($tindakanSearchResults) > 0)
                                    <div class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                                        @foreach ($tindakanSearchResults as $t)
                                            <button type="button"
                                                wire:click="selectTindakan({{ $t['id'] }})"
                                                class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between items-center"
                                            >
                                                <span class="font-bold text-zinc-900 dark:text-white">{{ $t['nama_tindakan'] }} <span class="text-[10px] text-zinc-400 font-normal">({{ $t['kategori'] ?: 'Umum' }})</span></span>
                                                <span class="font-mono font-bold text-indigo-650 dark:text-indigo-400">Rp {{ number_format($t['tarif'], 0, ',', '.') }}</span>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 font-semibold text-zinc-500">
                                        <th class="text-left py-2">Nama Tindakan</th>
                                        <th class="text-center py-2">Qty</th>
                                        <th class="text-center py-2">Cara Bayar</th>
                                        <th class="text-right py-2">Harga Satuan</th>
                                        <th class="text-right py-2">Subtotal</th>
                                        <th class="text-center py-2">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850">
                                    @forelse ($selectedRecord->tindakans as $t)
                                        <tr wire:key="tindakan-{{ $t->id }}">
                                            <td class="py-2 text-zinc-800 dark:text-zinc-200 font-medium">{{ $t->nama_tindakan }}</td>
                                            <td class="text-center py-2 font-mono font-bold">{{ $t->pivot->qty }}</td>
                                            <td class="text-center py-2">
                                                <select wire:change="updateTindakanCaraBayar({{ $t->id }}, $event.target.value)" class="text-[10px] rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-0.5 px-1.5">
                                                    <option value="umum" {{ ($t->pivot->cara_bayar_item ?? 'umum') === 'umum' ? 'selected' : '' }}>Umum</option>
                                                    <option value="bpjs" {{ ($t->pivot->cara_bayar_item ?? 'umum') === 'bpjs' ? 'selected' : '' }}>BPJS</option>
                                                </select>
                                            </td>
                                            <td class="text-right py-2 font-mono">Rp {{ number_format($t->tarif, 0, ',', '.') }}</td>
                                            <td class="text-right py-2 font-mono font-bold">
                                                @if (($t->pivot->cara_bayar_item ?? 'umum') === 'bpjs')
                                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-450 font-sans font-bold">[BPJS]</span> Rp 0
                                                @else
                                                    Rp {{ number_format($t->pivot->subtotal, 0, ',', '.') }}
                                                @endif
                                            </td>
                                            <td class="text-center py-2">
                                                <flux:button variant="ghost" icon="trash" size="xs" class="text-red-500" wire:click="removeTindakan({{ $t->id }})" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-4 text-center text-zinc-450 italic">Belum ada tindakan medis yang dicatat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Laboratory Section --}}
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                        <div class="bg-zinc-50 dark:bg-zinc-950/20 px-4 py-2 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-500">3. Pemeriksaan Laboratorium</span>
                            <flux:badge color="purple" size="sm">Lab</flux:badge>
                        </div>
                        <div class="p-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 font-semibold text-zinc-500">
                                        <th class="text-left py-2">Nama Tes</th>
                                        <th class="text-center py-2">Cara Bayar</th>
                                        <th class="text-right py-2">Biaya</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850">
                                    @forelse ($labTests as $lt)
                                        <tr wire:key="lab-test-{{ $lt->id }}">
                                            <td class="py-2 text-zinc-800 dark:text-zinc-200 font-medium">{{ $lt->test_name }}</td>
                                            <td class="text-center py-2">
                                                <select wire:change="updateLabTestCaraBayar({{ $lt->id }}, $event.target.value)" class="text-[10px] rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-0.5 px-1.5">
                                                    <option value="umum" {{ ($lt->cara_bayar_item ?? 'umum') === 'umum' ? 'selected' : '' }}>Umum</option>
                                                    <option value="bpjs" {{ ($lt->cara_bayar_item ?? 'umum') === 'bpjs' ? 'selected' : '' }}>BPJS</option>
                                                </select>
                                            </td>
                                            <td class="text-right py-2 font-mono font-bold">
                                                @if (($lt->cara_bayar_item ?? 'umum') === 'bpjs')
                                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-450 font-sans font-bold">[BPJS]</span> Rp 0
                                                @else
                                                    Rp {{ number_format($lt->price, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="py-4 text-center text-zinc-450 italic">Tidak ada rincian billing tes laboratorium.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pharmacy Section --}}
                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                        <div class="bg-zinc-50 dark:bg-zinc-950/20 px-4 py-2 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-zinc-500">4. Pembelian Obat (Apotek)</span>
                            <flux:badge color="emerald" size="sm">Farmasi</flux:badge>
                        </div>
                        <div class="p-4">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800 font-semibold text-zinc-500">
                                        <th class="text-left py-2">Nama Obat</th>
                                        <th class="text-center py-2">Qty Dispensed</th>
                                        <th class="text-center py-2">Cara Bayar</th>
                                        <th class="text-right py-2">Harga Jual</th>
                                        <th class="text-right py-2">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-850">
                                    @forelse ($medicines as $m)
                                        <tr wire:key="medicine-{{ $m->id }}">
                                            <td class="py-2 text-zinc-800 dark:text-zinc-200 font-medium">{{ $m->nama_obat }}</td>
                                            <td class="text-center py-2 font-mono font-bold">{{ (int) $m->dispensed_qty }}</td>
                                            <td class="text-center py-2">
                                                <select wire:change="updateMedicineCaraBayar({{ $m->id }}, $event.target.value)" class="text-[10px] rounded border-zinc-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500 py-0.5 px-1.5">
                                                    <option value="umum" {{ ($m->cara_bayar_item ?? 'umum') === 'umum' ? 'selected' : '' }}>Umum</option>
                                                    <option value="bpjs" {{ ($m->cara_bayar_item ?? 'umum') === 'bpjs' ? 'selected' : '' }}>BPJS</option>
                                                </select>
                                            </td>
                                            <td class="text-right py-2 font-mono">Rp {{ number_format($m->harga_jual, 0, ',', '.') }}</td>
                                            <td class="text-right py-2 font-mono font-bold">
                                                @if (($m->cara_bayar_item ?? 'umum') === 'bpjs')
                                                    <span class="text-[10px] text-emerald-650 dark:text-emerald-400 font-sans font-bold">[BPJS]</span> Rp 0
                                                @else
                                                    Rp {{ number_format($m->subtotal_price, 0, ',', '.') }}
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-zinc-450 italic">Tidak ada rincian billing resep obat.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Column 2: Checkout Form & Total Calculation (Right 1/3) --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm space-y-5">
                    <flux:heading size="lg" class="font-bold flex items-center gap-2">
                        <flux:icon.credit-card class="w-5 h-5 text-indigo-500" />
                        Pembayaran Billing
                    </flux:heading>

                    {{-- Totals Summary --}}
                    <div class="bg-zinc-50 dark:bg-zinc-950/30 p-4 border border-zinc-150 dark:border-zinc-850 rounded-lg space-y-3">
                        <div class="flex justify-between text-xs text-zinc-500 font-semibold">
                            <span>Total Bruto (Gross):</span>
                            <span class="font-mono text-zinc-400 line-through">Rp {{ number_format($originalSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-zinc-500 font-semibold">
                            <span>Out-of-Pocket Subtotal:</span>
                            <span class="font-mono">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>

                        <div>
                            <flux:input
                                type="number"
                                wire:model.live="discount"
                                label="Diskon Tambahan (Rp)"
                                size="sm"
                                placeholder="0"
                            />
                        </div>

                        <div class="border-t border-dashed border-zinc-250 dark:border-zinc-800 my-2 pt-2 flex justify-between items-center">
                            <span class="text-sm font-bold text-zinc-800 dark:text-zinc-200">GRAND TOTAL:</span>
                            <span class="text-2xl font-extrabold font-mono text-indigo-700 dark:text-indigo-400">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    {{-- Form Inputs using AlpineJS client-side calculations --}}
                    <div class="space-y-4" x-data="{
                        grandTotal: @entangle('grandTotal'),
                        paymentMethod: @entangle('paymentMethod'),
                        amountTendered: @entangle('amountTendered'),
                        get changeAmount() {
                            let diff = parseFloat(this.amountTendered || 0) - parseFloat(this.grandTotal || 0);
                            return diff >= 0 ? diff : 0;
                        }
                    }">
                        <flux:select x-model="paymentMethod" label="Metode Pembayaran" placeholder="Pilih Metode...">
                            <flux:select.option value="tunai">💵 Tunai / Cash</flux:select.option>
                            <flux:select.option value="qris">📱 QRIS Digital</flux:select.option>
                            <flux:select.option value="transfer">🏦 Transfer Bank</flux:select.option>
                            <flux:select.option value="asuransi">🛡️ Asuransi Kesehatan</flux:select.option>
                        </flux:select>
                        @error('paymentMethod')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Display cash payment input under Tunai --}}
                        <div x-show="paymentMethod === 'tunai'" class="space-y-4 pt-2 border-t border-zinc-100 dark:border-zinc-850" x-transition>
                            <flux:input
                                type="number"
                                x-model.number="amountTendered"
                                label="Uang yang Diterima (Rp)"
                                placeholder="Contoh: 100000"
                            />
                            @error('amountTendered')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror

                            <div class="bg-green-50 dark:bg-green-950/20 border border-green-200 dark:border-green-800/40 rounded-lg p-3 text-center">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-zinc-500 block">Kembalian (Change)</span>
                                <span class="text-2xl font-extrabold font-mono text-green-700 dark:text-green-400" x-text="changeAmount.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).replace('IDR', 'Rp')">
                                    Rp 0
                                </span>
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <flux:button variant="ghost" class="w-1/3" wire:click="closeBilling">Batal</flux:button>
                            <flux:button variant="primary" class="flex-1" icon="check" wire:click="submitPayment" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitPayment">Simpan & Proses Bayar</span>
                                <span wire:loading wire:target="submitPayment">Menyimpan...</span>
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
