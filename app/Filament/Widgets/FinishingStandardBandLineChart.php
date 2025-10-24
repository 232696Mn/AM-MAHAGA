<?php

namespace App\Filament\Widgets;

use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Google\Client;
use Google\Service\Sheets;

class FinishingStandardBandLineChart extends ApexChartWidget
{
    protected static ?string $chartId = 'finishingStandardBandLineChart';
    protected static ?string $heading = 'Standard Band Progress';

    protected function getOptions(): array
    {
        $labels = [];
        $targetData = [];
        $productionData = [];
        $deliveryData = [];

        try {
            // 🔹 Inisialisasi Google Client
            $client = new Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(Sheets::SPREADSHEETS_READONLY);

            $service = new Sheets($client);

            // 🔹 Ganti dengan ID spreadsheet & sheet name kamu
            $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
            $range = 'finishing!A1:F'; // sesuai kolom: A=Perangkat, B=Target, C=Production, D=Delivery, E=Destination, F=Keterangan

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $values = $response->getValues() ?? [];

            if (count($values) < 2) {
                throw new \Exception('Data di Google Sheet kosong atau kurang dari 2 baris');
            }

            // Ambil header baris pertama
            $headers = array_map('strtolower', $values[0]);

            $colTipe = array_search('tipe perangkat', $headers);
            $colTarget = array_search('target project', $headers);
            $colProduction = array_search('done production', $headers);
            $colDelivery = array_search('done delivery', $headers);
            $colDest = array_search('destination', $headers);
            $colKet = array_search('keterangan', $headers);

            // Loop data
            foreach (array_slice($values, 1) as $row) {
                $tipe = strtolower(trim($row[$colTipe] ?? ''));
                if ($tipe !== 'standar band') continue;

                $destination = trim($row[$colDest] ?? 'Unknown');
                $labels[] = $destination;

                $target = (float)($row[$colTarget] ?? 0);
                $production = (float)($row[$colProduction] ?? 0);
                $delivery = (float)($row[$colDelivery] ?? 0);

                $targetData[] = $target;
                $productionData[] = $production;
                $deliveryData[] = $delivery;
            }

        } catch (\Exception $e) {
            // 🔹 fallback dummy data
            $labels = ['Papua', 'NTT'];
            $targetData = [466, 276];
            $productionData = [76, 106];
            $deliveryData = [16, 106];
        }

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 380,
                'toolbar' => ['show' => true],
            ],
            'stroke' => [
                'curve' => 'smooth',
                'width' => 3,
            ],
            'colors' => ['#3B82F6', '#10B981', '#F59E0B'], // biru, hijau, oranye
            'series' => [
                ['name' => 'Target Project', 'data' => $targetData],
                ['name' => 'Done Production', 'data' => $productionData],
                ['name' => 'Done Delivery', 'data' => $deliveryData],
            ],
            'xaxis' => [
                'categories' => $labels,
                'title' => ['text' => 'Tipe Project'],
            ],
            'yaxis' => [
                'title' => ['text' => 'Jumlah Unit'],
                'min' => 0,
            ],
            'legend' => [
                'position' => 'top',
                'horizontalAlign' => 'center',
            ],
            'tooltip' => [
                'y' => [
                    'formatter' => 'function (val) { return val + " unit"; }',
                ],
            ],
            'grid' => [
                'borderColor' => '#f1f1f1',
                'strokeDashArray' => 4,
            ],
        ];
    }
}
