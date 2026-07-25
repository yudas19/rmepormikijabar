<div class="py-6 bg-slate-50 dark:bg-slate-950 min-h-screen space-y-6">
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
        <!-- Gradient Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-500 to-sky-600 p-6">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-white/5 blur-2xl"></div>
            <div class="relative z-10">
                <flux:heading size="xl" class="font-black text-white">Pendaftaran & Admisi Pasien</flux:heading>
                <flux:subheading class="mt-1 text-blue-100/80">Kelola data rekam medis pasien, registrasi kunjungan rawat jalan, dan dokumen medis secara terintegrasi.</flux:subheading>
            </div>
        </div>

        <div class="p-6">
        <div class="rounded-full bg-gradient-to-r from-blue-500 to-sky-500 px-5 py-2.5 mb-6 w-fit shadow-md shadow-blue-500/20">
            <flux:subheading class="font-black text-white text-center w-fit">Halo {{ Auth::user()->name ?? "User" }} 👋</flux:subheading>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pasien, NIK, atau No Rekam Medis..." icon="magnifying-glass" class="w-full max-w-md focus:border-blue-500" />
            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <flux:button variant="filled" icon="calendar" wire:click="$set('showBookingList', {{ $showBookingList ? 'false' : 'true' }})" class="font-semibold text-slate-800 dark:text-slate-200">
                    {{ $showBookingList ? 'Sembunyikan Booking' : 'Lihat Daftar Booking' }}
                </flux:button>
                <flux:button variant="primary" icon="user-plus" wire:click="openAddPatient" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 border-none">
                    + Tambah Pasien Baru
                </flux:button>
            </div>
        </div>

        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
            <flux:table>
                <flux:table.columns class="bg-gradient-to-r from-slate-200 to-slate-300 text-white">
                    <flux:table.column sortable :sorted="$sortField === 'no_rekam_medis'" :direction="$sortDirection" wire:click="sortBy('no_rekam_medis')">No. RM</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'nama_pasien'" :direction="$sortDirection" wire:click="sortBy('nama_pasien')">Nama Pasien</flux:table.column>
                    <flux:table.column sortable :sorted="$sortField === 'nik'" :direction="$sortDirection" wire:click="sortBy('nik')">NIK</flux:table.column>
                    <flux:table.column>No. BPJS</flux:table.column>
                    <flux:table.column>IHS Number</flux:table.column>
                    <flux:table.column>Jenis Kelamin</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($pasiens as $pasien)
                    <flux:table.row :key="$pasien->id" class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20">
                        <flux:table.cell class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300">{{ $pasien->no_rekam_medis }}</flux:table.cell>
                        <flux:table.cell class="font-semibold text-slate-900 dark:text-white">{{ $pasien->nama_pasien }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $pasien->nik }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $pasien->no_bpjs ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($pasien->ihs_number)
                            <flux:badge color="emerald" size="sm" class="font-mono text-xs shadow-xs">{{ $pasien->ihs_number }}</flux:badge>
                            @else
                            <flux:badge color="red" size="sm" class="animate-pulse">IHS Not Synced</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-slate-600 dark:text-slate-400">{{ $pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" class="hover:bg-slate-100 dark:hover:bg-slate-800" />
                                <flux:menu class="border border-slate-200 dark:border-slate-800 shadow-xl rounded-xl">
                                    <flux:menu.item icon="pencil-square" wire:click="editPatient({{ $pasien->id }})" class="hover:text-emerald-500">Edit Profil Pasien</flux:menu.item>
                                    <flux:menu.separator class="border-slate-100 dark:border-slate-800" />
                                    <flux:menu.item icon="building-office-2" wire:click="openRegisterOutpatient({{ $pasien->id }})" class="font-semibold text-slate-900 dark:text-white hover:text-emerald-500">Registrasi Pasien Rawat Jalan</flux:menu.item>
                                    <flux:menu.item icon="calendar" wire:click="openBookingModal({{ $pasien->id }})" class="font-semibold text-slate-900 dark:text-white hover:text-emerald-500">Booking Pendaftaran</flux:menu.item>
                                    <flux:menu.separator class="border-slate-100 dark:border-slate-800" />
                                    <flux:menu.item icon="document-text" wire:click="openConsentModal({{ $pasien->id }}, 'general_consent')">Persetujuan Umum</flux:menu.item>
                                    <flux:menu.item icon="document-check" wire:click="openConsentModal({{ $pasien->id }}, 'informed_consent_tindakan')">Persetujuan Tindakan Medis</flux:menu.item>
                                    <flux:menu.item icon="document-arrow-up" wire:click="openReferralModal({{ $pasien->id }})">Surat Rujukan</flux:menu.item>
                                    <flux:menu.separator class="border-slate-100 dark:border-slate-800" />
                                    <flux:menu.item icon="pencil-square" wire:click="openCertificateModal({{ $pasien->id }}, 'sehat')">Surat Keterangan Sehat</flux:menu.item>
                                    <flux:menu.item icon="pencil-square" wire:click="openCertificateModal({{ $pasien->id }}, 'sakit')">Surat Keterangan Sakit</flux:menu.item>
                                    <flux:menu.item icon="pencil-square" wire:click="openCertificateModal({{ $pasien->id }}, 'bebas_narkoba')">Surat Keterangan Bebas Narkoba</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-slate-400 py-8 italic">Tidak ada data pasien ditemukan.</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <div class="mt-4">
            {{ $pasiens->links() }}
        </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
        <!-- Gradient Header for Queue -->
        <div class="relative overflow-hidden bg-gradient-to-br from-sky-500 to-blue-600 p-6">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-white/5 blur-2xl"></div>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 relative z-10">
                <div>
                    <flux:heading size="lg" class="font-extrabold text-white">Antrean Kunjungan</flux:heading>
                    <flux:subheading class="mt-1 text-emerald-100/80">Daftar pasien yang terdaftar di poliklinik.</flux:subheading>
                </div>
                <div class="flex gap-3 w-full sm:w-auto">
                    <flux:input type="date" wire:model.live="filterStartDate" size="sm" label="Mulai" class="w-full sm:w-36 bg-white/10 border-white/20 text-white" />
                    <flux:input type="date" wire:model.live="filterEndDate" size="sm" label="Sampai" class="w-full sm:w-36 bg-white/10 border-white/20 text-white" />
                </div>
            </div>
        </div>

        <div class="p-6">
        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
            <flux:table>
                <flux:table.columns class="bg-gradient-to-r from-slate-200 to-slate-300 text-white">
                    <flux:table.column>No. Antrean</flux:table.column>
                    <flux:table.column>No. RM / Pasien</flux:table.column>
                    <flux:table.column>Poliklinik</flux:table.column>
                    <flux:table.column>Dokter</flux:table.column>
                    <flux:table.column>Cara Bayar</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($todayQueues as $q)
                    <flux:table.row :key="$q->id" class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20">
                        <flux:table.cell>
                            <flux:badge color="slate" size="md" class="font-mono text-sm font-black px-2.5 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">{{ $q->nomor_antrean }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $q->pasien->nama_pasien }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $q->pasien->no_rekam_medis }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-semibold text-slate-800 dark:text-slate-200">
                            {{ $q->poliklinik_type === 'umum' ? 'Poli Umum' : ($q->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'KIA') }}
                        </flux:table.cell>
                        <flux:table.cell class="text-slate-600 dark:text-slate-400">{{ $q->pendaftaran?->dokter?->nama_petugas ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $q->pendaftaran?->cara_bayar === 'BPJS' ? 'emerald' : 'slate' }}" size="sm" class="font-medium">{{ $q->pendaftaran?->cara_bayar ?? 'Umum' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                            $statusColors = [
                                'waiting' => 'slate',
                                'anamnesis' => 'orange',
                                'waiting_doctor' => 'amber',
                                'examination' => 'indigo',
                                'completed' => 'emerald',
                            ];
                            $statusNames = [
                                'waiting' => 'Menunggu',
                                'anamnesis' => 'Anamnesis',
                                'waiting_doctor' => 'Menunggu Dokter',
                                'examination' => 'Pemeriksaan',
                                'completed' => 'Selesai',
                            ];
                            $color = $statusColors[$q->status] ?? 'slate';
                            $name = $statusNames[$q->status] ?? $q->status;
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm" class="font-bold shadow-xs">{{ $name }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="flex items-center gap-2">
                            @if ($q->status !== 'completed' && $q->status !== 'completed_all')
                                @php
                                    $sudahDipanggil = in_array($q->status_panggilan, ['memanggil', 'selesai']);
                                @endphp
                                @if ($sudahDipanggil)
                                    <flux:button variant="ghost" icon="speaker-wave" size="sm" class="text-green-600 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-950/40 rounded-lg" wire:click="panggilAntrean({{ $q->id }})" title="Panggil Ulang Pasien">
                                        Panggil Ulang
                                    </flux:button>
                                @else
                                    <flux:button variant="ghost" icon="speaker-wave" size="sm" class="text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-lg" wire:click="panggilAntrean({{ $q->id }})" title="Panggil Pasien">
                                        Panggil
                                    </flux:button>
                                @endif
                            @endif
                            <flux:button variant="ghost" icon="printer" size="sm" class="hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400" href="{{ route('print.queue-ticket', ['id' => $q->id]) }}" target="_blank" title="Cetak Ulang Tiket" />
                            <flux:button size="sm" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg shadow-xs" href="{{ route('medical-record.examine', ['poliklinik' => $q->poliklinik_type, 'encounter_id' => $q->encounter_id]) }}" wire:navigate>
                                Periksa
                            </flux:button>
                            <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg" wire:click="confirmCancel({{ $q->id }})" title="Batalkan Kunjungan" />
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-slate-400 py-8 italic">Belum ada antrean kunjungan untuk hari ini.</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        </div>
    </div>

    @if ($showBookingList)
    {{-- === DAFTAR BOOKING PENDAFTARAN === --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 rounded-2xl shadow-sm overflow-hidden">
        <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-teal-600 p-6">
            <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -left-10 -bottom-10 h-40 w-40 rounded-full bg-white/5 blur-2xl"></div>
            <div class="relative z-10 flex justify-between items-center">
                <div>
                    <flux:heading size="lg" class="font-extrabold text-white">Daftar Booking Pendaftaran</flux:heading>
                    <flux:subheading class="mt-1 text-emerald-100/80">Booking kunjungan pasien yang menunggu konfirmasi kedatangan.</flux:subheading>
                </div>
                <flux:button variant="filled" size="sm" class="bg-white/10 border-white/20 text-white hover:bg-white/20" wire:click="$set('showBookingList', false)">
                    Tutup
                </flux:button>
            </div>
        </div>

        <div class="p-6">
        <div class="border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden bg-white dark:bg-slate-900">
            <flux:table>
                <flux:table.columns class="bg-gradient-to-r from-slate-200 to-slate-300 text-white">
                    <flux:table.column>No.</flux:table.column>
                    <flux:table.column>Pasien</flux:table.column>
                    <flux:table.column>Tanggal Booking</flux:table.column>
                    <flux:table.column>Poli / Dokter</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Dibuat</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($bookings as $index => $booking)
                    <flux:table.row :key="'booking-'.$booking->id" class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20">
                        <flux:table.cell class="font-mono text-sm font-bold text-slate-600">{{ $index + 1 }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="font-bold text-slate-900 dark:text-white">{{ $booking->pasien->nama_pasien }}</div>
                            <div class="text-xs text-slate-400 font-mono">{{ $booking->pasien->no_rekam_medis }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="sky" size="sm" class="font-semibold">{{ $booking->booking_date->translatedFormat('l, d M Y') }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ $booking->poli?->nama_poli ?? '-' }}</div>
                            <div class="text-xs text-slate-400">{{ $booking->dokter?->nama_petugas ?? '-' }}</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            @if ($booking->status === 'pending')
                                <flux:badge color="amber" size="sm" class="font-bold animate-pulse">Menunggu Konfirmasi</flux:badge>
                            @elseif ($booking->status === 'confirmed')
                                <flux:badge color="emerald" size="sm" class="font-bold">Terkonfirmasi</flux:badge>
                            @else
                                <flux:badge color="red" size="sm" class="font-bold">Dibatalkan</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell class="text-xs text-slate-500">{{ $booking->created_at->format('d/m/Y H:i') }}</flux:table.cell>
                        <flux:table.cell class="flex items-center gap-2">
                            @if ($booking->status === 'pending')
                                @if ($booking->booking_date->isToday())
                                    <flux:button size="sm" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-lg shadow-xs" wire:click="confirmBooking({{ $booking->id }})">
                                        Konfirmasi Kedatangan
                                    </flux:button>
                                @else
                                    <flux:badge color="slate" size="sm" class="font-medium">Belum waktunya</flux:badge>
                                @endif
                                <flux:button variant="ghost" icon="trash" size="sm" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 rounded-lg" wire:click="cancelBooking({{ $booking->id }})" title="Batalkan Booking" />
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-slate-400 py-8 italic">Belum ada booking pendaftaran.</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
        </div>
    </div>
    @endif

    @if ($showPatientModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto flex flex-col scale-100 transition-all">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-blue-500 to-indigo-600">
                <flux:heading size="lg" class="font-black text-white">{{ $pasien_id ? 'Edit Profil Pasien' : 'Tambah Pasien Baru' }}</flux:heading>
                <button type="button" wire:click="$set('showPatientModal', false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="savePatient" class="p-6 space-y-6 flex-1">
                <div class="bg-slate-50 dark:bg-slate-950/40 p-5 border border-slate-200 dark:border-slate-800 rounded-xl space-y-3">
                    <flux:heading size="md" class="font-extrabold text-slate-900 dark:text-white">Verifikasi SatuSehat Kemenkes</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex gap-2 items-end">
                            <flux:input maxlength="16" wire:model="nik" label="NIK (16 Digit)" required placeholder="Contoh: 1234567890123456" class="flex-1 font-mono focus:border-emerald-500" />
                            <flux:button type="button" variant="filled" wire:click="verifyNik" class="h-10 bg-slate-900 text-white dark:bg-emerald-600">Verify</flux:button>
                        </div>
                        <flux:input wire:model="ihs_number" label="IHS Number (Read-Only)" readonly placeholder="Akan terisi otomatis jika NIK terverifikasi" class="font-mono bg-slate-100 dark:bg-slate-950 text-slate-500" />
                    </div>
                    @if ($showIhsWarning)
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/60 rounded-xl text-amber-800 dark:text-amber-400 text-xs flex items-center gap-2">
                        <flux:icon.exclamation-circle class="w-4 h-4 flex-shrink-0 text-amber-500" />
                        <span><strong>IHS Not Synced:</strong> NIK tidak terdaftar di SatuSehat. Data pasien dapat disimpan secara lokal.</span>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="no_rekam_medis" label="No. Rekam Medis (Editable)" required class="font-mono" />
                    <flux:input wire:model="nama_pasien" label="Nama Lengkap Pasien" required />
                    <flux:input wire:model="panggilan" label="Nama Panggilan" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input maxlength="13" wire:model="no_bpjs" label="No. Kartu BPJS (13 Digit)" class="font-mono" />
                    <div class="relative">
                        <flux:input wire:model.live.debounce.250ms="tempatLahirQuery" label="Tempat Lahir" required placeholder="Ketik nama kota/kabupaten..." />
                        @if (!empty($tempatLahirResults))
                            <div class="absolute z-50 left-0 right-0 mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                @foreach ($tempatLahirResults as $res)
                                    <div wire:click="selectTempatLahir({{ $res['id'] }}, '{{ $res['nama_kabupaten_kota'] }}')" class="px-4 py-2 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer text-sm text-slate-900 dark:text-white">
                                        {{ $res['nama_kabupaten_kota'] }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                        @error('tempat_lahir_kabupaten_id')
                            <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span>
                        @enderror
                    </div>
                    <flux:input wire:model="tanggal_lahir" type="date" label="Tanggal Lahir" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="jenis_kelamin" label="Jenis Kelamin" required>
                        <flux:select.option value="">Pilih Jenis Kelamin</flux:select.option>
                        <flux:select.option value="L">Laki-laki</flux:select.option>
                        <flux:select.option value="P">Perempuan</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="golongan_darah" label="Golongan Darah">
                        <flux:select.option value="Tidak Tahu">Tidak Tahu</flux:select.option>
                        <flux:select.option value="A">A</flux:select.option>
                        <flux:select.option value="B">B</flux:select.option>
                        <flux:select.option value="AB">AB</flux:select.option>
                        <flux:select.option value="O">O</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="gelar" label="Gelar" placeholder="Contoh: S.Pd, dr., dll." />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="nama_orangtua" label="Nama Orang Tua" />
                    <flux:input wire:model="nrp" label="NRP (TNI/POLRI/PNS)" placeholder="Contoh: 123456" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="master_agama_id" label="Agama">
                        <flux:select.option value="">Pilih Agama</flux:select.option>
                        @foreach($agamas as $agama)
                        <flux:select.option value="{{ $agama->id }}">{{ $agama->nama_agama }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="master_pendidikan_id" label="Pendidikan">
                        <flux:select.option value="">Pilih Pendidikan</flux:select.option>
                        @foreach($pendidikans as $pendidikan)
                        <flux:select.option value="{{ $pendidikan->id }}">{{ $pendidikan->nama_pendidikan }}</flux:select.option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="master_pekerjaan_id" label="Pekerjaan">
                        <flux:select.option value="">Pilih Pekerjaan</flux:select.option>
                        @foreach($pekerjaans as $pekerjaan)
                        <flux:select.option value="{{ $pekerjaan->id }}">{{ $pekerjaan->nama_pekerjaan }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input maxlength="15" wire:model="no_whatsapp" label="No. WhatsApp / HP" />
                    <flux:input wire:model="email" type="email" label="Alamat Email" />
                    <flux:select wire:model="status_pasien" label="Status Pasien" required>
                        <flux:select.option value="aktif">Aktif</flux:select.option>
                        <flux:select.option value="nonaktif">Non-Aktif</flux:select.option>
                        <flux:select.option value="meninggal">Meninggal</flux:select.option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="alamat" label="Alamat Lengkap" required rows="3" />

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4">
                    <flux:button type="button" variant="ghost" wire:click="resetForm" class="hover:bg-slate-100">Clear</flux:button>
                    <flux:button type="button" variant="filled" wire:click="$set('showPatientModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-bold px-5 border-none shadow-lg shadow-blue-500/20">Simpan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showRegisterModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-emerald-500 to-teal-600">
                <flux:heading size="lg" class="font-black text-white">Daftar Rawat Jalan Pasien</flux:heading>
                <button type="button" wire:click="$set('showRegisterModal', false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveOutpatientRegistration" class="p-6 space-y-4">
                <flux:select wire:model.live="reg_poli_id" label="Klinik / Poli Tujuan" required placeholder="Pilih Klinik">
                    @foreach ($polis as $poli)
                    <flux:select.option value="{{ $poli->id }}">{{ $poli->kode_poli }} - {{ $poli->nama_poli }}</flux:select.option>
                    @endforeach
                </flux:select>

                @if ($this->isWalkInLab)
                    <div class="space-y-2">
                        <flux:label>Pilih Pemeriksaan Laboratorium</flux:label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 border border-slate-200 dark:border-slate-800 rounded-xl p-3 max-h-60 overflow-y-auto bg-slate-50/50 dark:bg-slate-950/30">
                            @foreach ($labTests as $test)
                                <label class="flex items-center gap-2 text-sm text-slate-900 dark:text-white cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 p-1.5 rounded-lg transition-all">
                                    <input type="checkbox" wire:model.live="selectedLabTests" value="{{ $test->id }}" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500" />
                                    <span>{{ $test->test_name }}</span>
                                    <span class="text-xs text-slate-400 font-mono">
                                        (Rp {{ number_format($reg_cara_bayar === 'BPJS' ? $test->tarif_bpjs : $test->tarif_umum, 0, ',', '.') }})
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        @error('selectedLabTests')
                            <span class="text-xs text-red-500 block">{{ $message }}</span>
                        @enderror
                    </div>
                @else
                    <flux:select wire:model="reg_dokter_id" label="Dokter Pemeriksa" required placeholder="Pilih Dokter">
                        @foreach ($doctors as $doc)
                        <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                        @endforeach
                    </flux:select>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model.live="reg_cara_bayar" label="Cara Pembayaran" required>
                        <flux:select.option value="Umum">Umum</flux:select.option>
                        <flux:select.option value="BPJS">BPJS Kesehatan</flux:select.option>
                        <flux:select.option value="Dinas/Instansi">Dinas/Instansi</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="reg_jenis_kunjungan" label="Jenis Kunjungan" required>
                        <flux:select.option value="Baru">Kunjungan Baru</flux:select.option>
                        <flux:select.option value="Lama">Kunjungan Lama</flux:select.option>
                        <flux:select.option value="Kontrol">Kontrol Ulang</flux:select.option>
                    </flux:select>
                    <flux:input type="date" wire:model="reg_tanggal_kunjungan" label="Tanggal Kunjungan" required />
                </div>

                <flux:textarea wire:model="reg_keluhan_awal" label="Keluhan Utama / Alasan Kunjungan" :required="!$this->isWalkInLab" placeholder="{{ $this->isWalkInLab ? 'Pemeriksaan Lab Mandiri (Opsional)' : 'Tulis keluhan pasien...' }}" rows="3" />

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showRegisterModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold border-none shadow-lg shadow-emerald-500/20">Daftarkan Rawat Jalan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif
@if ($showBookingModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-emerald-500 to-teal-600">
                <flux:heading size="lg" class="font-black text-white">Booking Pendaftaran</flux:heading>
                <button type="button" wire:click="$set('showBookingModal', false)" class="text-white/70 hover:text-white p-1 rounded-lg hover:bg-white/10">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>
            <form wire:submit.prevent="saveBooking" class="p-6 space-y-4">
                @if ($selectedPasienId)
                    @php $bookingPasien = \App\Models\Pasien::find($selectedPasienId); @endphp
                    @if ($bookingPasien)
                    <div class="bg-slate-50 dark:bg-slate-950/40 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                        <p class="text-xs text-slate-400 mb-1">Pasien</p>
                        <p class="font-bold text-slate-900 dark:text-white">{{ $bookingPasien->nama_pasien }}</p>
                        <p class="text-xs text-slate-500 font-mono">RM: {{ $bookingPasien->no_rekam_medis }}</p>
                    </div>
                    @endif
                @endif

                {{-- Tanggal Booking --}}
                <div>
                    <flux:input type="date" wire:model.live="bookingDate" label="Tanggal Kunjungan (Booking)" required min="{{ now()->addDay()->format('Y-m-d') }}" class="w-full" />
                    <p class="text-xs text-slate-400 mt-1">Pilih tanggal kunjungan untuk hari esok atau seterusnya.</p>
                    @error('bookingDate')
                        <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pilih Poli --}}
                <div>
                    <flux:select wire:model.live="booking_poli_id" label="Poli / Klinik Tujuan" required placeholder="Pilih Poli">
                        @foreach ($bookingPolis as $poli)
                            <flux:select.option value="{{ $poli->id }}">{{ $poli->kode_poli }} - {{ $poli->nama_poli }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('booking_poli_id')
                        <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Pilih Dokter (muncul setelah poli & tanggal dipilih) --}}
                @if ($booking_poli_id && $bookingDate)
                <div>
                    @if ($this->bookingDokters->isEmpty())
                        <div class="flex items-center gap-2 p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800 rounded-xl text-amber-800 dark:text-amber-400 text-sm">
                            <flux:icon.exclamation-circle class="w-5 h-5 flex-shrink-0" />
                            <span>Tidak ada dokter yang memiliki jadwal di poli ini pada hari <strong>{{ \Carbon\Carbon::parse($bookingDate)->isoFormat('dddd') }}</strong>.</span>
                        </div>
                    @else
                        <flux:select wire:model="booking_dokter_id" label="Dokter Pemeriksa" required placeholder="Pilih Dokter">
                            @foreach ($this->bookingDokters as $dok)
                                <flux:select.option value="{{ $dok->id }}">{{ $dok->nama_petugas }} (SIP: {{ $dok->nomor_sip ?? '-' }})</flux:select.option>
                            @endforeach
                        </flux:select>
                        @error('booking_dokter_id')
                            <span class="text-xs text-red-500 block mt-1">{{ $message }}</span>
                        @enderror
                    @endif
                </div>
                @else
                <div class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-950/30 border border-slate-200 dark:border-slate-700 rounded-xl text-slate-500 text-sm">
                    <flux:icon.information-circle class="w-5 h-5 flex-shrink-0" />
                    <span>Pilih poli dan tanggal terlebih dahulu untuk melihat dokter yang tersedia.</span>
                </div>
                @endif

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4">
                    <flux:button type="button" variant="ghost" wire:click="$set('showBookingModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold">Simpan Booking</flux:button>
                </div>
            </form>
        </div>
    </div>
@endif
    @if ($showConsentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-amber-500 to-orange-600">
                <flux:heading size="lg" class="font-black text-white">
                    {{ $consent_type === 'general_consent' ? 'Buat Persetujuan Umum (General Consent)' : 'Buat Persetujuan Tindakan (Informed Consent)' }}
                </flux:heading>
                <button type="button" wire:click="$set('showConsentModal', false)" class="text-white/70 hover:text-white">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveConsent" class="p-6 space-y-4">
                <flux:input wire:model="consent_nama_penanggung_jawab" label="Nama Penanggung Jawab" required />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="consent_hubungan_penanggung_jawab" label="Hubungan Penanggung Jawab" required>
                        <flux:select.option value="diri_sendiri">Diri Sendiri</flux:select.option>
                        <flux:select.option value="suami">Suami</flux:select.option>
                        <flux:select.option value="istri">Istri</flux:select.option>
                        <flux:select.option value="ayah">Ayah</flux:select.option>
                        <flux:select.option value="ibu">Ibu</flux:select.option>
                        <flux:select.option value="anak">Anak</flux:select.option>
                        <flux:select.option value="lainnya">Lainnya</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="consent_nik_penanggung_jawab" label="NIK Penanggung Jawab (16 Digit)" class="font-mono" />
                </div>

                @if ($consent_type === 'informed_consent_tindakan')
                <flux:input wire:model="consent_nama_tindakan_medis" label="Nama Tindakan Medis yang Diusulkan" required placeholder="Contoh: Insisi Abses, Pembedahan Minor" />
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="consent_pernyataan" label="Pernyataan Persetujuan" required>
                        <flux:select.option value="setuju">MENYETUJUI Tindakan/Prosedur</flux:select.option>
                        <flux:select.option value="menolak">MENOLAK Tindakan/Prosedur</flux:select.option>
                    </flux:select>

                    <flux:select wire:model="consent_petugas_id" label="Petugas Saksi Klinik" required placeholder="Pilih Petugas">
                        @foreach ($petugass as $p)
                        <flux:select.option value="{{ $p->id }}">{{ $p->nama_petugas }} ({{ $p->jenis_petugas }})</flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showConsentModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white font-bold border-none shadow-lg shadow-amber-500/20">Simpan & Cetak PDF</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showReferralModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-purple-500 to-violet-600">
                <flux:heading size="lg" class="font-black text-white">Buat Surat Rujukan Pasien</flux:heading>
                <button type="button" wire:click="$set('showReferralModal', false)" class="text-white/70 hover:text-white">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveReferral" class="p-6 space-y-4">
                <flux:input wire:model="ref_faskes_tujuan" label="Fasilitas Kesehatan Tujuan Rujukan" required placeholder="Contoh: RSUD Karawang, Klinik Spesialis Sehat" />
                <flux:input wire:model="ref_diagnosa" label="Diagnosis Utama" required placeholder="Contoh: Hipertensi, Susp. Appendicitis" />
                <flux:textarea wire:model="ref_catatan" label="Catatan Medis / Terapi Awal" placeholder="Terapi yang diberikan, alasan rujukan, dll." rows="3" />

                <flux:select wire:model="ref_dokter_id" label="Dokter yang Merujuk" required placeholder="Pilih Dokter">
                    @foreach ($doctors as $doc)
                    <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showReferralModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white font-bold border-none shadow-lg shadow-purple-500/20">Simpan & Cetak Rujukan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showCertificateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-gradient-to-r from-cyan-500 to-sky-600">
                <flux:heading size="lg" class="font-black text-white">
                    @if ($cert_type === 'sehat')
                    Buat Surat Keterangan Sehat
                    @elseif ($cert_type === 'sakit')
                    Buat Surat Keterangan Sakit (Sick Leave)
                    @else
                    Buat Surat Keterangan Bebas Narkoba
                    @endif
                </flux:heading>
                <button type="button" wire:click="$set('showCertificateModal', false)" class="text-white/70 hover:text-white">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveCertificate" class="p-6 space-y-4">
                @if ($cert_type === 'sehat')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="cert_sehat_tinggi" label="Tinggi Badan (cm)" required type="number" />
                    <flux:input wire:model="cert_sehat_berat" label="Berat Badan (kg)" required type="number" />
                    <flux:input wire:model="cert_sehat_tensi" label="Tekanan Darah (mmHg)" required placeholder="Contoh: 120/80" />
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:select wire:model="cert_sehat_butawarna" label="Buta Warna" required>
                        <flux:select.option value="tidak">Tidak Buta Warna</flux:select.option>
                        <flux:select.option value="ya">Buta Warna</flux:select.option>
                    </flux:select>
                    <flux:input wire:model="cert_sehat_catatan" label="Catatan Dokter (Optional)" placeholder="Fisik dalam batas normal" />
                </div>
                @elseif ($cert_type === 'sakit')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:input wire:model="cert_sakit_tanggal_mulai" type="date" label="Tanggal Mulai Istirahat" required />
                    <flux:input wire:model="cert_sakit_tanggal_selesai" type="date" label="Tanggal Selesai Istirahat" required />
                </div>
                <flux:input wire:model="cert_sakit_diagnosa" label="Diagnosis (Tertera di Surat)" placeholder="Demam Akut, dyspepsia, dll." />
                @else
                <flux:input wire:model="cert_narkoba_keperluan" label="Keperluan Pembuatan Surat" required placeholder="Contoh: Persyaratan Melamar Pekerjaan" />
                <flux:textarea wire:model="cert_narkoba_hasil" label="Hasil Pemeriksaan Laboratorium Urin" required rows="3" />
                @endif

                <flux:select wire:model="cert_dokter_id" label="Dokter yang Memeriksa & Menandatangani" required placeholder="Pilih Dokter">
                    @foreach ($doctors as $doc)
                    <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="flex justify-end gap-2 border-t border-slate-200 dark:border-slate-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showCertificateModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary" class="bg-gradient-to-r from-cyan-500 to-sky-600 hover:from-cyan-600 hover:to-sky-700 text-white font-bold border-none shadow-lg shadow-cyan-500/20">Simpan & Cetak Surat</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if ($showCancelConfirmation)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
            <flux:heading size="lg" class="text-red-600 dark:text-red-400 font-black">Konfirmasi Pembatalan</flux:heading>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">Apakah Anda yakin ingin membatalkan registrasi kunjungan pasien ini? Pasien yang dibatalkan akan dihapus dari semua antrean aktif poliklinik dan penunjang.</p>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="filled" wire:click="$set('showCancelConfirmation', false)">Batal</flux:button>
                <flux:button variant="danger" class="bg-red-600 hover:bg-red-500 font-bold text-white rounded-lg" wire:click="cancelPendaftaran">Ya, Batalkan</flux:button>
            </div>
        </div>
    </div>
    @endif

    @if ($showSuccessPrintModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6 space-y-4">
            <div class="flex items-center gap-3 text-emerald-600 dark:text-emerald-400">
                <flux:icon.check-circle class="w-8 h-8 shrink-0" />
                <flux:heading size="lg" class="font-black">Dokumen Berhasil Dibuat</flux:heading>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">{{ $successPrintMessage }}</p>
            <div class="flex justify-end gap-2 pt-2">
                <flux:button variant="filled" wire:click="$set('showSuccessPrintModal', false)">Tutup</flux:button>
                <flux:button href="{{ $successPrintUrl }}" target="_blank" variant="primary" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold" wire:click="$set('showSuccessPrintModal', false)">Cetak Dokumen</flux:button>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('open-print-tab', (data) => {
                const eventData = Array.isArray(data) ? data[0] : data;
                if (eventData && eventData.url) {
                    window.open(eventData.url, '_blank');
                }
            });
        });
    </script>
</div>