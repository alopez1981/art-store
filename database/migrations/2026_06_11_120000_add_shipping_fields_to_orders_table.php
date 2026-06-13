<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Zona elegida en checkout (FK lógica a config/shipping.php).
            $table->string('shipping_zone', 32)->nullable()->after('payment_status');
            // Importe del envío en céntimos, congelado en el momento del pago.
            $table->unsignedInteger('shipping_amount')->nullable()->after('shipping_zone');
            // Nombre y teléfono del comprador (datos de contacto para el envío).
            $table->string('customer_name')->nullable()->after('stripe_customer_email');
            $table->string('customer_phone', 32)->nullable()->after('customer_name');
            // Dirección de envío completa tal y como la devuelve Stripe.
            $table->json('shipping_address')->nullable()->after('shipping_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_zone',
                'shipping_amount',
                'customer_name',
                'customer_phone',
                'shipping_address',
            ]);
        });
    }
};
