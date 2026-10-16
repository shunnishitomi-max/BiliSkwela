<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Tests\InventoryTestCase;

class CatalogTest extends InventoryTestCase
{
    public function test_category_resource_crud_and_unique_validation(): void
    {
        $this->actingAs($this->actor());
        $this->post('/categories', ['name' => 'Writing', 'description' => 'Pens'])->assertRedirect('/categories');
        $c = Category::firstOrFail();
        $this->get('/categories/'.$c->id)->assertOk();
        $this->get('/categories/'.$c->id.'/edit')->assertOk();
        $this->post('/categories', ['name' => 'Writing'])->assertSessionHasErrors('name');
        $this->put('/categories/'.$c->id, ['name' => 'Stationery'])->assertRedirect('/categories');
        $this->delete('/categories/'.$c->id)->assertRedirect('/categories');
        $this->assertModelMissing($c);
    }

    public function test_supplier_crud_and_invalid_email(): void
    {
        $this->actingAs($this->actor());
        $this->post('/suppliers', ['name' => 'Partner', 'email' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/suppliers', ['name' => 'Partner', 'email' => 'contact@example.test'])->assertRedirect('/suppliers');
        $s = Supplier::firstOrFail();
        $this->get('/suppliers/'.$s->id)->assertOk();
        $this->put('/suppliers/'.$s->id, ['name' => 'Updated Partner'])->assertRedirect('/suppliers');
        $this->delete('/suppliers/'.$s->id)->assertRedirect('/suppliers');
        $this->assertModelMissing($s);
    }

    public function test_product_crud_and_server_validation_preserve_stock(): void
    {
        $p = $this->product();
        $this->actingAs($this->actor());
        $input = ['sku' => 'P-002', 'name' => 'Pad Paper', 'category_id' => $p->category_id, 'supplier_id' => $p->supplier_id, 'price' => '12.50', 'minimum_stock' => 3];
        $this->post('/products', [...$input, 'quantity' => 100])->assertSessionHasErrors('quantity');
        $this->post('/products', [...$input, 'price' => '-1'])->assertSessionHasErrors('price');
        $this->post('/products', $input)->assertRedirect('/products');
        $new = Product::where('sku', 'P-002')->firstOrFail();
        $this->assertSame(0, $new->quantity);
        $this->get('/products/'.$new->id)->assertOk();
        $this->get('/products/'.$new->id.'/edit')->assertOk();
        $this->put('/products/'.$new->id, [...$input, 'name' => 'Updated Paper'])->assertRedirect('/products');
        $this->delete('/products/'.$new->id)->assertRedirect('/products');
        $this->assertModelMissing($new);
    }

    public function test_product_relationships_reject_unknown_keys_and_duplicate_sku(): void
    {
        $p = $this->product();
        $this->actingAs($this->actor());
        $this->post('/products', ['sku' => $p->sku, 'name' => 'New', 'category_id' => 999, 'supplier_id' => 999, 'price' => 10, 'minimum_stock' => 1])->assertSessionHasErrors(['sku', 'category_id', 'supplier_id']);
    }
}
