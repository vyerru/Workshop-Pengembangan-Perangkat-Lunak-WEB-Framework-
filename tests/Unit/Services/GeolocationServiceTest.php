<?php

namespace Tests\Unit\Services;

use App\Services\GeolocationService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeolocationServiceTest extends TestCase
{
    private GeolocationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new GeolocationService();
    }

    #[Test]
    public function it_menghitung_jarak_haversine_antar_titik_yang_sama(): void
    {
        $jarak = $this->service->haversineDistance(-6.2, 106.8, -6.2, 106.8);
        $this->assertEquals(0, $jarak);
    }

    #[Test]
    public function it_menghitung_jarak_haversine_monas_ke_gedung_sate(): void
    {
        $jarak = $this->service->haversineDistance(-6.1754, 106.8272, -6.9025, 107.6188);
        $this->assertGreaterThan(100000, $jarak);
        $this->assertLessThan(120000, $jarak);
    }

    #[Test]
    public function it_menghitung_threshold_efektif(): void
    {
        $threshold = $this->service->hitungThresholdEfektif(100, 10, 15);
        $this->assertEquals(125, $threshold);
    }

    #[Test]
    public function it_validasi_jarak_diterima_jika_kurang_dari_threshold(): void
    {
        $this->assertTrue($this->service->isValid(50, 100));
        $this->assertFalse($this->service->isValid(150, 100));
        $this->assertTrue($this->service->isValid(100, 100));
    }

    #[Test]
    public function it_menghitung_jarak_antar_titik_yang_sangat_dekat(): void
    {
        $jarak = $this->service->haversineDistance(-6.1754, 106.8272, -6.1755, 106.8273);
        $this->assertLessThan(20, $jarak);
    }
}
