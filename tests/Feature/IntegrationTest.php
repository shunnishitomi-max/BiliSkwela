<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\InventoryService;
use Database\Seeders\DemoSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\InventoryTestCase;

class IntegrationTest extends InventoryTestCase
{
    public function test_inertia_shares_permissions_and_database_backed_dashboard(): void
    {
        $this->seed(DemoSeeder::class);
        $this->actingAs(User::where('email', 'staff@biliskwela.test')->firstOrFail());
        $this->get('/dashboard')->assertInertia(fn (Assert $page) => $page->component('Dashboard')->where('stats.products', 8)->has('lowStock', 3)->where('auth.user.roles.0', 'Staff')->has('auth.user.permissions', 5));
    }

    public function test_filters_and_date_validation(): void
    {
        $this->seed(DemoSeeder::class);
        $this->actingAs(User::where('email', 'admin@biliskwela.test')->firstOrFail());
        $this->get('/products?search=Pencil')->assertInertia(fn (Assert $page) => $page->component('Catalog')->has('records.data', 2));
        $this->get('/products?stock=low')->assertInertia(fn (Assert $page) => $page->has('records.data', 3));
        $this->get('/reports?from=2026-10-20&to=2026-10-01')->assertSessionHasErrors('to');
        $this->get('/stock?type=INVALID')->assertSessionHasErrors('type');
    }

    public function test_demo_seed_is_repeatable_without_resetting_existing_stock(): void
    {
        $this->seed(DemoSeeder::class);
        $p = Product::where('sku', 'BS-001')->firstOrFail();
        $u = User::where('email', 'admin@biliskwela.test')->firstOrFail();
        app(InventoryService::class)->record(['product_id' => $p->id, 'type' => 'OUT', 'quantity' => 1], $u);
        $count = StockMovement::count();
        $this->seed(DemoSeeder::class);
        $this->assertSame(34, $p->fresh()->quantity);
        $this->assertSame($count, StockMovement::count());
        $this->assertDatabaseCount('products', 8);
    }

    public function test_report_dates_filter_movements_and_totals_without_changing_current_inventory(): void
    {
        $product = $this->product();
        $actor = $this->actor();
        $service = app(InventoryService::class);
        $this->travelTo(now()->setDate(2026, 10, 1)->startOfDay());
        $service->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => 12], $actor);
        $this->travelTo(now()->setDate(2026, 10, 2)->startOfDay());
        $service->record(['product_id' => $product->id, 'type' => 'OUT', 'quantity' => 5], $actor);

        $this->actingAs($actor)->get('/reports?from=2026-10-02&to=2026-10-02')
            ->assertInertia(fn (Assert $page) => $page->component('Reports')
                ->where('totals.in', 0)
                ->where('totals.out', 5)
                ->where('totals.units', 7)
                ->has('movements.data', 1)
                ->where('movements.data.0.type', 'OUT')
                ->where('filters.from', '2026-10-02')
                ->where('products.0.quantity', 7));
        $this->travelBack();
    }
}
