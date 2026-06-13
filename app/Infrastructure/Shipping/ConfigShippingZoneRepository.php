<?php

declare(strict_types=1);

namespace App\Infrastructure\Shipping;

use App\Domain\Shipping\ShippingZone;
use App\Domain\Shipping\ShippingZoneRepository;

/**
 * Adaptador de infraestructura: materializa las zonas
 * desde config/shipping.php. Si mañana las tarifas viven
 * en BBDD o en la API de un carrier, se sustituye este
 * adaptador sin tocar dominio ni controllers.
 */
final class ConfigShippingZoneRepository implements ShippingZoneRepository
{
    /** @param array<string, array<string, mixed>> $zonesConfig */
    public function __construct(private readonly array $zonesConfig)
    {
    }

    public function all(): array
    {
        $zones = [];

        foreach ($this->zonesConfig as $code => $zone) {
            $zones[] = new ShippingZone(
                code: $code,
                label: $zone['label'],
                description: $zone['description'],
                amount: (int) $zone['amount'],
                minDays: (int) $zone['delivery_days'][0],
                maxDays: (int) $zone['delivery_days'][1],
                countries: $zone['countries'],
            );
        }

        return $zones;
    }

    public function findByCode(string $code): ?ShippingZone
    {
        foreach ($this->all() as $zone) {
            if ($zone->code === $code) {
                return $zone;
            }
        }

        return null;
    }
}
