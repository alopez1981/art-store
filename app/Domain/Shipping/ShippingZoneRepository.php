<?php

declare(strict_types=1);

namespace App\Domain\Shipping;

/**
 * Puerto (hexagonal): el dominio define el contrato,
 * la infraestructura decide de dónde salen las zonas
 * (config, BBDD, API de transportista...).
 */
interface ShippingZoneRepository
{
    /** @return list<ShippingZone> */
    public function all(): array;

    public function findByCode(string $code): ?ShippingZone;
}
