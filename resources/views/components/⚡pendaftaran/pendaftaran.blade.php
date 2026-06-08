<div class="py-6">
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm">
        <div class="mb-6">
            <flux:heading size="xl">Pendaftaran & Admisi Pasien</flux:heading>
            <flux:subheading class="mt-1">Kelola data rekam medis pasien, registrasi kunjungan rawat jalan, dan dokumen medis secara terintegrasi.</flux:subheading>
        </div>

        <!-- Search & Add New Patient -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama pasien, NIK, atau No Rekam Medis..." icon="magnifying-glass" class="w-full max-w-md" />
            <flux:button variant="primary" icon="user-plus" wire:click="openAddPatient">
                + Add New Patient
            </flux:button>
        </div>

        <!-- Data Table -->
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
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
                    <flux:table.row :key="$pasien->id">
                        <flux:table.cell class="font-mono text-xs font-semibold">{{ $pasien->no_rekam_medis }}</flux:table.cell>
                        <flux:table.cell class="font-semibold text-zinc-900 dark:text-white">{{ $pasien->nama_pasien }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $pasien->nik }}</flux:table.cell>
                        <flux:table.cell class="font-mono text-xs">{{ $pasien->no_bpjs ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            @if ($pasien->ihs_number)
                            <flux:badge color="green" size="sm" class="font-mono text-xs">{{ $pasien->ihs_number }}</flux:badge>
                            @else
                            <flux:badge color="red" size="sm">IHS Not Synced</flux:badge>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>{{ $pasien->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:dropdown align="end">
                                <flux:button variant="ghost" icon="ellipsis-horizontal" size="sm" />
                                <flux:menu>
                                    <flux:menu.item icon="pencil-square" wire:click="editPatient({{ $pasien->id }})">Edit Profil Pasien</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="building-office-2" wire:click="openRegisterOutpatient({{ $pasien->id }})">Register Outpatient</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="document-text" wire:click="openConsentModal({{ $pasien->id }}, 'general_consent')">General Consent</flux:menu.item>
                                    <flux:menu.item icon="document-check" wire:click="openConsentModal({{ $pasien->id }}, 'informed_consent_tindakan')">Informed Consent</flux:menu.item>
                                    <flux:menu.item icon="document-arrow-up" wire:click="openReferralModal({{ $pasien->id }})">Referral Letter</flux:menu.item>
                                    <flux:menu.separator />
                                    <flux:menu.item icon="document" wire:click="openCertificateModal({{ $pasien->id }}, 'sehat')">Health Certificate</flux:menu.item>
                                    <flux:menu.item icon="document" wire:click="openCertificateModal({{ $pasien->id }}, 'sakit')">Sick Leave Certificate</flux:menu.item>
                                    <flux:menu.item icon="document" wire:click="openCertificateModal({{ $pasien->id }}, 'bebas_narkoba')">Drug-Free Certificate</flux:menu.item>
                                </flux:menu>
                            </flux:dropdown>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500 py-8">Tidak ada data pasien ditemukan.</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $pasiens->links() }}
        </div>
    </div>

    <!-- Antrean Hari Ini Card -->
    <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 shadow-sm mt-6">
        <div class="mb-4">
            <flux:heading size="lg">Antrean Kunjungan Hari Ini</flux:heading>
            <flux:subheading class="mt-1">Daftar pasien yang terdaftar di poliklinik hari ini.</flux:subheading>
        </div>

        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900">
            <flux:table>
                <flux:table.columns>
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
                    <flux:table.row :key="$q->id">
                        <flux:table.cell>
                            <flux:badge color="zinc" size="md" class="font-mono text-sm font-bold">{{ $q->nomor_antrean }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="font-semibold text-zinc-900 dark:text-white">{{ $q->pasien->nama_pasien }}</div>
                            <div class="text-xs text-zinc-500 font-mono">{{ $q->pasien->no_rekam_medis }}</div>
                        </flux:table.cell>
                        <flux:table.cell class="font-semibold">
                            {{ $q->poliklinik_type === 'umum' ? 'Poli Umum' : ($q->poliklinik_type === 'gigi' ? 'Poli Gigi' : 'KIA') }}
                        </flux:table.cell>
                        <flux:table.cell>{{ $q->pendaftaran->dokter->nama_petugas ?? '-' }}</flux:table.cell>
                        <flux:table.cell>
                            <flux:badge size="sm">{{ $q->pendaftaran->cara_bayar ?? 'Umum' }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            @php
                            $statusColors = [
                            'waiting' => 'zinc',
                            'anamnesis' => 'orange',
                            'waiting_doctor' => 'yellow',
                            'examination' => 'blue',
                            'completed' => 'green',
                            ];
                            $statusNames = [
                            'waiting' => 'Menunggu',
                            'anamnesis' => 'Anamnesis',
                            'waiting_doctor' => 'Menunggu Dokter',
                            'examination' => 'Pemeriksaan',
                            'completed' => 'Selesai',
                            ];
                            $color = $statusColors[$q->status] ?? 'zinc';
                            $name = $statusNames[$q->status] ?? $q->status;
                            @endphp
                            <flux:badge color="{{ $color }}" size="sm">{{ $name }}</flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="flex items-center gap-2">
                            <flux:button variant="ghost" icon="printer" size="sm" wire:click="reprintTicket({{ $q->id }})" title="Cetak Ulang Tiket" />
                            <flux:button variant="primary" size="sm" href="{{ route('medical-record.examine', ['poliklinik' => $q->poliklinik_type, 'encounter_id' => $q->encounter_id]) }}" wire:navigate>
                                Periksa
                            </flux:button>
                        </flux:table.cell>
                    </flux:table.row>
                    @empty
                    <flux:table.row>
                        <flux:table.cell colspan="7" class="text-center text-zinc-500 py-8">Belum ada antrean kunjungan untuk hari ini.</flux:table.cell>
                    </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    <!-- MODAL PATIENT FORM -->
    @if ($showPatientModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">{{ $pasien_id ? 'Edit Profil Pasien' : 'Tambah Pasien Baru' }}</flux:heading>
                <button type="button" wire:click="$set('showPatientModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="savePatient" class="p-6 space-y-6 flex-1">
                <!-- NIK Verification (Mock SatuSehat) -->
                <div class="bg-zinc-50 dark:bg-zinc-950/20 p-4 border border-zinc-200 dark:border-zinc-800 rounded-lg space-y-3">
                    <flux:heading size="md">Verifikasi SatuSehat Kemenkes</flux:heading>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex gap-2 items-end">
                            <flux:input wire:model="nik" label="NIK (16 Digit)" required placeholder="Contoh: 1234567890123456" class="flex-1 font-mono" />
                            <flux:button type="button" variant="filled" wire:click="verifyNik" class="h-10">Verify</flux:button>
                        </div>
                        <flux:input wire:model="ihs_number" label="IHS Number (Read-Only)" readonly placeholder="Akan terisi otomatis jika NIK terverifikasi" class="font-mono bg-zinc-100 dark:bg-zinc-800" />
                    </div>
                    @if ($showIhsWarning)
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-lg text-amber-700 dark:text-amber-400 text-xs flex items-center gap-2">
                        <flux:icon.exclamation-circle class="w-4 h-4 flex-shrink-0" />
                        <span><strong>IHS Not Synced:</strong> NIK tidak terdaftar di SatuSehat. Data pasien dapat disimpan secara lokal.</span>
                    </div>
                    @endif
                </div>

                <!-- Biodata Dasar -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="no_rekam_medis" label="No. Rekam Medis (Editable)" required class="font-mono" />
                    <flux:input wire:model="nama_pasien" label="Nama Lengkap Pasien" required />
                    <flux:input wire:model="panggilan" label="Nama Panggilan" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="no_bpjs" label="No. Kartu BPJS (13 Digit)" class="font-mono" />
                    <flux:input wire:model="tempat_lahir" label="Tempat Lahir" required />
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
                    <flux:select wire:model="pekerjaan_id" label="Pekerjaan">
                        <flux:select.option value="">Pilih Pekerjaan</flux:select.option>
                        @foreach($pekerjaans as $pekerjaan)
                        <flux:select.option value="{{ $pekerjaan->id }}">
                            {{ $pekerjaan->nama_pekerjaan }}
                        </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Kontak & Alamat -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:input wire:model="no_whatsapp" label="No. WhatsApp / HP" />
                    <flux:input wire:model="email" type="email" label="Alamat Email" />
                    <flux:select wire:model="status_pasien" label="Status Pasien" required>
                        <flux:select.option value="aktif">Aktif</flux:select.option>
                        <flux:select.option value="nonaktif">Non-Aktif</flux:select.option>
                        <flux:select.option value="meninggal">Meninggal</flux:select.option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="alamat" label="Alamat Lengkap" required rows="3" />

                <!-- Form Buttons -->
                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4">
                    <flux:button type="button" variant="ghost" wire:click="resetForm">Clear</flux:button>
                    <flux:button type="button" variant="filled" wire:click="$set('showPatientModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL REGISTER OUTPATIENT -->
    @if ($showRegisterModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Daftar Rawat Jalan Pasien</flux:heading>
                <button type="button" wire:click="$set('showRegisterModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
                    <flux:icon.x-mark class="w-6 h-6" />
                </button>
            </div>

            <form wire:submit.prevent="saveOutpatientRegistration" class="p-6 space-y-4">
                <flux:select wire:model="reg_poli_id" label="Klinik / Poli Tujuan" required placeholder="Pilih Klinik">
                    @foreach ($polis as $poli)
                    <flux:select.option value="{{ $poli->id }}">{{ $poli->kode_poli }} - {{ $poli->nama_poli }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="reg_dokter_id" label="Dokter Pemeriksa" required placeholder="Pilih Dokter">
                    @foreach ($doctors as $doc)
                    <flux:select.option value="{{ $doc->id }}">{{ $doc->nama_petugas }} (SIP: {{ $doc->nomor_sip ?? '-' }})</flux:select.option>
                    @endforeach
                </flux:select>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <flux:select wire:model="reg_cara_bayar" label="Cara Pembayaran" required>
                        <flux:select.option value="Umum">Umum</flux:select.option>
                        <flux:select.option value="BPJS">BPJS Kesehatan</flux:select.option>
                        <flux:select.option value="Dinas/Instansi">Dinas/Instansi</flux:select.option>
                    </flux:select>
                    <flux:select wire:model="reg_jenis_kunjungan" label="Jenis Kunjungan" required>
                        <flux:select.option value="Baru">Kunjungan Baru</flux:select.option>
                        <flux:select.option value="Lama">Kunjungan Lama</flux:select.option>
                        <flux:select.option value="Kontrol">Kontrol Ulang</flux:select.option>
                    </flux:select>
                </div>

                @if ($reg_cara_bayar === 'BPJS')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-indigo-50/20 p-4 rounded-lg border border-indigo-200/50">
                    <flux:input wire:model="reg_no_sep" label="Nomor SEP (Surat Eligibilitas Peserta)" required placeholder="Contoh: 0134R001..." class="font-mono" />
                    <flux:input wire:model="reg_no_rujukan" label="Nomor Rujukan" required placeholder="Contoh: 0134101..." class="font-mono" />
                </div>
                @endif

                <flux:textarea wire:model="reg_keluhan_awal" label="Keluhan Utama / Alasan Kunjungan" required rows="3" />

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showRegisterModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Daftarkan Rawat Jalan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL CONSENT FORM -->
    @if ($showConsentModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">
                    {{ $consent_type === 'general_consent' ? 'Buat Persetujuan Umum (General Consent)' : 'Buat Persetujuan Tindakan (Informed Consent)' }}
                </flux:heading>
                <button type="button" wire:click="$set('showConsentModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
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

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showConsentModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak PDF</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL REFERRAL FORM -->
    @if ($showReferralModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">Buat Surat Rujukan Pasien (Referral Letter)</flux:heading>
                <button type="button" wire:click="$set('showReferralModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
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

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showReferralModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak Rujukan</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- MODAL CERTIFICATE FORM -->
    @if ($showCertificateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-xs p-4">
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto flex flex-col">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-800 flex justify-between items-center">
                <flux:heading size="lg">
                    @if ($cert_type === 'sehat')
                    Buat Surat Keterangan Sehat
                    @elseif ($cert_type === 'sakit')
                    Buat Surat Keterangan Sakit (Sick Leave)
                    @else
                    Buat Surat Keterangan Bebas Narkoba
                    @endif
                </flux:heading>
                <button type="button" wire:click="$set('showCertificateModal', false)" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300">
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

                <div class="flex justify-end gap-2 border-t border-zinc-200 dark:border-zinc-800 pt-4 mt-4">
                    <flux:button type="button" variant="filled" wire:click="$set('showCertificateModal', false)">Batal</flux:button>
                    <flux:button type="submit" variant="primary">Simpan & Cetak Surat</flux:button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- JS Event Listener to Open Print in New Tab -->
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