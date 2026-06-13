<?php

use App\Models\MasterJadwalDokter;
use App\Models\MasterPetugas;
use App\Models\Poli;
use Flux\Flux;
use Livewire\Component;

new class extends Component
{
    public $search = '';

    public $selectedId = null;

    public $petugas_id = '';

    public $poli_id = '';

    public $hari = 'Senin';

    public $jam_mulai = '08:00';

    public $jam_selesai = '12:00';

    public $kuota_pasien = 30;

    public function edit($id): void
    {
        $this->resetForm();
        $this->selectedId = $id;
        $record = MasterJadwalDokter::findOrFail($id);

        $this->petugas_id = $record->petugas_id;
        $this->poli_id = $record->poli_id;
        $this->hari = $record->hari;
        $this->jam_mulai = substr($record->jam_mulai, 0, 5);
        $this->jam_selesai = substr($record->jam_selesai, 0, 5);
        $this->kuota_pasien = $record->kuota_pasien;
    }

    public function resetForm(): void
    {
        $this->selectedId = null;
        $this->petugas_id = '';
        $this->poli_id = '';
        $this->hari = 'Senin';
        $this->jam_mulai = '08:00';
        $this->jam_selesai = '12:00';
        $this->kuota_pasien = 30;
        $this->resetErrorBag();
    }

    public function save(): void
    {
        $rules = [
            'petugas_id' => 'required|exists:master_petugass,id',
            'poli_id' => 'required|exists:master_polis,id',
            'hari' => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'kuota_pasien' => 'required|integer|min:1',
        ];

        $validated = $this->validate($rules);

        if ($this->selectedId) {
            $record = MasterJadwalDokter::findOrFail($this->selectedId);
            $record->update($validated);
            $message = 'Jadwal dokter berhasil diperbarui.';
        } else {
            MasterJadwalDokter::create($validated);
            $message = 'Jadwal dokter berhasil ditambahkan.';
        }

        Flux::toast(variant: 'success', text: $message);
        $this->resetForm();
    }

    public function delete($id): void
    {
        $record = MasterJadwalDokter::findOrFail($id);
        $record->delete();
        Flux::toast(variant: 'success', text: 'Jadwal dokter berhasil dihapus.');
        if ($this->selectedId === $id) {
            $this->resetForm();
        }
    }

    public function render()
    {
        $schedules = MasterJadwalDokter::with(['petugas', 'poli'])
            ->when($this->search, function ($query) {
                $query->whereHas('petugas', function ($q) {
                    $q->where('nama_petugas', 'like', '%'.$this->search.'%');
                })->orWhereHas('poli', function ($q) {
                    $q->where('nama_poli', 'like', '%'.$this->search.'%');
                })->orWhere('hari', 'like', '%'.$this->search.'%');
            })
            ->get();

        $groupedSchedules = $schedules->groupBy(function ($item) {
            return $item->poli->nama_poli;
        });

        $doctors = MasterPetugas::where('jenis_petugas', 'Dokter')
            ->where('is_aktif', true)
            ->orderBy('nama_petugas')
            ->get();

        $polis = Poli::where('is_active', true)
            ->orderBy('nama_poli')
            ->get();

        return view('components.master.⚡jadwal-dokter.jadwal-dokter', [
            'groupedSchedules' => $groupedSchedules,
            'doctors' => $doctors,
            'polis' => $polis,
        ])->layout('layouts::app');
    }
};
