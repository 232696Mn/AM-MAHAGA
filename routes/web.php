<?php

use Illuminate\Support\Facades\Route;
use Google\Client;
use Google\Service\Sheets;

Route::get('/test-sheet', function () {
    $spreadsheetId = '1gdPpx5kcmB6TcoyFXhPTkx8IQUoa7TIpgvo6xaaoM1k';
    $sheetName = 'produksi';
    $range = "{$sheetName}!A1:L";

    $client = new Google\Client();
    $client->setApplicationName('Laravel Google Sheets');
    $client->setAuthConfig(storage_path('app/google/credentials.json'));
    $client->setScopes([Google\Service\Sheets::SPREADSHEETS_READONLY]);

    $service = new Google\Service\Sheets($client);
    $response = $service->spreadsheets_values->get($spreadsheetId, $range);
    $values = $response->getValues();

    if (empty($values)) {
        return response()->json(['data' => []]);
    }

    $headers = array_map(function ($header) {
        return str_replace([' ', '-', '/'], '_', strtolower(trim($header)));
    }, array_shift($values));

    $rows = collect($values)->map(function ($row) use ($headers) {
        return array_combine($headers, array_pad($row, count($headers), null));
    });

    return response()->json([
        'total_rows' => $rows->count(),
        'data' => $rows->values(),
    ], 200, [], JSON_PRETTY_PRINT);
})->name('test-sheet');

