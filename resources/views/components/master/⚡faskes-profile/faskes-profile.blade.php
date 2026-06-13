<x-master.layout>
    <x-slot:heading>Profil Fasilitas Kesehatan</x-slot:heading>
    <x-slot:subheading>Atur informasi profil faskes Anda untuk penulisan kop surat dan administrasi sistem.</x-slot:subheading>

    <div class="max-w-4xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl shadow-sm p-6 space-y-6">
        <form wire:submit.prevent="save" class="space-y-6">
            <!-- Logo & Clinic Name Header -->
            <div class="flex flex-col md:flex-row items-center gap-6 pb-6 border-b border-zinc-200 dark:border-zinc-800">
                <div class="relative group">
                    <div class="w-24 h-24 rounded-2xl border-2 border-dashed border-zinc-350 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center overflow-hidden">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="object-contain w-full h-full" alt="Temp Logo" />
                        @elseif ($logo_path)
                            <img src="{{ asset('storage/' . $logo_path) }}" class="object-contain w-full h-full" alt="Clinic Logo" />
                        @else
                            <flux:icon.building-office-2 class="w-10 h-10 text-zinc-400 dark:text-zinc-500" />
                        @endif
                    </div>
                    <label for="logo-upload" class="absolute inset-0 bg-black/45 text-white text-[10px] font-semibold flex items-center justify-center rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                        Ubah Logo
                    </label>
                    <input type="file" id="logo-upload" wire:model.live="logo" class="hidden" accept="image/*" />
                </div>

                <div class="flex-1 space-y-1 text-center md:text-left">
                    <h3 class="text-lg font-bold text-zinc-950 dark:text-white">{{ $nama_faskes ?: 'Fasilitas Kesehatan' }}</h3>
                    <p class="text-xs text-zinc-500 font-mono">Kode Kemenkes: {{ $kode_faskes_kemenkes ?: '-' }}</p>
                    <div wire:loading wire:target="logo" class="text-xs text-indigo-650 dark:text-indigo-400 mt-1">Mengunggah logo...</div>
                    @error('logo') <span class="text-xs text-red-600 dark:text-red-400 block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="nama_faskes" label="Nama Fasilitas Kesehatan" required placeholder="Contoh: Klinik Pratama Pormiki" />
                <flux:input wire:model="kode_faskes_kemenkes" label="Kode Faskes (Kemenkes)" required placeholder="Contoh: F-12345" />

                <flux:input wire:model="penanggung_jawab" label="Dokter Penanggung Jawab" required placeholder="Contoh: dr. Andi Wijaya" />
                <flux:input wire:model="no_telp" label="Nomor Telepon" required placeholder="Contoh: 022-7654321" />

                <flux:input wire:model="email" type="email" label="Email Faskes" required placeholder="Contoh: info@rmepormikijabar.com" />
            </div>

            <flux:input wire:model="alamat" label="Alamat Lengkap" required placeholder="Tuliskan alamat lengkap faskes..." />

            <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                <flux:button type="submit" variant="primary" icon="check" class="shadow-md shadow-indigo-500/10">
                    Simpan Perubahan
                </flux:button>
            </div>
        </form>
    </div>
</x-master.layout>