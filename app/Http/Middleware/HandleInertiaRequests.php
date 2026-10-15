<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $user = $request->user();

        return [...parent::share($request),
            'auth' => ['user' => $user ? ['id' => $user->id, 'name' => $user->name, 'email' => $user->email,
                'roles' => $user->getRoleNames(), 'permissions' => ($user->hasRole('Admin') ? ["stock.view", "stock.in", "stock.out", "reports.view", "users.manage", "categories.view", "categories.create", "categories.edit", "categories.delete", "suppliers.view", "suppliers.create", "suppliers.edit", "suppliers.delete", "products.view", "products.create", "products.edit", "products.delete"] : [])] : null],
            'flash' => ['success' => fn () => $request->session()->get('success')],
        ];
    }
}
