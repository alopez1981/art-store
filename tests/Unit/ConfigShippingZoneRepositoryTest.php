<?php

namespace Tests\Unit;

use App\Domain\Shipping\ShippingZone;
use App\Infrastructure\Shipping\ConfigShippingZoneRepository;
use PHPUnit\Framework\TestCase;

class ConfigShippingZoneRepositoryTest extends TestCase
{
    private function repository(): ConfigShippingZoneRepository
    {
        return new ConfigShippingZoneRepository([
            'local' => [
                'label' => 'Badalona / Área de Barcelona',
                'description' => 'Entrega local',
                'amount' => 800,
                'delivery_days' => [1, 3],
                'countries' => ['ES'],
            ],
            'europa' => [
                'label' => 'Europa',
                'description' => 'Envío europeo',
                'amount' => 3500,
                'delivery_days' => [5, 10],
                'countries' => ['FR', 'DE', 'IT'],
            ],
        ]);
    }

    public function test_materializa_todas_las_zonas_como_value_objects(): void
    {
        $zones = $this->repository()->all();

        $this->assertCount(2, $zones);
        $this->assertContainsOnlyInstancesOf(ShippingZone::class, $zones);
        $this->assertSame('local', $zones[0]->code);
        $this->assertSame(800, $zones[0]->amount);
        $this->assertSame(1, $zones[0]->minDays);
        $this->assertSame(3, $zones[0]->maxDays);
    }

    public function test_busca_zona_por_codigo(): void
    {
        $zone = $this->repository()->findByCode('europa');

        $this->assertNotNull($zone);
        $this->assertSame('Europa', $zone->label);
        $this->assertTrue($zone->allowsCountry('fr'));
        $this->assertFalse($zone->allowsCountry('US'));
    }

    public function test_devuelve_null_si_la_zona_no_existe(): void
    {
        $this->assertNull($this->repository()->findByCode('marte'));
    }
}
