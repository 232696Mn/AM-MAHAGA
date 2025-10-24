<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Google\Client;
use Google\Service\Sheets;

class GoogleSheetLineStats extends ApexChartWidget
{
    protected static ?string $chartId = 'googleSheetLineChart';
    protected static ?string $heading = 'Perbandingan Pencapaian Material terhadap Target Project';

    protected function getOptions(): array
    {
        $labels = [];
        $packaging = [];
        $pickup = [];
        $produksi = [];
        $target = [];

        try {
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);
            $response = $service->spreadsheets_values->get(
                '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k',
                'produksi!A1:O'
            );

            $values = $response->getValues() ?? [];
            if (count($values) < 2) {
                throw new \Exception('Data di Google Sheet kosong atau kurang dari 2 baris');
            }

            // Ambil header (baris pertama)
            $headers = array_map('strtolower', $values[0]);

            // Cari index kolom berdasarkan nama header
            $colName = array_search('material name', $headers);
            $colPackaging = array_search('material packaging', $headers);
            $colPickup = array_search('material pick up', $headers);
            $colProduksi = array_search('material produksi', $headers);
            $colTarget = array_search('target project', $headers);

            // Loop isi datanya mulai dari baris ke-2
            foreach (array_slice($values, 1) as $row) {
                $materialName = $row[$colName] ?? null;
                if (empty($materialName)) continue;

                $labels[] = trim($materialName);
                $packaging[] = (float)($row[$colPackaging] ?? 0);
                $pickup[] = (float)($row[$colPickup] ?? 0);
                $produksi[] = (float)($row[$colProduksi] ?? 0);
                $target[] = (float)($row[$colTarget] ?? 0);
            }
        } catch (\Exception $e) {
            // Fallback dummy data
            $labels = ['Router', 'Modem', 'Access Point', 'Adaptor'];
            $packaging = [15, 25, 20, 30];
            $pickup = [10, 18, 15, 25];
            $produksi = [12, 20, 17, 28];
            $target = [25, 25, 25, 25];
        }

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
                'toolbar' => ['show' => true],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'colors' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444'], // biru, hijau, oranye, merah
            'series' => [
                ['name' => 'Material Packaging', 'data' => $packaging],
                ['name' => 'Material Pick Up', 'data' => $pickup],
                ['name' => 'Material Produksi', 'data' => $produksi],
                ['name' => 'Target Project', 'data' => $target],
            ],
            'xaxis' => [
                'categories' => $labels,
                'title' => ['text' => 'Material Name'],
                'labels' => [
                    'rotate' => -25,
                    'style' => ['fontSize' => '12px', 'fontWeight' => 500],
                ],
            ],
            'yaxis' => [
                'title' => ['text' => 'Jumlah Capaian'],
                'min' => 0,
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'center',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val.toLocaleString() + " unit"; }',
                ],
            ],
            'grid' => [
                'borderColor' => '#f1f1f1',
                'strokeDashArray' => 4,
            ],
        ];
    }
}
