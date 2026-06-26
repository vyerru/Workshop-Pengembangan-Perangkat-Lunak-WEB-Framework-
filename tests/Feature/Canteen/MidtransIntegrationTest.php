<?php

namespace Tests\Feature\Canteen;

use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Vendor $vendor;
    private Menu $menu;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->vendor = Vendor::create([
            'user_id' => User::factory()->create(['role' => 'vendor'])->id,
            'nama_vendor' => 'Test Vendor',
        ]);
        $this->menu = Menu::create([
            'vendor_id' => $this->vendor->id,
            'nama_menu' => 'Test Menu',
            'harga' => 15000,
            'path_gambar' => null,
        ]);
    }

    #[Test]
    public function checkout_returns_snap_token_and_creates_pending_order(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson('/canteen/checkout', [
                'vendor_id' => $this->vendor->id,
                'cart' => [
                    ['menu_id' => $this->menu->id, 'jumlah' => 2],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'snap_token',
                'id_pesanan',
                'nomor_antrian',
                'qr_html',
            ])
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('pesanans', [
            'user_id' => $this->customer->id,
            'vendor_id' => $this->vendor->id,
            'total' => 30000,
            'status_bayar' => 0,
            'metode_bayar' => null,
        ]);

        $pesanan = Pesanan::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($pesanan->snap_token);
    }

    #[Test]
    public function callback_updates_status_to_lunas_on_settlement(): void
    {
        $pesanan = Pesanan::create([
            'user_id' => $this->customer->id,
            'vendor_id' => $this->vendor->id,
            'kode_pesanan' => 'ORD-TEST-1234567890',
            'total' => 30000,
            'status_bayar' => 0,
            'metode_bayar' => null,
            'snap_token' => 'test-token',
            'qr_token' => 'test-qr-token',
            'nomor_antrian' => 1,
            'status_antrian' => 'pending',
            'nama' => 'Test Customer',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $pesanan->kode_pesanan . '200' . $pesanan->total . $serverKey);

        $response = $this->postJson('/midtrans/callback', [
            'order_id' => $pesanan->kode_pesanan,
            'status_code' => '200',
            'gross_amount' => (string) $pesanan->total,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-123',
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('pesanans', [
            'id' => $pesanan->id,
            'status_bayar' => 1,
            'metode_bayar' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-123',
        ]);
    }

    #[Test]
    public function callback_updates_status_to_batal_on_deny(): void
    {
        $pesanan = Pesanan::create([
            'user_id' => $this->customer->id,
            'vendor_id' => $this->vendor->id,
            'kode_pesanan' => 'ORD-TEST-1234567891',
            'total' => 30000,
            'status_bayar' => 0,
            'qr_token' => 'test-qr-token',
            'nomor_antrian' => 1,
            'status_antrian' => 'pending',
            'nama' => 'Test Customer',
        ]);

        $serverKey = config('midtrans.server_key');
        $signatureKey = hash('sha512', $pesanan->kode_pesanan . '200' . $pesanan->total . $serverKey);

        $this->postJson('/midtrans/callback', [
            'order_id' => $pesanan->kode_pesanan,
            'status_code' => '200',
            'gross_amount' => (string) $pesanan->total,
            'signature_key' => $signatureKey,
            'transaction_status' => 'deny',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-456',
        ]);

        $this->assertDatabaseHas('pesanans', [
            'id' => $pesanan->id,
            'status_bayar' => 2,
        ]);
    }

    #[Test]
    public function callback_rejects_invalid_signature(): void
    {
        $pesanan = Pesanan::create([
            'user_id' => $this->customer->id,
            'vendor_id' => $this->vendor->id,
            'kode_pesanan' => 'ORD-TEST-1234567892',
            'total' => 30000,
            'status_bayar' => 0,
            'qr_token' => 'test-qr-token',
            'nomor_antrian' => 1,
            'status_antrian' => 'pending',
            'nama' => 'Test Customer',
        ]);

        $this->postJson('/midtrans/callback', [
            'order_id' => $pesanan->kode_pesanan,
            'status_code' => '200',
            'gross_amount' => (string) $pesanan->total,
            'signature_key' => 'invalid-signature',
            'transaction_status' => 'settlement',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'midtrans-trx-789',
        ])->assertStatus(403);

        $this->assertDatabaseHas('pesanans', [
            'id' => $pesanan->id,
            'status_bayar' => 0,
        ]);
    }

    #[Test]
    public function midtrans_callback_route_exists(): void
    {
        $routes = Route::getRoutes();
        $found = false;
        
        foreach ($routes as $route) {
            if ($route->uri() === 'midtrans/callback' && in_array('POST', $route->methods())) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, 'Route POST midtrans/callback not found');
    }
}