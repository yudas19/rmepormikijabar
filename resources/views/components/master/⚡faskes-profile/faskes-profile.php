<?php

use App\Models\FaskesProfile;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component
{
    use WithFileUploads;

    public $nama_faskes = '';

    public $alamat = '';

    public $penanggung_jawab = '';

    public $no_telp = '';

    public $email = '';

    public $kode_faskes_kemenkes = '';

    public $logo;

    public $logo_path = '';

    public function mount(): void
    {
        $profile = FaskesProfile::firstOrCreate(
            ['id' => 1],
            [
                'nama_faskes' => 'Klinik Pratama Pormiki',
                'alamat' => 'Jl. Pormiki Raya No. 45, Bandung',
                'penanggung_jawab' => 'dr. Andi Wijaya',
                'no_telp' => '022-7654321',
                'email' => 'info@rmepormikijabar.com',
                'kode_faskes_kemenkes' => 'F-12345',
            ]
        );

        $this->nama_faskes = $profile->nama_faskes;
        $this->alamat = $profile->alamat;
        $this->penanggung_jawab = $profile->penanggung_jawab;
        $this->no_telp = $profile->no_telp;
        $this->email = $profile->email;
        $this->kode_faskes_kemenkes = $profile->kode_faskes_kemenkes;
        $this->logo_path = $profile->logo_path;
    }

    public function save(): void
    {
        $rules = [
            'nama_faskes' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'penanggung_jawab' => 'required|string|max:255',
            'no_telp' => 'required|string|max:50',
            'email' => 'required|email|max:255',
            'kode_faskes_kemenkes' => 'required|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ];

        $this->validate($rules);

        $profile = FaskesProfile::findOrFail(1);

        if ($this->logo) {
            $path = $this->logo->store('logos', 'public');
            $profile->logo_path = $path;
            $this->logo_path = $path;
            $this->logo = null;
        }

        $profile->update([
            'nama_faskes' => $this->nama_faskes,
            'alamat' => $this->alamat,
            'penanggung_jawab' => $this->penanggung_jawab,
            'no_telp' => $this->no_telp,
            'email' => $this->email,
            'kode_faskes_kemenkes' => $this->kode_faskes_kemenkes,
        ]);

        Flux::toast(variant: 'success', text: 'Profil Fasilitas Kesehatan berhasil diperbarui.');
    }

    public function render()
    {
        return view('components.master.⚡faskes-profile.faskes-profile')
            ->layout('layouts::app');
    }
};
