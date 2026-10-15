<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate(['product_id' => ['nullable', 'integer', 'exists:products,id'], 'type' => ['nullable', Rule::in(['IN', 'OUT'])], 'page' => ['nullable', 'integer', 'min:1']]);
        $movements = StockMovement::with(['product', 'user'])->when($filters['product_id'] ?? null, fn ($q, $id) => $q->where('product_id', $id))->when($filters['type'] ?? null, fn ($q, $v) => $q->where('type', $v))->latest('id')->paginate(10)->withQueryString();

        return Inertia::render('Stock', ['movements' => $movements, 'products' => Product::orderBy('name')->get(['id', 'name', 'sku', 'quantity']), 'filters' => $filters]);
    }

    public function store(Request $request, InventoryService $inventory)
    {
        $data = $request->validate(['product_id' => ['required', 'integer', 'exists:products,id'], 'type' => ['required', Rule::in(['IN', 'OUT'])], 'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'], 'remarks' => ['nullable', 'string', 'max:255']]);
        abort_unless($request->user()->can($data['type'] === 'IN' ? 'stock.in' : 'stock.out'), 403);
        $inventory->record($data, $request->user());

        return to_route('stock.index')->with('success', 'Stock movement recorded.');
    }
}
