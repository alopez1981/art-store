<?php

namespace Tests\Feature;

use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    use RefreshDatabase;

    private function makeArtwork(array $overrides = []): Artwork
    {
        return Artwork::create(array_merge([
            'title' => 'SMILE',
            'slug' => 'smile',
            'description' => 'Escultura de prueba',
            'price' => 20000,
            'image_url' => '/images/obras/smile.jpg',
            'technique' => 'Latas recicladas',
            'dimensions' => '49 × 18 × 7 cm',
            'year' => 2026,
            'is_published' => true,
        ], $overrides));
    }

    public function test_checkout_requiere_zona_de_envio(): void
    {
        $artwork = $this->makeArtwork();

        $this->postJson("/checkout/{$artwork->id}", [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['shipping_zone']);
    }

    public function test_checkout_rechaza_zona_inexistente(): void
    {
        $artwork = $this->makeArtwork();

        $this->postJson("/checkout/{$artwork->id}", ['shipping_zone' => 'marte'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['shipping_zone']);
    }

    public function test_checkout_rechaza_obra_vendida(): void
    {
        $artwork = $this->makeArtwork(['vendido_at' => now()]);

        $this->postJson("/checkout/{$artwork->id}", ['shipping_zone' => 'local'])
            ->assertStatus(410);
    }

    public function test_checkout_rechaza_obra_sin_publicar(): void
    {
        $artwork = $this->makeArtwork(['is_published' => false]);

        $this->postJson("/checkout/{$artwork->id}", ['shipping_zone' => 'local'])
            ->assertNotFound();
    }

    public function test_la_ficha_expone_las_tarifas_de_envio(): void
    {
        $artwork = $this->makeArtwork();

        $this->get("/artworks/{$artwork->slug}")
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('ArtworkShow')
                ->has('shippingZones', count(config('shipping.zones')))
                ->where('shippingZones.0.code', 'local')
            );
    }

    public function test_la_ficha_de_obra_sin_publicar_devuelve_404(): void
    {
        $artwork = $this->makeArtwork(['is_published' => false]);

        $this->get("/artworks/{$artwork->slug}")->assertNotFound();
    }
}
