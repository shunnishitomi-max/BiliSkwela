<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return Inertia::render('Dashboard', [
            'stats' => ['products' => Product::count(), 'categories' => Category::count(), 'suppliers' => Supplier::count(), 'units' => (int) Product::sum('quantity'), 'low' => Product::whereColumn('quantity', '<=', 'minimum_stock')->count()],
            'lowStock' => Product::with('category')->whereColumn('quantity', '<=', 'minimum_stock')->orderBy('quantity')->limit(8)->get(),
            'recent' => StockMovement::with(['product', 'user'])->latest('id')->limit(6)->get(),
        ]);
    }
}
