<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ProductController extends Controller
{
    private function payload(string $mode, ?Product $record = null): array
    {
        return ['resource' => 'products', 'mode' => $mode, 'record' => $record?->load(['category', 'supplier']),
            'categories' => Category::orderBy('name')->get(['id', 'name']), 'suppliers' => Supplier::orderBy('name')->get(['id', 'name'])];
    }

    public function index(Request $request)
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'category_id' => ['nullable', 'integer', 'exists:categories,id'], 'stock' => ['nullable', Rule::in(['low', 'available', 'out'])], 'page' => ['nullable', 'integer', 'min:1']]);
        $records = Product::query()->with(['category', 'supplier'])
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->where(fn ($q) => $q->where('name', 'like', '%'.$s.'%')->orWhere('sku', 'like', '%'.$s.'%')))
            ->when($filters['category_id'] ?? null, fn ($q, $id) => $q->where('category_id', $id))
            ->when($filters['stock'] ?? null, function ($q, $s) {
                if ($s === 'low') {
                    $q->whereColumn('quantity', '<=', 'minimum_stock');
                }
                if ($s === 'available') {
                    $q->where('quantity', '>', 0);
                }
                if ($s === 'out') {
                    $q->where('quantity', 0);
                }
            })->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Catalog', [...$this->payload('index'), 'records' => $records, 'filters' => $filters]);
    }

    public function create()
    {
        return Inertia::render('Catalog', $this->payload('create'));
    }

    public function show(Product $product)
    {
        return Inertia::render('Catalog', $this->payload('show', $product));
    }

    public function edit(Product $product)
    {
        return Inertia::render('Catalog', $this->payload('edit', $product));
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'sku' => ['required', 'string', 'max:40', Rule::unique('products')->ignore($product?->id)],
            'name' => ['required', 'string', 'max:150', Rule::unique('products')->ignore($product?->id)],
            'category_id' => ['required', 'integer', 'exists:categories,id'], 'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
            'minimum_stock' => ['required', 'integer', 'min:0', 'max:2147483647'], 'quantity' => ['prohibited'],
        ]);
    }

    public function store(Request $request)
    {
        Product::create($this->validated($request));

        return to_route('products.index')->with('success', 'Product created. Record opening stock in Stock movements.');
    }

    public function update(Request $request, Product $product)
    {
        $product->update($this->validated($request, $product));

        return to_route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $deleted = DB::transaction(function () use ($product) {
            $locked = Product::query()->lockForUpdate()->findOrFail($product->id);
            if ($locked->quantity > 0 || $locked->stockMovements()->exists()) {
                return false;
            }

            return $locked->delete();
        });
        if (! $deleted) {
            return back()->withErrors(['delete' => 'Keep products with stock or history. Only unused zero-stock products can be deleted.']);
        }

        return to_route('products.index')->with('success', 'Product deleted.');
    }
}
