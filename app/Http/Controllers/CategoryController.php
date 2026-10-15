<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class CategoryController extends Controller
{
    private function payload(string $mode, ?Category $record = null): array
    {
        return ['resource' => 'categories', 'mode' => $mode, 'record' => $record];
    }

    public function index(Request $request)
    {
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:100'], 'page' => ['nullable', 'integer', 'min:1']]);
        $records = Category::query()->when($filters['search'] ?? null, fn ($q, $s) => $q->where('name', 'like', '%'.$s.'%'))->withCount('products')->orderBy('name')->paginate(10)->withQueryString();

        return Inertia::render('Catalog', [...$this->payload('index'), 'records' => $records, 'filters' => $filters]);
    }

    public function create()
    {
        return Inertia::render('Catalog', $this->payload('create'));
    }

    public function show(Category $category)
    {
        return Inertia::render('Catalog', $this->payload('show', $category));
    }

    public function edit(Category $category)
    {
        return Inertia::render('Catalog', $this->payload('edit', $category));
    }

    private function validated(Request $request, ?Category $record = null): array
    {
        return $request->validate(['name' => ['required', 'string', 'max:100', Rule::unique('categories')->ignore($record?->id)], 'description' => ['nullable', 'string', 'max:255']]);
    }

    public function store(Request $request)
    {
        Category::create($this->validated($request));

        return to_route('categories.index')->with('success', 'Category created.');
    }

    public function update(Request $request, Category $category)
    {
        $category->update($this->validated($request, $category));

        return to_route('categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category)
    {
        $deleted = DB::transaction(function () use ($category) {
            $record = Category::query()->lockForUpdate()->findOrFail($category->id);
            if ($record->products()->exists()) {
                return false;
            }

            return $record->delete();
        });
        if (! $deleted) {
            return back()->withErrors(['delete' => 'This category is used by products. Reassign them before deleting.']);
        }

        return to_route('categories.index')->with('success', 'Category deleted.');
    }
}
