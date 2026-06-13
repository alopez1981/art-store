<?php

namespace App\Providers;

use App\Domain\Shipping\ShippingZoneRepository;
use App\Infrastructure\Shipping\ConfigShippingZoneRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Puerto -> Adaptador. El resto de la app solo conoce la interfaz.
        $this->app->singleton(ShippingZoneRepository::class, function ($app) {
            return new ConfigShippingZoneRepository(
                $app['config']->get('shipping.zones', [])
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
