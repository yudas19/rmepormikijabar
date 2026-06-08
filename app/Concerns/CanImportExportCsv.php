<?php

namespace App\Concerns;

use Flux\Flux;
use Illuminate\Support\Facades\Response;
use Livewire\WithFileUploads;

trait CanImportExportCsv
{
    use WithFileUploads;

    public $csvFile;

    abstract protected function getModelClass();

    abstract protected function getExportColumns();

    abstract protected function getUniqueKeys();

    public function updatedCsvFile()
    {
        $this->importCsv();
    }

    public function exportCsv()
    {
        $modelClass = $this->getModelClass();
        $exportColumns = $this->getExportColumns();

        $records = $modelClass::all();

        $callback = function () use ($records, $exportColumns) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, array_keys($exportColumns));

            // Data rows
            foreach ($records as $row) {
                $data = [];
                foreach ($exportColumns as $header => $col) {
                    $val = $row->{$col};
                    // Format booleans as 1/0
                    if (is_bool($val)) {
                        $val = $val ? 1 : 0;
                    }
                    $data[] = $val;
                }
                fputcsv($file, $data);
            }

            fclose($file);
        };

        $filename = strtolower(class_basename($modelClass)).'_export_'.date('Y-m-d_H-i-s').'.csv';

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function importCsv()
    {
        $this->validate([
            'csvFile' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $modelClass = $this->getModelClass();
        $importColumns = $this->getExportColumns();
        $uniqueKeys = $this->getUniqueKeys();

        $path = $this->csvFile->getRealPath();
        $file = fopen($path, 'r');

        // Get header row
        $headers = fgetcsv($file);
        if (! $headers) {
            fclose($file);
            $this->addError('csvFile', 'File CSV kosong.');

            return;
        }

        // Clean headers (remove BOM or spaces)
        $headers = array_map(fn ($h) => trim($h, "\xEF\xBB\xBF\r\n\t "), $headers);

        $rowCount = 0;
        while (($row = fgetcsv($file)) !== false) {
            $data = [];
            foreach ($headers as $index => $header) {
                if (isset($importColumns[$header]) && isset($row[$index])) {
                    $colName = $importColumns[$header];
                    $val = $row[$index];
                    // Clean null/empty values
                    if ($val === '') {
                        $val = null;
                    }
                    $data[$colName] = $val;
                }
            }

            if (empty($data)) {
                continue;
            }

            // Build match attributes for updateOrCreate
            $matchAttributes = [];
            foreach ($uniqueKeys as $key) {
                if (isset($data[$key])) {
                    $matchAttributes[$key] = $data[$key];
                }
            }

            if (empty($matchAttributes)) {
                $modelClass::create($data);
            } else {
                $modelClass::updateOrCreate($matchAttributes, $data);
            }
            $rowCount++;
        }

        fclose($file);
        $this->csvFile = null;

        Flux::toast(variant: 'success', text: "Berhasil mengimpor {$rowCount} data.");
    }
}
