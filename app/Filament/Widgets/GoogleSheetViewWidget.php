<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Google\Client;
use Google\Service\Sheets;
use Illuminate\Support\Facades\Log;

class GoogleSheetViewWidget extends Widget
{
    protected static string $view = 'filament.widgets.google-sheet-view';

    protected static ?string $heading = 'Daftar Material (Google Sheet)';

    // ✅ Full width biar tabel melebar ke seluruh area dashboard
    protected int|string|array $columnSpan = 'full';

    public function getViewData(): array
    {
        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);
            $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
            $sheetName = 'produksi';
            $range = "{$sheetName}!A1:L";

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                throw new \Exception('Tidak ada data di Google Sheet.');
            }

            $headers = array_map(fn($h) => str_replace([' ', '-', '/'], '_', strtolower(trim($h))), array_shift($values));
            $rows = collect($values)->map(fn($row) => array_combine($headers, array_pad($row, count($headers), null)));

            Log::info('GoogleSheetViewWidget loaded', [
                'count' => $rows->count(),
                'sample' => $rows->first(),
            ]);
        } catch (\Throwable $e) {
            Log::error('GoogleSheetViewWidget error', ['message' => $e->getMessage()]);
            $rows = collect();
        }

        return [
            'data' => $rows,
        ];
    }
}
