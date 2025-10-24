<?php

namespace App\Services;

use Google\Client;
use Google\Service\Sheets;

class GoogleSheetService
{
    protected $service;
    protected $spreadsheetId;

    public function __construct()
    {
        $client = new Client();
        $client->setApplicationName('Laravel Google Sheets');
        $client->setScopes([Sheets::SPREADSHEETS_READONLY]);
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $this->service = new Sheets($client);

        // Ganti dengan ID spreadsheet kamu
        $this->spreadsheetId = 'YOUR_SPREADSHEET_ID';
    }

    public function getData(string $range): array
    {
        $response = $this->service->spreadsheets_values->get($this->spreadsheetId, $range);
        $values = $response->getValues();

        if (empty($values)) {
            return [];
        }

        // Asumsikan baris pertama adalah header
        $headers = array_shift($values);

        // Gabungkan header dan nilai agar mudah diakses
        return array_map(function ($row) use ($headers) {
            return array_combine($headers, array_pad($row, count($headers), null));
        }, $values);
    }
}
