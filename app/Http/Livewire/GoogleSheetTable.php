<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Google\Client as GoogleClient;
use Google\Service\Sheets as GoogleSheets;
use Illuminate\Support\Collection;

class GoogleSheetTable extends Component
{
    public Collection $rows;
    public ?string $error = null;
    public bool $loading = true;

    // ganti sesuai spreadsheet kamu
    protected string $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
    protected string $sheetName = 'produksi';
    protected string $range = 'A1:O'; // header di baris 1, data mulai A2

    public function mount()
    {
        $this->rows = collect();
        $this->load();
    }

    public function load()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $client = new GoogleClient();
            $client->setApplicationName('Laravel Google Sheets Livewire');
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->setScopes([GoogleSheets::SPREADSHEETS_READONLY]);

            $service = new GoogleSheets($client);
            $response = $service->spreadsheets_values->get($this->spreadsheetId, "{$this->sheetName}!{$this->range}");
            $values = $response->getValues() ?? [];

            if (empty($values)) {
                $this->rows = collect();
                $this->loading = false;
                return;
            }

            // Ambil header (baris pertama) dan normalize jadi snake_case keys
            $headersRaw = array_shift($values);
            $headers = array_map(fn($h) => preg_replace('/[^a-z0-9_]+/','_', strtolower(trim($h))), $headersRaw);

            // Map data -> associative arrays
            $mapped = collect($values)->map(function ($row) use ($headers) {
                $assoc = [];
                foreach ($headers as $idx => $key) {
                    $assoc[$key] = $row[$idx] ?? null;
                }
                return $assoc;
            });

            $this->rows = $mapped;
        } catch (\Throwable $e) {
            $this->error = $e->getMessage();
            $this->rows = collect();
        } finally {
            $this->loading = false;
        }
    }

    public function refreshData()
    {
        $this->load();
    }

    public function render()
    {
        return view('livewire.google-sheet-table');
    }
}
