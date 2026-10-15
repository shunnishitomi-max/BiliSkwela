<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserRoleController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'page' => ['nullable', 'integer', 'min:1']]);

        return Inertia::render('Users', ['users' => User::with('roles')->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$s.'%')->orWhere('email', 'like', '%'.$s.'%')))->orderBy('name')->paginate(10)->withQueryString(), 'filters' => $filters]);
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(['Admin', 'Manager', 'Staff'])]]);

        return Cache::lock('biliskwela-role-change', 10)->block(5, function () use ($user, $data) {
            $user->refresh()->unsetRelation('roles');
            if ($user->hasRole('Admin') && $data['role'] !== 'Admin' && User::role('Admin')->count() <= 1) {
                return back()->withErrors(['role' => 'Keep at least one Admin account.']);
            }
            $user->syncRoles([$data['role']]);

            return to_route('users.index')->with('success', 'User role updated.');
        });
    }
}
