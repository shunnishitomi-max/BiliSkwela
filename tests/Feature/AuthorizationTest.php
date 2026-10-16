<?php

namespace Tests\Feature;

use Tests\InventoryTestCase;

class AuthorizationTest extends InventoryTestCase
{
    public function test_staff_cannot_modify_catalog_receive_stock_or_manage_roles(): void
    {
        $staff = $this->actor('Staff');
        $p = $this->product();
        $this->actingAs($staff);
        foreach (['/products/create', '/categories/create', '/suppliers/create', '/reports', '/users'] as $url) {
            $this->get($url)->assertForbidden();
        }
        foreach (['products', 'categories', 'suppliers'] as $resource) {
            $this->post('/'.$resource, [])->assertForbidden();
        }
        $this->put('/products/'.$p->id, [])->assertForbidden();
        $this->delete('/products/'.$p->id)->assertForbidden();
        $this->post('/stock', ['product_id' => $p->id, 'type' => 'IN', 'quantity' => 2])->assertForbidden();
        $this->assertSame(0, $p->fresh()->quantity);
    }

    public function test_manager_can_read_reports_but_not_change_roles(): void
    {
        $u = $this->actor('Manager');
        $target = $this->actor('Staff');
        $this->actingAs($u);
        $this->get('/reports')->assertOk();
        $this->get('/users')->assertForbidden();
        $this->put('/users/'.$target->id.'/role', ['role' => 'Admin'])->assertForbidden();
        $this->assertTrue($target->fresh()->hasRole('Staff'));
    }

    public function test_only_admin_can_assign_roles_and_last_admin_is_preserved(): void
    {
        $admin = $this->actor();
        $staff = $this->actor('Staff');
        $this->actingAs($admin);
        $this->put('/users/'.$admin->id.'/role', ['role' => 'Staff'])->assertSessionHasErrors('role');
        $this->put('/users/'.$staff->id.'/role', ['role' => 'Manager'])->assertRedirect('/users');
        $this->assertTrue($staff->fresh()->hasRole('Manager'));
        $this->put('/users/'.$staff->id.'/role', ['role' => 'Root'])->assertSessionHasErrors('role');
    }
}
