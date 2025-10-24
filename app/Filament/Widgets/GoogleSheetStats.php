<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Google\Client;
use Google\Service\Sheets;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;

class GoogleSheetStats extends BaseWidget
{
    protected static ?string $pollingInterval = null; // Nonaktif auto-refresh

    protected function getStats(): array
    {
        $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
        $sheetName = 'produksi'; // ganti sesuai nama tab di spreadsheet kamu

        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);
            $range = "{$sheetName}!A2:O"; // Kolom A sampai O
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues();

            if (empty($values)) {
                return [
                    Stat::make('PO PSN', '0')->description('Tidak ada data'),
                    Stat::make('PO OTHER', '0')->description('Tidak ada data'),
                    Stat::make('PO OTHER 2', '0')->description('Tidak ada data'),
                    Stat::make('Stock Mahaga', '0')->description('Tidak ada data'),
                ];
            }

            // Inisialisasi total
            $poPsn = $poOther = $poOther2 = $stockMahaga = 0;

            foreach ($values as $row) {
                $poPsn += (int)($row[1] ?? 0); // kolom B
                $poOther += (int)($row[2] ?? 0); // kolom C
                $poOther2 += (int)($row[3] ?? 0); // kolom D
                $stockMahaga += (int)($row[4] ?? 0); // kolom E
            }

            return [
                Stat::make('PO PSN', number_format($poPsn))
                    ->description('Total PO PSN')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->color(Color::Blue)
                    ,

                Stat::make('PO OTHER', number_format($poOther))
                    ->description('Total PO OTHER')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->color(Color::Rose)
                    ,

                Stat::make('PO OTHER 2', number_format($poOther2))
                    ->description('Total PO OTHER 2')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->color(Color::Amber)
                    ,

                Stat::make('Stock Mahaga', number_format($stockMahaga))
                    ->description('Total stok Mahaga')
                    ->descriptionIcon('heroicon-m-cube', IconPosition::Before)
                    ->color(Color::Emerald)
                    ,
            ];
        } catch (\Exception $e) {
            return [
                Stat::make('Error', 'Gagal memuat Google Sheet')
                    ->description($e->getMessage())
                    ->color(Color::Red)
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }
    }
}
