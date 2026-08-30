<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use App\Models\StockOpname;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdvancedFilteringSortingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Kategori $kategoriA;
    protected Kategori $kategoriB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();

        $this->kategoriA = Kategori::create(['nama_kategori' => 'Kategori Banner']);
        $this->kategoriB = Kategori::create(['nama_kategori' => 'Kategori Tinta']);
    }

    public function test_barang_index_defaults_to_newest_first_and_supports_sorting(): void
    {
        $barang1 = Barang::create([
            'nama_barang'  => 'Alpha Item',
            'kategori_id'  => $this->kategoriA->id,
            'stok'         => 10,
            'stok_minimum' => 5,
        ]);

        $barang2 = Barang::create([
            'nama_barang'  => 'Omega Item',
            'kategori_id'  => $this->kategoriB->id,
            'stok'         => 50,
            'stok_minimum' => 5,
        ]);

        // Default: Terbaru (barang2 before barang1)
        $response = $this->actingAs($this->admin)->get(route('inventory.barang.index'));
        $response->assertStatus(200);
        $response->assertSeeInOrder(['Omega Item', 'Alpha Item']);

        // Sort: Nama A-Z (Alpha before Omega)
        $responseAz = $this->actingAs($this->admin)->get(route('inventory.barang.index', ['sort' => 'nama_asc']));
        $responseAz->assertStatus(200);
        $responseAz->assertSeeInOrder(['Alpha Item', 'Omega Item']);

        // Filter: Kategori A only
        $responseKat = $this->actingAs($this->admin)->get(route('inventory.barang.index', ['kategori' => $this->kategoriA->id]));
        $responseKat->assertStatus(200);
        $responseKat->assertSee('Alpha Item');
        $responseKat->assertDontSee('Omega Item');
    }

    public function test_barang_index_filters_by_stock_status(): void
    {
        Barang::create([
            'nama_barang'  => 'Barang Aman',
            'kategori_id'  => $this->kategoriA->id,
            'stok'         => 20,
            'stok_minimum' => 5,
        ]);

        Barang::create([
            'nama_barang'  => 'Barang Menipis',
            'kategori_id'  => $this->kategoriA->id,
            'stok'         => 2,
            'stok_minimum' => 10,
        ]);

        // Filter status: kurang (stok < stok_minimum)
        $response = $this->actingAs($this->admin)->get(route('inventory.barang.index', ['status' => 'kurang']));
        $response->assertStatus(200);
        $response->assertSee('Barang Menipis');
        $response->assertDontSee('Barang Aman');

        // Filter status: normal (stok >= stok_minimum)
        $responseNormal = $this->actingAs($this->admin)->get(route('inventory.barang.index', ['status' => 'normal']));
        $responseNormal->assertStatus(200);
        $responseNormal->assertSee('Barang Aman');
        $responseNormal->assertDontSee('Barang Menipis');
    }

    public function test_laporan_transaksi_search_and_sorting(): void
    {
        $barang = Barang::create([
            'nama_barang'  => 'Spanduk Flexi 280g',
            'kategori_id'  => $this->kategoriA->id,
            'stok'         => 100,
            'stok_minimum' => 10,
        ]);

        BarangMasuk::create([
            'barang_id'   => $barang->id,
            'jumlah'      => 30,
            'sisa_jumlah' => 30,
            'tanggal'     => now()->subDays(2)->format('Y-m-d'),
            'sumber'      => 'PT Supplier Utama',
            'user_id'     => $this->staff->id,
        ]);

        BarangMasuk::create([
            'barang_id'   => $barang->id,
            'jumlah'      => 70,
            'sisa_jumlah' => 70,
            'tanggal'     => now()->format('Y-m-d'),
            'sumber'      => 'PT Mitra Abadi',
            'user_id'     => $this->staff->id,
        ]);

        // Filter search: Mitra
        $responseSearch = $this->actingAs($this->admin)->get(route('inventory.laporan.transaksi', [
            'search' => 'Mitra',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('PT Mitra Abadi');
        $responseSearch->assertDontSee('PT Supplier Utama');

        // Verify Document Number Generation
        $responseSearch->assertSee('IN-' . now()->format('Ymd'));
    }

    public function test_stock_opname_history_filtering(): void
    {
        $barang = Barang::create([
            'nama_barang'  => 'Tinta Eco Solvent',
            'kategori_id'  => $this->kategoriB->id,
            'stok'         => 15,
            'stok_minimum' => 5,
        ]);

        StockOpname::create([
            'barang_id'  => $barang->id,
            'stok_fisik' => 20,
            'selisih'    => 5, // Surplus
            'tanggal'    => now()->format('Y-m-d'),
        ]);

        StockOpname::create([
            'barang_id'  => $barang->id,
            'stok_fisik' => 10,
            'selisih'    => -5, // Defisit
            'tanggal'    => now()->format('Y-m-d'),
        ]);

        // Filter surplus only
        $responseSurplus = $this->actingAs($this->admin)->get(route('inventory.transaksi.opname.history', [
            'status' => 'surplus',
        ]));
        $responseSurplus->assertStatus(200);
        $responseSurplus->assertSee('Surplus (+5)');
        $responseSurplus->assertDontSee('Defisit (-5)');
        $responseSurplus->assertSee('OPN-' . now()->format('Ymd'));
    }

    public function test_user_management_search_and_role_filter(): void
    {
        $userKepala = User::factory()->create([
            'name'  => 'Budi Supervisor',
            'email' => 'budi.gudang@example.com',
            'role'  => 'kepala_gudang',
        ]);

        $userStaff = User::factory()->create([
            'name'  => 'Siti Operator',
            'email' => 'siti.staff@example.com',
            'role'  => 'staff',
        ]);

        // Filter by role kepala_gudang
        $responseRole = $this->actingAs($this->admin)->get(route('inventory.user.index', [
            'role' => 'kepala_gudang',
        ]));
        $responseRole->assertStatus(200);
        $responseRole->assertSee('Budi Supervisor');
        $responseRole->assertDontSee('Siti Operator');

        // Search by email
        $responseSearch = $this->actingAs($this->admin)->get(route('inventory.user.index', [
            'search' => 'siti.staff',
        ]));
        $responseSearch->assertStatus(200);
        $responseSearch->assertSee('Siti Operator');
        $responseSearch->assertDontSee('Budi Supervisor');
    }
}
