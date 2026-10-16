<?php

namespace Tests\Feature;

use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Database\QueryException;
use Tests\InventoryTestCase;

class StockTest extends InventoryTestCase
{
    public function test_stock_balance_rolls_back_when_ledger_insert_fails(): void
    {
        $product = $this->product();
        $actor = $this->actor();
        $actor->delete();

        try {
            app(InventoryService::class)->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => 4], $actor);
            $this->fail('Expected the foreign key to reject the missing author.');
        } catch (QueryException $exception) {
            $this->assertSame(0, $product->fresh()->quantity);
            $this->assertDatabaseCount('stock_movements', 0);
        }
    }

    public function test_stock_limit_rejects_overflow_without_mutating_history(): void
    {
        $product = $this->product();
        $actor = $this->actor();
        app(InventoryService::class)->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => 2147483647], $actor);

        $this->actingAs($actor)->post('/stock', ['product_id' => $product->id, 'type' => 'IN', 'quantity' => 1])->assertSessionHasErrors('quantity');
        $this->assertSame(2147483647, $product->fresh()->quantity);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_stock_ledger_balances_and_staff_can_only_issue_stock(): void
    {
        $p = $this->product();
        $admin = $this->actor();
        $staff = $this->actor('Staff');
        $this->actingAs($admin);
        $this->post('/stock', ['product_id' => $p->id, 'type' => 'IN', 'quantity' => 12])->assertRedirect('/stock');
        $this->actingAs($staff)->post('/stock', ['product_id' => $p->id, 'type' => 'OUT', 'quantity' => 5])->assertRedirect('/stock');
        $this->assertSame(7, $p->fresh()->quantity);
        $this->assertDatabaseCount('stock_movements', 2);
        $this->assertDatabaseHas('stock_movements', ['user_id' => $staff->id, 'type' => 'OUT', 'quantity' => 5]);
        $balance = StockMovement::where('type', 'IN')->sum('quantity') - StockMovement::where('type', 'OUT')->sum('quantity');
        $this->assertSame(7, (int) $balance);
    }

    public function test_rejected_movements_leave_balance_and_history_unchanged(): void
    {
        $p = $this->product();
        $u = $this->actor();
        app(InventoryService::class)->record(['product_id' => $p->id, 'type' => 'IN', 'quantity' => 2], $u);
        $this->actingAs($u);
        $this->post('/stock', ['product_id' => $p->id, 'type' => 'OUT', 'quantity' => 3])->assertSessionHasErrors('quantity');
        foreach ([0, -1, '1.5', 'not-a-number'] as $q) {
            $this->post('/stock', ['product_id' => $p->id, 'type' => 'IN', 'quantity' => $q])->assertSessionHasErrors('quantity');
        }
        $this->assertSame(2, $p->fresh()->quantity);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_used_categories_suppliers_and_products_cannot_be_deleted(): void
    {
        $p = $this->product();
        $u = $this->actor();
        app(InventoryService::class)->record(['product_id' => $p->id, 'type' => 'IN', 'quantity' => 1], $u);
        $this->actingAs($u);
        $this->delete('/categories/'.$p->category_id)->assertSessionHasErrors('delete');
        $this->delete('/suppliers/'.$p->supplier_id)->assertSessionHasErrors('delete');
        $this->delete('/products/'.$p->id)->assertSessionHasErrors('delete');
        $this->assertModelExists($p);
        $this->assertDatabaseCount('stock_movements', 1);
    }
}
