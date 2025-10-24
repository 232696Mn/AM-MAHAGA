<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Google\Client;
use Google\Service\Sheets;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\IconPosition;
use Illuminate\Support\Facades\Log;

class GoogleSheetMaterialStats extends BaseWidget
{
    protected static ?string $pollingInterval = null; // Nonaktif auto-refresh

    // Atur jumlah kolom per baris via properti statis compatible
    protected static int|array|null $columns = 2;

    // Atur lebar widget di grid (opsional, buat tampilan lebih konsisten)
    protected int|string|array $columnSpan = [
        'default' => 2,
        'md' => 2,
        'lg' => 2,
    ];

    protected function getStats(): array
    {
        $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
        $sheetName = 'produksi';

        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);
            $range = "{$sheetName}!A2:O";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues() ?? [];

            if (empty($values)) {
                return [
                    Stat::make('Material Received Partial', '0')->description('Tidak ada data'),
                    Stat::make('Material Received', '0')->description('Tidak ada data'),
                    Stat::make('Material Produksi', '0')->description('Tidak ada data'),
                    Stat::make('Material Packaging', '0')->description('Tidak ada data'),
                ];
            }

            $totalPartial = $totalReceived = $totalProduksi = $totalPackaging = 0;

            foreach ($values as $row) {
                $totalPartial   += (int)($row[5] ?? 0);
                $totalReceived  += (int)($row[6] ?? 0);
                $totalProduksi  += (int)($row[7] ?? 0);
                $totalPackaging += (int)($row[8] ?? 0);
            }

            Log::info('GoogleSheetMaterialStats totals', [
                'partial' => $totalPartial,
                'received' => $totalReceived,
                'produksi' => $totalProduksi,
                'packaging' => $totalPackaging,
            ]);

            return [
                Stat::make('Material Received Partial', number_format($totalPartial))
                    ->description('Total')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->color(Color::Sky),

                Stat::make('Material Received', number_format($totalReceived))
                    ->description('Total')
                    ->descriptionIcon('heroicon-m-arrow-trending-up', IconPosition::Before)
                    ->color(Color::Emerald),

                Stat::make('Material Produksi', number_format($totalProduksi))
                    ->description('Total')
                    ->descriptionIcon('heroicon-m-cog-8-tooth', IconPosition::Before)
                    ->color(Color::Amber),

                Stat::make('Material Packaging', number_format($totalPackaging))
                    ->description('Total')
                    ->descriptionIcon('heroicon-m-archive-box', IconPosition::Before)
                    ->color(Color::Purple),
            ];
        } catch (\Exception $e) {
            Log::error('GoogleSheetMaterialStats error: ' . $e->getMessage());

            return [
                Stat::make('Error', 'Gagal memuat Google Sheet')
                    ->description($e->getMessage())
                    ->color(Color::Red)
                    ->icon('heroicon-o-exclamation-triangle'),
            ];
        }
    }
    
}
