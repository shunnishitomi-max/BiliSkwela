<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SupplierController extends Controller
{
    private function payload(string $mode, ?Supplier $record = null): array
    {
        return ['resource' => 'suppliers', 'mode' => $mode, 'record' => $record];
    }

    public function index(Request $request)
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'page' => ['nullable', 'integer', 'min:1']]);
        $records = Supplier::query()->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', '%'.$s.'%'))->withCount('products')->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Catalog', [...$this->payload('index'), 'records' => $records, 'filters' => $filters]);
    }

    public function create()
    {
        return Inertia::render('Catalog', $this->payload('create'));
    }

    public function show(Supplier $supplier)
    {
        return Inertia::render('Catalog', $this->payload('show', $supplier));
    }

    public function edit(Supplier $supplier)
    {
        return Inertia::render('Catalog', $this->payload('edit', $supplier));
    }

    private function validated(Request $request, ?Supplier $record = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:150', Rule::unique('suppliers')->ignore($record?->id)], 'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:30'], 'address' => ['nullable', 'string', 'max:255']]);
    }

    public function store(Request $request)
    {
        Supplier::create($this->validated($request));

        return to_route('suppliers.index')->with('success', 'Supplier created.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->validated($request, $supplier));

        return to_route('suppliers.index')->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier)
    {
        $deleted = DB::transaction(function () use ($supplier) {
            $record = Supplier::query()->lockForUpdate()->findOrFail($supplier->id);
            if ($record->products()->exists()) {
                return false;
            }

            return $record->delete();
        });
        if (! $deleted) {
            return back()->withErrors(['delete' => 'This supplier is used by products. Reassign them before deleting.']);
        }

        return to_route('suppliers.index')->with('success', 'Supplier deleted.');
    }
}
