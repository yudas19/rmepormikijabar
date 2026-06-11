<div class="py-6 px-6 space-y-6">

    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl" class="font-extrabold tracking-tight">MANAJEMEN STOK & OPNAME</flux:heading>
                    <flux:badge color="emerald" size="md">Farmasi</flux:badge>
                </div>
                <flux:subheading class="mt-1 font-medium">Monitoring inventaris obat, status kadaluarsa, dan stock opname.</flux:subheading>
            </div>
            <a href="{{ route('layanan.farmasi') }}">
                <flux:button variant="ghost" icon="arrow-left" size="sm">Kembali ke Antrian Farmasi</flux:button>
            </a>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-2">
        <flux:button variant="{{ $activeTab === 'inventory' ? 'primary' : 'ghost' }}" icon="archive-box" wire:click="$set('activeTab', 'inventory')">Inventaris Obat</flux:button>
        <flux:button variant="{{ $activeTab === 'movements' ? 'primary' : 'ghost' }}" icon="arrow-path" wire:click="$set('activeTab', 'movements')">Riwayat Pergerakan Stok</flux:button>
        <flux:button variant="{{ $activeTab === 'restock' ? 'primary' : 'ghost' }}" icon="plus-circle" wire:click="$set('activeTab', 'restock')">Penerimaan Stok (Restock)</flux:button>
    </div>

    {{-- ═══════ TAB 1: INVENTORY ═══════ --}}
    @if ($activeTab === 'inventory')
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-52">
                <flux:input wire:model.live.debounce.300ms="searchQuery" placeholder="Cari nama obat..." icon="magnifying-glass" size="sm" />
            </div>
            <div>
                <flux:select wire:model.live="stockFilter" class="min-w-36" size="sm">
                    <flux:select.option value="">Semua Stok</flux:select.option>
                    <flux:select.option value="habis">🔴 Habis (Stok = 0)</flux:select.option>
                    <flux:select.option value="hampir_habis">🟡 Hampir Habis</flux:select.option>
                </flux:select>
            </div>
            <div>
                <flux:select wire:model.live="expiryFilter" class="min-w-44" size="sm">
                    <flux:select.option value="">Semua Kadaluarsa</flux:select.option>
                    <flux:select.option value="expired">❌ Sudah Kadaluarsa</flux:select.option>
                    <flux:select.option value="expiring_soon">⚠️ Hampir Kadaluarsa (≤6 bln)</flux:select.option>
                </flux:select>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-950/40 border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Nama Obat</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Satuan</th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Stok</th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Min.</th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Status Stok</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Kadaluarsa</th>
                        <th class="px-4 py-3 text-right font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Harga Jual</th>
                        <th class="px-4 py-3 text-center font-semibold text-zinc-600 dark:text-zinc-400 text-xs uppercase tracking-wider">Opname</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @forelse ($medicines as $obat)
                    @php
                        $rowBg = match($obat->stock_status) {
                            'habis' => 'bg-red-50/60 dark:bg-red-950/15',
                            'hampir_habis' => 'bg-amber-50/40 dark:bg-amber-950/10',
                            default => '',
                        };
                        $expiryStatus = $obat->expiry_status;
                    @endphp
                    <tr wire:key="obat-{{ $obat->id }}" class="{{ $rowBg }}">
                        <td class="px-4 py-3 font-mono text-xs text-zinc-500">{{ $obat->kode_obat ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $obat->nama_obat }}</div>
                            @if ($obat->kode_kfa)
                            <div class="text-[10px] text-zinc-400 font-mono mt-0.5">KFA: {{ $obat->kode_kfa }}</div>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $obat->satuan }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="font-bold font-mono text-lg {{ $obat->stock_status === 'habis' ? 'text-red-600' : ($obat->stock_status === 'hampir_habis' ? 'text-amber-600' : 'text-zinc-900 dark:text-white') }}">
                                {{ $obat->stok_saat_ini }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center font-mono text-xs text-zinc-500">{{ $obat->stok_minimal }}</td>
                        <td class="px-4 py-3 text-center">
                            <flux:badge color="{{ $obat->stock_status_color }}" size="sm" class="font-semibold">{{ $obat->stock_status_label }}</flux:badge>
                        </td>
                        <td class="px-4 py-3">
                            @if ($obat->tanggal_kadaluarsa)
                            <div class="font-mono text-xs {{ $expiryStatus === 'expired' ? 'text-red-600 font-bold' : ($expiryStatus === 'expiring_soon' ? 'text-amber-600 font-semibold' : 'text-zinc-600') }}">
                                {{ $obat->tanggal_kadaluarsa->format('d-m-Y') }}
                            </div>
                            @if ($expiryStatus === 'expired')
                            <flux:badge color="red" size="sm" class="mt-0.5 text-[9px]">EXPIRED</flux:badge>
                            @elseif ($expiryStatus === 'expiring_soon')
                            <flux:badge color="amber" size="sm" class="mt-0.5 text-[9px]">Hampir Expired</flux:badge>
                            @endif
                            @else
                            <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700 dark:text-emerald-400 text-xs">
                            Rp {{ number_format($obat->harga_jual, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <flux:button variant="ghost" icon="pencil-square" size="sm" wire:click="openOpname({{ $obat->id }})">Opname</flux:button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-4 py-16 text-center text-zinc-400">
                            <flux:icon.archive-box class="w-10 h-10 mx-auto mb-2 text-zinc-300" />
                            <div class="text-sm font-semibold">Tidak ada data obat ditemukan.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($medicines->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $medicines->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════ TAB 2: MOVEMENT HISTORY ═══════ --}}
    @if ($activeTab === 'movements')
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        {{-- Filters --}}
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Periode Awal</label>
                <flux:input type="date" wire:model.live="movementDateStart" size="sm" />
            </div>
            <div>
                <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Periode Akhir</label>
                <flux:input type="date" wire:model.live="movementDateEnd" size="sm" />
            </div>
            <div class="flex-1 min-w-48">
                <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Filter Obat</label>
                <flux:input wire:model.live.debounce.300ms="movementObatFilter" placeholder="Cari nama obat..." icon="magnifying-glass" size="sm" />
            </div>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Nama Obat</flux:table.column>
                <flux:table.column>Tipe</flux:table.column>
                <flux:table.column>Qty</flux:table.column>
                <flux:table.column>Stok Sebelum</flux:table.column>
                <flux:table.column>Stok Sesudah</flux:table.column>
                <flux:table.column>Keterangan</flux:table.column>
                <flux:table.column>Petugas</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($movements as $mov)
                <flux:table.row wire:key="mov-{{ $mov->id }}">
                    <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $mov->created_at->format('d-m-Y H:i') }}</flux:table.cell>
                    <flux:table.cell class="font-semibold text-zinc-900 dark:text-white">{{ $mov->masterObat?->nama_obat ?? '-' }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="{{ $mov->type_color }}" size="sm" class="font-semibold">{{ $mov->type_label }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="font-bold font-mono {{ $mov->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $mov->quantity > 0 ? '+'.$mov->quantity : $mov->quantity }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $mov->previous_stock }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $mov->current_stock }}</flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-300 max-w-xs truncate">{{ $mov->notes ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-500">{{ $mov->user?->name ?? '-' }}</flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-16">
                        <flux:icon.arrow-path class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-2" />
                        <div class="text-sm font-semibold text-zinc-400">Belum ada riwayat pergerakan stok.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($movements->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $movements->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- ═══════ TAB 3: RESTOCK HISTORY ═══════ --}}
    @if ($activeTab === 'restock')
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
        {{-- Filters & Add Button --}}
        <div class="p-4 border-b border-zinc-100 dark:border-zinc-800 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
            <div class="flex flex-wrap gap-3 items-end w-full md:w-auto">
                <div>
                    <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Periode Awal</label>
                    <flux:input type="date" wire:model.live="restockDateStart" size="sm" />
                </div>
                <div>
                    <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Periode Akhir</label>
                    <flux:input type="date" wire:model.live="restockDateEnd" size="sm" />
                </div>
                <div class="flex-1 min-w-48">
                    <label class="text-xs font-semibold text-zinc-500 uppercase tracking-wider block mb-1">Filter Obat</label>
                    <flux:input wire:model.live.debounce.300ms="restockObatFilter" placeholder="Cari nama obat..." icon="magnifying-glass" size="sm" />
                </div>
            </div>
            <flux:button variant="primary" icon="plus" size="sm" wire:click="openRestockModal" class="w-full md:w-auto">
                Tambah Stok Masuk
            </flux:button>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column>Nama Obat</flux:table.column>
                <flux:table.column>Jumlah Masuk</flux:table.column>
                <flux:table.column>Stok Sebelum</flux:table.column>
                <flux:table.column>Stok Sesudah</flux:table.column>
                <flux:table.column>Tanggal Kadaluarsa Baru</flux:table.column>
                <flux:table.column>Catatan</flux:table.column>
                <flux:table.column>Petugas</flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($restocks as $res)
                <flux:table.row wire:key="restock-{{ $res->id }}">
                    <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $res->created_at->format('d-m-Y H:i') }}</flux:table.cell>
                    <flux:table.cell class="font-semibold text-zinc-900 dark:text-white">{{ $res->masterObat?->nama_obat ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="font-bold font-mono text-green-600">
                        +{{ $res->quantity }}
                    </flux:table.cell>
                    <flux:table.cell class="font-mono text-xs text-zinc-500">{{ $res->previous_stock }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-xs font-bold text-zinc-800 dark:text-zinc-200">{{ $res->current_stock }}</flux:table.cell>
                    <flux:table.cell class="font-mono text-xs">
                        {{ $res->masterObat?->tanggal_kadaluarsa?->format('d-m-Y') ?? '-' }}
                    </flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-600 dark:text-zinc-300 max-w-xs truncate">{{ $res->notes ?? '-' }}</flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-500">{{ $res->user?->name ?? '-' }}</flux:table.cell>
                </flux:table.row>
                @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="text-center py-16">
                        <flux:icon.arrow-path class="w-10 h-10 mx-auto text-zinc-300 dark:text-zinc-600 mb-2" />
                        <div class="text-sm font-semibold text-zinc-400">Belum ada riwayat penerimaan stok.</div>
                    </flux:table.cell>
                </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        @if ($restocks->hasPages())
        <div class="px-6 py-4 border-t border-zinc-100 dark:border-zinc-800">
            {{ $restocks->links() }}
        </div>
        @endif
    </div>
    @endif

    {{-- Restock Modal --}}
    <flux:modal wire:model.self="showRestockModal" class="max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg" class="font-bold">Penerimaan Stok Masuk (Restock)</flux:heading>
            <flux:subheading>Masukkan informasi stok obat baru yang diterima.</flux:subheading>

            {{-- Medicine Selection --}}
            <div class="space-y-2 relative">
                <flux:input
                    wire:model.live.debounce.250ms="restockObatQuery"
                    label="Cari & Pilih Obat"
                    placeholder="Ketik nama obat..."
                    icon="magnifying-glass"
                />

                @if (count($restockObatResults) > 0)
                <div wire:key="restock-drug-dropdown" class="absolute z-50 left-0 right-0 mt-1 max-h-60 overflow-y-auto bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-lg shadow-lg">
                    @foreach ($restockObatResults as $res)
                    <button type="button"
                        wire:click="selectRestockObat({{ $res['id'] }}, '{{ addslashes($res['nama_obat']) }}')"
                        class="w-full text-left px-4 py-2.5 hover:bg-zinc-50 dark:hover:bg-zinc-850 border-b border-zinc-100 dark:border-zinc-800/80 text-xs flex justify-between items-center"
                    >
                        <span class="font-bold text-zinc-900 dark:text-white">{{ $res['nama_obat'] }}</span>
                        <flux:badge size="sm" color="zinc">{{ $res['satuan'] }} (Stok: {{ $res['stok_saat_ini'] }})</flux:badge>
                    </button>
                    @endforeach
                </div>
                @endif

                @error('restockObatId')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            @if ($restockObatId)
            @php
                $selectedObat = \App\Models\MasterObat::find($restockObatId);
            @endphp
            @if ($selectedObat)
            <div class="bg-zinc-50 dark:bg-zinc-950 rounded-lg p-4 space-y-2 text-sm border border-zinc-100 dark:border-zinc-800/50">
                <div class="flex justify-between">
                    <span class="text-zinc-500 font-semibold">Stok Saat Ini:</span>
                    <span class="font-bold font-mono text-zinc-900 dark:text-white">{{ $selectedObat->stok_saat_ini }}</span>
                </div>
            </div>
            @endif
            @endif

            <flux:input
                type="number"
                wire:model="restockQuantity"
                label="Jumlah Masuk (Quantity)"
                min="1"
                placeholder="Contoh: 100"
            />

            <flux:input
                type="date"
                wire:model="restockExpiryDate"
                label="Tanggal Kadaluarsa Batch Baru"
            />

            <flux:textarea
                wire:model="restockNotes"
                label="Catatan / Keterangan (Opsional)"
                placeholder="Contoh: Beli di apotek luar, PBF Kalbe, dll."
                rows="2"
            />

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" wire:click="$set('showRestockModal', false)">Batal</flux:button>
                <flux:button variant="primary" icon="check" wire:click="submitRestock" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitRestock">Simpan Stok Masuk</span>
                    <span wire:loading wire:target="submitRestock">Menyimpan...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Opname Modal --}}
    <flux:modal wire:model.self="showOpnameModal" class="max-w-lg">
        <div class="space-y-5">
            <flux:heading size="lg" class="font-bold">Stock Opname — Penyesuaian Stok Fisik</flux:heading>

            <div class="bg-zinc-50 dark:bg-zinc-950 rounded-lg p-4 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 font-semibold">Nama Obat</span>
                    <span class="font-bold text-zinc-900 dark:text-white">{{ $opnameObatName }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 font-semibold">Stok Sistem Saat Ini</span>
                    <span class="font-bold font-mono text-lg text-zinc-900 dark:text-white">{{ $opnameCurrentStock }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-zinc-500 font-semibold">Petugas Opname</span>
                    <span class="font-bold text-emerald-700 dark:text-emerald-300">{{ Auth::user()->name }}</span>
                </div>
            </div>

            <flux:input type="number" wire:model.live="opnamePhysicalQty" label="Jumlah Stok Fisik (Hasil Hitung Nyata)" min="0" />

            <div class="bg-{{ $opnameVariance === 0 ? 'zinc' : ($opnameVariance > 0 ? 'green' : 'red') }}-50 dark:bg-{{ $opnameVariance === 0 ? 'zinc' : ($opnameVariance > 0 ? 'green' : 'red') }}-950/20 border border-{{ $opnameVariance === 0 ? 'zinc' : ($opnameVariance > 0 ? 'green' : 'red') }}-200 dark:border-{{ $opnameVariance === 0 ? 'zinc' : ($opnameVariance > 0 ? 'green' : 'red') }}-800/40 rounded-lg p-4 text-center">
                <div class="text-xs font-semibold uppercase tracking-wider text-zinc-500 mb-1">Selisih (Variance)</div>
                <div class="text-3xl font-extrabold font-mono {{ $opnameVariance > 0 ? 'text-green-700' : ($opnameVariance < 0 ? 'text-red-700' : 'text-zinc-700') }}">
                    {{ $opnameVariance > 0 ? '+'.$opnameVariance : $opnameVariance }}
                </div>
                <div class="text-xs text-zinc-400 mt-1">
                    @if ($opnameVariance > 0) Stok fisik lebih banyak dari sistem
                    @elseif ($opnameVariance < 0) Stok fisik lebih sedikit dari sistem
                    @else Tidak ada selisih
                    @endif
                </div>
            </div>

            <flux:textarea wire:model="opnameNotes" label="Catatan Opname (Opsional)" placeholder="Misal: Ditemukan selisih karena pencatatan ganda..." rows="2" />

            <div class="flex justify-end gap-3 pt-2">
                <flux:button variant="ghost" wire:click="$set('showOpnameModal', false)">Batal</flux:button>
                <flux:button variant="primary" icon="check" wire:click="submitOpname" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submitOpname">Simpan Opname</span>
                    <span wire:loading wire:target="submitOpname">Menyimpan...</span>
                </flux:button>
            </div>
        </div>
    </flux:modal>
</div>
