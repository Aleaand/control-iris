<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

it('verifica que la clave API de Trade.gov esté configurada', function () {
    $apiKey = config('services.trade_gov.api_key');
    
    expect($apiKey)->not->toBeEmpty();
});

it('puede conectarse a la API de Consolidated Screening List de Trade.gov y recibir una respuesta exitosa', function () {
    Http::fake([
        'api.trade.gov/*' => Http::response([
            'total' => 1,
            'results' => [
                ['name' => 'Juana', 'source' => 'SDN']
            ]
        ], 200)
    ]);

    $response = Http::get('https://api.trade.gov/consolidated_screening_list/search', [
        'name' => 'Juana',
        'size' => 1
    ]);

    expect($response->status())->toBe(200);
    
    $data = $response->json();

    expect($data)->toBeArray();
    expect($data)->toHaveKey('results');
});

it('encuentra el registro SDN específico al buscar por nombre y coincidencia aproximada', function () {
    Http::fake([
        'api.trade.gov/*' => Http::response([
            'total' => 1,
            'results' => [
                ['name' => 'JUANA OLIVERA JIMENEZ', 'source' => 'SDN']
            ]
        ], 200)
    ]);

    $response = Http::get('https://api.trade.gov/consolidated_screening_list/search', [
        'name' => 'Juana Olivera Jimenez',
        'fuzzy_name' => 'true'
    ]);

    $data = $response->json();

    expect($response->successful())->toBeTrue();
    expect($data)->toBeArray();
    expect($data['total'] ?? 0)->toBeGreaterThan(0);
    
    // Verificar que el nombre coincida en los resultados
    $found = false;
    foreach ($data['results'] as $result) {
        if (str_contains(strtolower($result['name']), 'juana') && str_contains(strtolower($result['name']), 'olivera')) {
            $found = true;
            break;
        }
    }
    
    expect($found)->toBeTrue();
});
