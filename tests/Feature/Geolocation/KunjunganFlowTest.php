<?php

namespace Tests\Feature\Geolocation;

use App\Models\Kunjungan;
use App\Models\Toko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class KunjunganFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $sales;
    private Toko $toko;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware();
        $this->sales = User::factory()->sales()->create();
        $this->toko = Toko::factory()->create([
            'latitude'  => -6.1754,
            'longitude' => 106.8272,
            'accuracy'  => 10,
        ]);
    }

    #[Test]
    public function sales_dapat_memindai_toko_dan_kunjungan_diterima(): void
    {
        $response = $this->actingAs($this->sales)->postJson('/geolocation/kunjungan', [
            'barcode_token'   => $this->toko->barcode_token,
            'latitude_sales'  => -6.1754,
            'longitude_sales' => 106.8272,
            'accuracy_sales'  => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'diterima']);

        $this->assertDatabaseHas('kunjungans', [
            'toko_id'  => $this->toko->id,
            'sales_id' => $this->sales->id,
            'status'   => 'diterima',
        ]);
    }

    #[Test]
    public function kunjungan_ditolak_jika_posisi_terlalu_jauh(): void
    {
        $response = $this->actingAs($this->sales)->postJson('/geolocation/kunjungan', [
            'barcode_token'   => $this->toko->barcode_token,
            'latitude_sales'  => -6.9025,
            'longitude_sales' => 107.6188,
            'accuracy_sales'  => 50,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'ditolak']);

        $this->assertDatabaseHas('kunjungans', [
            'toko_id'  => $this->toko->id,
            'sales_id' => $this->sales->id,
            'status'   => 'ditolak',
        ]);
    }

    #[Test]
    public function kunjungan_gagal_jika_toko_belum_punya_koordinat(): void
    {
        $tokoTanpaKoordinat = Toko::factory()->tanpaKoordinat()->create();

        $response = $this->actingAs($this->sales)->postJson('/geolocation/kunjungan', [
            'barcode_token'   => $tokoTanpaKoordinat->barcode_token,
            'latitude_sales'  => -6.1754,
            'longitude_sales' => 106.8272,
            'accuracy_sales'  => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 'error']);
    }

    #[Test]
    public function sales_tidak_bisa_kunjungi_toko_yang_sama_dalam_5_menit(): void
    {
        Kunjungan::create([
            'toko_id'          => $this->toko->id,
            'sales_id'         => $this->sales->id,
            'latitude_sales'   => -6.1754,
            'longitude_sales'  => 106.8272,
            'accuracy_sales'   => 10,
            'latitude_toko'    => $this->toko->latitude,
            'longitude_toko'   => $this->toko->longitude,
            'accuracy_toko'    => $this->toko->accuracy ?? 0,
            'jarak_terhitung'  => 0,
            'threshold_efektif' => 120,
            'status'           => 'diterima',
            'waktu_kunjungan'  => now(),
        ]);

        $response = $this->actingAs($this->sales)->postJson('/geolocation/kunjungan', [
            'barcode_token'   => $this->toko->barcode_token,
            'latitude_sales'  => -6.1754,
            'longitude_sales' => 106.8272,
            'accuracy_sales'  => 10,
        ]);

        $response->assertStatus(429);
    }

    #[Test]
    public function akses_ditolak_untuk_role_non_sales(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->postJson('/geolocation/kunjungan', [
            'barcode_token'   => $this->toko->barcode_token,
            'latitude_sales'  => -6.1754,
            'longitude_sales' => 106.8272,
            'accuracy_sales'  => 10,
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function get_toko_by_barcode_returns_data(): void
    {
        $response = $this->actingAs($this->sales)
            ->getJson('/geolocation/kunjungan/toko/' . $this->toko->barcode_token);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data'   => [
                'nama_toko' => $this->toko->nama_toko,
            ],
        ]);
    }
}
