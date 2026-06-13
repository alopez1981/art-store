<?php

/*
 * Zonas de envío con origen en Badalona (Barcelona).
 * Importes en céntimos de euro. Ajusta tarifas aquí sin tocar código.
 * delivery_days: estimación [min, max] en días laborables.
 */
return [
    'origin' => [
        'city' => 'Badalona',
        'country' => 'ES',
    ],

    'zones' => [
        'local' => [
            'label' => 'Badalona / Área de Barcelona',
            'description' => 'Entrega local desde el estudio (≈ 0–30 km)',
            'amount' => 800, // 8 €
            'delivery_days' => [1, 3],
            'countries' => ['ES'],
        ],
        'es_peninsula' => [
            'label' => 'España península',
            'description' => 'Envío peninsular (≈ 30–1.000 km)',
            'amount' => 1500, // 15 €
            'delivery_days' => [2, 5],
            'countries' => ['ES'],
        ],
        'es_islas' => [
            'label' => 'Baleares, Canarias, Ceuta y Melilla',
            'description' => 'Envío insular y ciudades autónomas',
            'amount' => 2500, // 25 €
            'delivery_days' => [4, 8],
            'countries' => ['ES'],
        ],
        'europa' => [
            'label' => 'Europa',
            'description' => 'Envío internacional europeo (≈ 1.000–3.000 km)',
            'amount' => 3500, // 35 €
            'delivery_days' => [5, 10],
            'countries' => [
                'AT', 'BE', 'BG', 'CH', 'CY', 'CZ', 'DE', 'DK', 'EE', 'FI',
                'FR', 'GB', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV',
                'MT', 'NL', 'NO', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK',
            ],
        ],
        'mundo' => [
            'label' => 'Resto del mundo',
            'description' => 'Envío internacional (> 3.000 km)',
            'amount' => 6000, // 60 €
            'delivery_days' => [7, 15],
            'countries' => [
                'US', 'CA', 'MX', 'AR', 'BR', 'CL', 'CO', 'PE', 'UY',
                'JP', 'KR', 'SG', 'HK', 'AU', 'NZ', 'AE', 'IL', 'ZA',
            ],
        ],
    ],
];
