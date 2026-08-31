<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected User $kepalaGudang;
    protected User $management;
    protected Barang $barang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->staff = User::factory()->staff()->create();
        $this->kepalaGudang = User::factory()->kepalaGudang()->create();
        $this->management = User::factory()->management()->create();

        $kategori = Kategori::create(['nama_kategori' => 'General']);
        $this->barang = Barang::create([
            'nama_barang'  => 'Barang Uji RBAC',
            'kategori_id'  => $kategori->id,
            'stok'         => 10,
            'stok_minimum' => 2,
        ]);
    }

    /**
     * Test Dashboard access: All 4 roles can access dashboard.
     */
    public function test_all_roles_can_access_dashboard(): void
    {
        $this->actingAs($this->admin)->get(route('inventory.dashboard'))->assertStatus(200);
        $this->actingAs($this->staff)->get(route('inventory.dashboard'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.dashboard'))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.dashboard'))->assertStatus(200);
    }

    /**
     * Test Admin permissions: Admin has full access.
     */
    public function test_admin_has_full_management_access(): void
    {
        $this->actingAs($this->admin)->get(route('inventory.user.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('inventory.kategori.index'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('inventory.barang.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('inventory.transaksi.opname.create'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('inventory.laporan.transaksi'))->assertStatus(200);
        $this->actingAs($this->admin)->get(route('inventory.log-aktivitas'))->assertStatus(200);
    }

    /**
     * Test Staff permissions: Can input transactions, cannot manage user / opname / reports.
     */
    public function test_staff_permissions(): void
    {
        // Allowed
        $this->actingAs($this->staff)->get(route('inventory.barang.index'))->assertStatus(200);
        $this->actingAs($this->staff)->get(route('inventory.transaksi.masuk.create'))->assertStatus(200);
        $this->actingAs($this->staff)->get(route('inventory.transaksi.keluar.create'))->assertStatus(200);

        // Denied
        $this->actingAs($this->staff)->get(route('inventory.user.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->staff)->get(route('inventory.kategori.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->staff)->get(route('inventory.transaksi.approval.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->staff)->get(route('inventory.transaksi.opname.create'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->staff)->get(route('inventory.laporan.transaksi'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->staff)->get(route('inventory.log-aktivitas'))->assertRedirect(route('inventory.dashboard'));
    }

    /**
     * Test Kepala Gudang permissions:
     * Allowed: Barang show/create/edit, Kategori, Opname, Laporan, Log
     * Denied: Delete barang, User management
     */
    public function test_kepala_gudang_permissions(): void
    {
        // Allowed
        $this->actingAs($this->kepalaGudang)->get(route('inventory.barang.index'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.barang.show', $this->barang->id))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.barang.create'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.barang.edit', $this->barang->id))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.kategori.index'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.transaksi.approval.index'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.transaksi.opname.create'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.transaksi.opname.history'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.laporan.transaksi'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.laporan.stok'))->assertStatus(200);
        $this->actingAs($this->kepalaGudang)->get(route('inventory.log-aktivitas'))->assertStatus(200);

        // Denied: User Management & Delete Barang
        $this->actingAs($this->kepalaGudang)->get(route('inventory.user.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->kepalaGudang)->delete(route('inventory.barang.destroy', $this->barang->id))->assertRedirect(route('inventory.dashboard'));
    }

    /**
     * Test Management permissions: Read-only monitoring.
     */
    public function test_management_permissions(): void
    {
        // Allowed
        $this->actingAs($this->management)->get(route('inventory.barang.index'))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.barang.show', $this->barang->id))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.transaksi.opname.history'))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.laporan.transaksi'))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.laporan.stok'))->assertStatus(200);
        $this->actingAs($this->management)->get(route('inventory.log-aktivitas'))->assertStatus(200);

        // Denied
        $this->actingAs($this->management)->get(route('inventory.user.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->management)->get(route('inventory.barang.create'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->management)->get(route('inventory.transaksi.masuk.create'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->management)->get(route('inventory.transaksi.keluar.create'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->management)->get(route('inventory.transaksi.approval.index'))->assertRedirect(route('inventory.dashboard'));
        $this->actingAs($this->management)->get(route('inventory.transaksi.opname.create'))->assertRedirect(route('inventory.dashboard'));
    }
}
