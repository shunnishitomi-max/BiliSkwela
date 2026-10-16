<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Database\Seeders\RealisticDemoSeeder;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\InventoryTestCase;

class RealisticDemoSeederTest extends InventoryTestCase
{
    public function test_recent_stock_screens_sort_by_transaction_date(): void
    {
        $product = $this->product();
        $actor = $this->actor();
        $service = app(InventoryService::class);
        $newer = $service->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => 10], $actor);
        $older = $service->record(['product_id' => $product->id, 'type' => 'OUT', 'quantity' => 2], $actor);
        $older->forceFill(['created_at' => now()->subDays(3)])->save();
        $this->actingAs($actor)->get('/dashboard')->assertInertia(fn (Assert $page) => $page->where('recent.0.id', $newer->id));
        foreach (['/stock', '/reports'] as $path) {
            $this->get($path)->assertInertia(fn (Assert $page) => $page->where('movements.data.0.id', $newer->id));
        }
    }

    public function test_sample_shop_data_preserves_existing_stock_and_can_be_seeded_twice(): void
    {
        $existing = $this->product();
        app(InventoryService::class)->record(['product_id' => $existing->id, 'type' => 'IN', 'quantity' => 17], $this->actor());
        $this->seed(RealisticDemoSeeder::class);
        $this->assertDatabaseCount('products', 41);
        $this->assertSame(17, $existing->fresh()->quantity);
        $quantities = Product::orderBy('id')->pluck('quantity', 'id')->all();
        $movements = StockMovement::count();
        $balances = StockMovement::all()->groupBy('product_id')->map(fn ($rows) => $rows->sum(fn ($row) => $row->type === 'IN' ? $row->quantity : -$row->quantity))->sortKeys()->all();
        $this->assertSame($quantities, array_replace(array_fill_keys(array_keys($quantities), 0), $balances));

        $this->seed(RealisticDemoSeeder::class);
        $this->assertSame($movements, StockMovement::count());
        $this->assertSame($quantities, Product::orderBy('id')->pluck('quantity', 'id')->all());
    }
}
