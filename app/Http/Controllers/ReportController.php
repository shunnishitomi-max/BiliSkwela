<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate(['from' => ['nullable', 'date'], 'to' => ['nullable', 'date', ...($request->filled('from') ? ['after_or_equal:from'] : [])], 'page' => ['nullable', 'integer', 'min:1']]);
        $query = StockMovement::query()->when($filters['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))->when($filters['to'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d));

        return Inertia::render('Reports', [
            'totals' => ['in' => (int) (clone $query)->where('type', 'IN')->sum('quantity'), 'out' => (int) (clone $query)->where('type', 'OUT')->sum('quantity'), 'units' => (int) Product::sum('quantity')],
            'products' => Product::with(['category', 'supplier'])->orderBy('name')->get(),
            'movements' => (clone $query)->with(['product', 'user'])->latest('id')->paginate(20)->withQueryString(),
            'filters' => $filters, 'generatedAt' => now()->toIso8601String(),
        ]);
    }
}
