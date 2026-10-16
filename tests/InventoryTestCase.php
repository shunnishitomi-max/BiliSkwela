<?php

namespace Tests;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

abstract class InventoryTestCase extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->withoutVite();
    }

    protected function actor(string $role = 'Admin'): User
    {
        $u = User::factory()->create(['password' => Hash::make('Password123!')]);
        $u->assignRole($role);

        return $u;
    }

    protected function product(): Product
    {
        $category = Category::create(['name' => 'Paper']);
        $supplier = Supplier::create(['name' => 'School Supply Partner']);

        return Product::create(['sku' => 'P-001', 'name' => 'Notebook', 'category_id' => $category->id, 'supplier_id' => $supplier->id, 'price' => '35.00', 'minimum_stock' => 5]);
    }
}
