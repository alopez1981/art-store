<?php

declare(strict_types=1);

namespace App\Domain\Shipping;

/**
 * Value Object inmutable. Sin dependencias de framework:
 * la capa de dominio no sabe nada de Laravel ni de Stripe.
 */
final readonly class ShippingZone
{
    /**
     * @param string $code Identificador estable de la zona (ej. "es_peninsula")
     * @param string $label Nombre visible para el comprador
     * @param string $description Detalle orientativo (distancia aproximada)
     * @param int $amount Importe del envío en céntimos
     * @param int $minDays Estimación mínima de entrega (días laborables)
     * @param int $maxDays Estimación máxima de entrega (días laborables)
     * @param list<string> $countries Códigos ISO 3166-1 alpha-2 admitidos
     */
    public function __construct(
        public string $code,
        public string $label,
        public string $description,
        public int $amount,
        public int $minDays,
        public int $maxDays,
        public array $countries,
    ) {
    }

    public function allowsCountry(string $isoCode): bool
    {
        return in_array(strtoupper($isoCode), $this->countries, true);
    }
}
