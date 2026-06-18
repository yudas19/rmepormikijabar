<x-layouts::app :title="'Clinical Workspace - ' . $patient->nama_pasien">
    @if (in_array($poliklinik, ['umum', 'gigi', 'kia']))
        @livewire('⚡medical-record.poli-umum', ['record' => $record])
    @else
        <div class="p-6">
            <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-6 text-center text-zinc-500">
                Workspace untuk Poliklinik ini belum tersedia.
            </div>
        </div>
    @endif
</x-layouts::app>