<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Google\Client;
use Google\Service\Sheets;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Log;

class GoogleSheetMaterialKekuranganStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static int|array|null $columns = 2;
    protected int|string|array $columnSpan = [
        'default' => 2,
        'md' => 2,
        'lg' => 2,
    ];
    protected ?string $heading = 'Status Material (Terpenuhi / Kekurangan)';

    protected function getStats(): array
    {
        $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
        $sheetName = 'produksi';

        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);
            $range = "{$sheetName}!A1:O";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues() ?? [];

            if (count($values) < 2) {
                return [
                    Stat::make('Material', '0')
                        ->description('Tidak ada data ditemukan')
                        ->color(Color::Gray),
                ];
            }

            $headers = array_map(fn($h) => strtolower(trim((string)$h)), $values[0]);
            $colName = $this->findHeaderIndexBySubstring($headers, 'material');
            $colKekurangan = $this->findHeaderIndexBySubstring($headers, 'kekurangan');

            if ($colName === false || $colKekurangan === false) {
                return [
                    Stat::make('Error', 'Gagal memuat Google Sheet')
                        ->description('Kolom Material atau Kekurangan tidak ditemukan.')
                        ->color(Color::Red)
                        ->icon('heroicon-o-exclamation-triangle'),
                ];
            }

            $data = [];
            foreach (array_slice($values, 1) as $row) {
                $name = isset($row[$colName]) ? trim((string)$row[$colName]) : null;
                $qty = isset($row[$colKekurangan]) && is_numeric($row[$colKekurangan])
                    ? (int)$row[$colKekurangan]
                    : 0;

                if ($name !== null && $name !== '') {
                    $data[$name] = $qty;
                }
            }

            if (empty($data)) {
                return [
                    Stat::make('Material', '0')
                        ->description('Tidak ada material ditemukan')
                        ->color(Color::Gray),
                ];
            }

            // Urutkan biar rapi (misal kekurangan dulu baru terpenuhi)
            uasort($data, function ($a, $b) {
                return $a <=> $b; // yang minus akan muncul duluan
            });

            $stats = [];
            foreach ($data as $material => $qty) {
                if ($qty < 0) {
                    // ❌ Material kurang
                    $stats[] = Stat::make($material, number_format($qty))
                        ->description('Kekurangan')
                        ->descriptionIcon('heroicon-m-arrow-down', IconPosition::Before)
                        ->color(Color::Red);
                } else {
                    // ✅ Material cukup / terpenuhi
                    $stats[] = Stat::make($material, number_format($qty))
                        ->description('Terpenuhi')
                        ->descriptionIcon('heroicon-m-check', IconPosition::Before)
                        ->color(Color::Emerald);
                }
            }

            return $stats;

        } catch (\Exception $e) {
            Log::error('GoogleSheetMaterialKekuranganStats error: ' . $e->getMessage());
            return [
                Stat::make('Error', 'Gagal memuat Google Sheet')
                    ->description($e->getMessage())
                    ->color(Color::Red)
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }
    }

    private function findHeaderIndexBySubstring(array $headers, string $substr): int|false
    {
        foreach ($headers as $i => $h) {
            if (strpos($h, $substr) !== false) {
                return $i;
            }
        }
        return false;
    }
}
