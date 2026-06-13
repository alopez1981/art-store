<?php

namespace Database\Seeders;

use App\Models\Artwork;
use Illuminate\Database\Seeder;

/**
 * Obras reales de la colección (datos de Estela/obra para shop/textos.rtf).
 * Idempotente: usa updateOrCreate por slug, se puede relanzar sin duplicar.
 */
class ArtworkSeeder extends Seeder
{
    private const TECHNIQUE = 'Escultura con latas recicladas · técnica mixta, spray y acrílico';

    public function run(): void
    {
        $artworks = [
            [
                'title' => 'YOU MAKE ME BEAT',
                'slug' => 'you-make-me-beat',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. El latido como mensaje: latas que reivindican desde el amor un mundo mejor.',
                'price' => 50000, // 500€
                'image_url' => '/images/obras/you-make-me-beat.jpg',
                'dimensions' => '46 × 67 × 12 cm',
            ],
            [
                'title' => 'RED LOVE',
                'slug' => 'red-love',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. El amor en rojo, directo y sin adornos.',
                'price' => 20000, // 200€
                'image_url' => '/images/obras/love.jpg',
                'dimensions' => '40 × 20 × 10 cm',
            ],
            [
                'title' => 'FIX YOU',
                'slug' => 'fix-you',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. Reparar, recomponer, volver a latir.',
                'price' => 35000, // 350€
                'image_url' => '/images/obras/fix-you.jpg',
                'dimensions' => '36 × 40 × 12 cm',
            ],
            [
                'title' => 'FOREVER',
                'slug' => 'forever',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. Un mensaje que no caduca.',
                'price' => 28000, // 280€
                'image_url' => '/images/obras/forever.jpg',
                'dimensions' => '54 × 28 × 10 cm',
            ],
            [
                'title' => 'NEVER GIVE UP',
                'slug' => 'never-give-up',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. Resistencia enlatada: no rendirse nunca.',
                'price' => 35000, // 350€
                'image_url' => '/images/obras/never-give-up.jpg',
                'dimensions' => '38 × 39 × 8 cm',
            ],
            [
                'title' => 'SMILE',
                'slug' => 'smile',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. Hacer sonreír a los peatones, también en interiores.',
                'price' => 20000, // 200€
                'image_url' => '/images/obras/smile.jpg',
                'dimensions' => '49 × 18 × 7 cm',
            ],
            [
                'title' => 'LEGO HEAD SPRAY CAN',
                'slug' => 'lego-head-spray-can',
                'description' => 'Escultura realizada con latas recicladas. Técnica mixta, pintura en spray y acrílico. Pieza de bolsillo con cabeza de juguete y alma de spray.',
                'price' => 9000, // 90€
                'image_url' => '/images/obras/lego-smile-spray-can.jpg',
                'dimensions' => '20 × 7 × 7 cm',
            ],
        ];

        foreach ($artworks as $artwork) {
            Artwork::updateOrCreate(
                ['slug' => $artwork['slug']],
                $artwork + [
                    'technique' => self::TECHNIQUE,
                    'year' => 2026,
                    'is_published' => true,
                ]
            );
        }
    }
}
