<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RealisticDemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Sample shop data is limited to local/testing environments.');
        }

        $this->call(DemoSeeder::class);
        $anchor = now()->startOfDay();
        $actors = User::whereIn('email', ['admin@biliskwela.test', 'manager@biliskwela.test', 'staff@biliskwela.test'])->get()->keyBy('email');
        $suppliers = [
            ['Sampaguita School Supply Trading', 'orders@sampaguita-supplies.example.test', 'Warehouse 3, Mabini Street, Quezon City'],
            ['Balangay Paper & Packaging', 'sales@balangay-paper.example.test', 'Unit 8, Rizal Avenue, Caloocan City'],
            ['Likha Arts Distribution', 'orders@likha-arts.example.test', '2F, Bonifacio Street, Pasig City'],
            ['Tala Office Essentials', 'sales@tala-office.example.test', 'Unit 12, National Road, San Pedro, Laguna'],
            ['Paaralan Stationery Wholesale', 'orders@paaralan.example.test', 'Warehouse 2, P. Burgos Street, Marikina City'],
        ];
        $catalog = [
            ['Writing', 'Ballpoint Pen Black 0.7mm', 12, 20, 0, 55],
            ['Writing', 'Ballpoint Pen Red 0.7mm', 12, 15, 0, 12],
            ['Writing', 'Gel Pen Blue 0.5mm', 25, 12, 0, 40],
            ['Writing', 'Mechanical Pencil 0.5mm', 35, 10, 0, 7],
            ['Writing', 'Pencil Lead 0.5mm HB - 12 pcs', 18, 10, 0, 25],
            ['Writing', 'Whiteboard Marker Black', 32, 10, 3, 3],
            ['Writing', 'Highlighter Yellow', 28, 12, 3, 40],
            ['Paper', 'Composition Notebook 80 Leaves', 28, 20, 1, 80],
            ['Paper', 'Cattleya-style Notebook 100 Leaves', 42, 15, 1, 25],
            ['Paper', 'Intermediate Pad - 80 Sheets', 32, 15, 1, 7],
            ['Paper', 'Grade 1 Pad - 80 Sheets', 30, 12, 1, 40],
            ['Paper', 'Yellow Pad - 80 Sheets', 45, 12, 1, 12],
            ['Paper', 'Bond Paper A4 70gsm - 500 Sheets', 210, 5, 1, 25],
            ['Paper', 'Bond Paper Short 70gsm - 500 Sheets', 195, 5, 1, 3],
            ['Paper', 'Index Card 3 x 5 - Pack of 100', 48, 8, 1, 12],
            ['Art', 'Colored Pencils - 12 Colors', 75, 8, 2, 25],
            ['Art', 'Watercolor Set - 12 Colors', 65, 8, 2, 0],
            ['Art', 'Drawing Book A4 - 20 Sheets', 38, 10, 2, 40],
            ['Art', 'Poster Paint Red 15ml', 25, 8, 2, 7],
            ['Art', 'Paint Brush Set - 3 pcs', 42, 8, 2, 12],
            ['Accessories', 'Eraser White Medium', 8, 20, 4, 55],
            ['Accessories', 'Sharpener Single Hole', 10, 20, 4, 25],
            ['Accessories', 'Scissors 5-inch Round Tip', 38, 10, 4, 12],
            ['Accessories', 'Correction Tape 5mm x 6m', 32, 12, 4, 3],
            ['Office Essentials', 'Stapler No. 10', 85, 5, 3, 12],
            ['Office Essentials', 'Staple Wire No. 10 - 1000 pcs', 15, 12, 3, 40],
            ['Project Materials', 'Illustration Board 1/8', 18, 15, 2, 7],
            ['Project Materials', 'Colored Paper A4 - 20 Sheets', 35, 12, 2, 25],
            ['Project Materials', 'Long Brown Envelope', 8, 20, 1, 80],
            ['Project Materials', 'Long Plastic Envelope with Button', 18, 15, 4, 40],
            ['Office Essentials', 'Clear Tape 18mm x 20m', 22, 12, 3, 25],
            ['Project Materials', 'Glue White 40g', 28, 10, 2, 3],
        ];

        DB::transaction(function () use ($suppliers, $catalog, $actors, $anchor): void {
            $supplierIds = [];
            foreach ($suppliers as [$name, $email, $address]) {
                $supplierIds[] = Supplier::firstOrCreate(['name' => $name], ['email' => $email, 'address' => $address])->id;
            }

            foreach ($catalog as $index => [$group, $name, $price, $minimum, $supplier, $remaining]) {
                if (Product::where('name', $name)->exists()) {
                    continue;
                }
                $category = Category::firstOrCreate(['name' => $group], ['description' => $group.' stocked for classroom and school project needs.']);
                $product = Product::firstOrCreate(['sku' => 'BKS-'.str_pad((string) ($index + 101), 4, '0', STR_PAD_LEFT)], ['name' => $name, 'category_id' => $category->id, 'supplier_id' => $supplierIds[$supplier], 'price' => $price, 'minimum_stock' => $minimum]);
                if (! $product->wasRecentlyCreated) {
                    continue;
                }

                $issues = [];
                foreach ([18, 15, 12, 9, 6, 3, 1, 0] as $position => $daysAgo) {
                    $issues[] = [$daysAgo, 2 + (($index * 3 + $position * 2) % 9)];
                }
                $restock = $index % 3 === 0 ? 12 : 0;
                $opening = $remaining + array_sum(array_column($issues, 1)) - $restock;
                $service = app(InventoryService::class);
                $this->at($anchor->copy()->subDays(21)->setTime(1, 15), fn () => $service->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => $opening, 'remarks' => 'Sample opening count - stockroom inventory'], $actors['admin@biliskwela.test']));
                if ($restock > 0) {
                    $this->at($anchor->copy()->subDays(20)->setTime(2, 30), fn () => $service->record(['product_id' => $product->id, 'type' => 'IN', 'quantity' => $restock, 'remarks' => 'Sample delivery DR-'.str_pad((string) ($index + 201), 4, '0', STR_PAD_LEFT)], $actors['manager@biliskwela.test']));
                }
                foreach ($issues as $position => [$daysAgo, $quantity]) {
                    $remarks = ['Sample counter sales - morning batch', 'Sample counter sales - afternoon batch', 'Sample classroom supply request', 'Sample project supply purchase'][$position % 4];
                    $this->at($anchor->copy()->subDays($daysAgo)->setTime($daysAgo === 0 ? 0 : 2 + ($index % 6), 5 + $index), fn () => $service->record(['product_id' => $product->id, 'type' => 'OUT', 'quantity' => $quantity, 'remarks' => $remarks], $actors[$position % 3 === 0 ? 'manager@biliskwela.test' : 'staff@biliskwela.test']));
                }
            }
        });
    }

    private function at(Carbon $time, callable $action): void
    {
        $previous = Carbon::getTestNow();
        try {
            Carbon::setTestNow($time);
            $action();
        } finally {
            Carbon::setTestNow($previous);
        }
    }
}
