<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_invitado_no_puede_ver_los_pedidos(): void
    {
        $this->get('/admin/orders')->assertRedirect();
    }

    public function test_un_usuario_no_admin_recibe_403(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin/orders')->assertForbidden();
    }

    public function test_un_admin_ve_el_listado_de_pedidos(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $artwork = Artwork::create([
            'title' => 'SMILE',
            'slug' => 'smile',
            'price' => 20000,
            'image_url' => '/images/obras/smile.jpg',
            'is_published' => true,
        ]);

        Order::create([
            'artwork_id' => $artwork->id,
            'stripe_session_id' => 'cs_test_123',
            'stripe_customer_email' => 'comprador@example.com',
            'customer_name' => 'Comprador Test',
            'customer_phone' => '+34600000000',
            'amount_total' => 20800,
            'currency' => 'EUR',
            'payment_status' => 'paid',
            'shipping_zone' => 'local',
            'shipping_amount' => 800,
            'shipping_address' => [
                'name' => 'Comprador Test',
                'address' => [
                    'line1' => 'Calle Mar 1',
                    'city' => 'Badalona',
                    'postal_code' => '08911',
                    'country' => 'ES',
                ],
            ],
            'paid_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get('/admin/orders')
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Admin/Orders/Index')
                ->has('orders', 1)
                ->where('orders.0.customer_name', 'Comprador Test')
                ->where('orders.0.shipping_amount', 800)
                ->where('orders.0.shipping_zone_label', 'Badalona / Área de Barcelona')
            );
    }
}
