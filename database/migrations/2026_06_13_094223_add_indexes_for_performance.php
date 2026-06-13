<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Para búsquedas en webhook
            $table->index('stripe_session_id');
            $table->index('stripe_payment_intent_id');
            $table->index('stripe_customer_email');
            // Para queries de órdenes por fecha
            $table->index('created_at');
        });

        Schema::table('artworks', function (Blueprint $table) {
            // Para marcar como vendidas
            $table->index('vendido_at');
            // Para listados públicos
            $table->index(['is_published', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['stripe_session_id']);
            $table->dropIndex(['stripe_payment_intent_id']);
            $table->dropIndex(['stripe_customer_email']);
            $table->dropIndex(['created_at']);
        });

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropIndex(['vendido_at']);
            $table->dropIndex(['is_published', 'created_at']);
        });
    }
};
