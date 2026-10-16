<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('DemoSeeder is limited to local/testing environments.');
        }
        $this->call(RolesAndPermissionsSeeder::class);
        foreach (['Admin', 'Manager', 'Staff'] as $role) {
            $email = strtolower($role).'@biliskwela.test';
            $user = User::firstOrCreate(['email' => $email], ['name' => 'Demo '.$role, 'password' => Hash::make('DemoPass123!')]);
            if ($user->wasRecentlyCreated) {
                $user->assignRole($role);
            }
        }
        $admin = User::where('email', 'admin@biliskwela.test')->firstOrFail();
        $supplier = Supplier::firstOrCreate(['name' => 'Eskwela Essentials'], ['email' => 'supplies@example.test', 'phone' => '09170000000', 'address' => 'Demo supplier address']);
        $samples = [['Writing', 'Ballpen Blue', 12.00, 35], ['Writing', 'Pencil No. 2', 8.00, 4], ['Paper', 'Spiral Notebook', 45.00, 20], ['Paper', 'Pad Paper', 25.00, 3], ['Art', 'Crayon Set', 55.00, 14], ['Art', 'Glue Stick', 20.00, 0], ['Accessories', 'Ruler 30cm', 15.00, 12], ['Accessories', 'Pencil Case', 65.00, 8]];
        foreach ($samples as $i => [$group,$name,$price,$quantity]) {
            $category = Category::firstOrCreate(['name' => $group], ['description' => $group.' essentials for school.']);
            $product = Product::firstOrCreate(['sku' => 'BS-'.str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT)], ['name' => $name, 'category_id' => $category->id, 'supplier_id' => $supplier->id, 'price' => $price, 'minimum_stock' => 5]);
            if ($product->wasRecentlyCreated && $quantity > 0) {
                app(InventoryService::class)->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => $quantity, 'remarks' => 'Opening demo stock'], $admin);
            }
        }
    }
}
