<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    public function record(array $data, User $actor): StockMovement
    {
        return DB::transaction(function () use ($data, $actor) {
            $product = Product::query()->lockForUpdate()->findOrFail($data['product_id']);
            $amount = (int) $data['quantity'];
            if ($amount < 1 || ! in_array($data['type'], ['IN', 'OUT'], true)) {
                throw ValidationException::withMessages(['quantity' => 'Enter a valid movement with a positive whole number.']);
            }
            if ($data['type'] === 'OUT' && $amount > $product->quantity) {
                throw ValidationException::withMessages(['quantity' => 'Not enough stock. Available: '.$product->quantity.'.']);
            }
            $balance = $product->quantity + ($data['type'] === 'IN' ? $amount : -$amount);
            if ($balance > 2147483647) {
                throw ValidationException::withMessages(['quantity' => 'This movement exceeds the supported stock limit.']);
            }
            $product->quantity = $balance;
            $product->save();

            return StockMovement::create([
                'product_id' => $product->id, 'user_id' => $actor->id, 'type' => $data['type'],
                'quantity' => $amount, 'remarks' => $data['remarks'] ?? null,
            ]);
        }, 3);
    }
}
