<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhilippineBrandsSeeder extends Seeder
{
    public const PRODUCTS = [
        'BKS-0101' => ['Ballpoint Pen Black 0.7mm', 'HBW Ballpoint Pen Black 0.7mm'],
        'BKS-0102' => ['Ballpoint Pen Red 0.7mm', 'Pilot Ballpoint Pen Red 0.7mm'],
        'BKS-0103' => ['Gel Pen Blue 0.5mm', 'Dong-A My Gel Pen Blue 0.5mm'],
        'BKS-0104' => ['Mechanical Pencil 0.5mm', 'Faber-Castell Mechanical Pencil 0.5mm'],
        'BKS-0105' => ['Pencil Lead 0.5mm HB - 12 pcs', 'Faber-Castell Pencil Lead 0.5mm HB'],
        'BKS-0106' => ['Whiteboard Marker Black', 'HBW Whiteboard Marker Black'],
        'BKS-0107' => ['Highlighter Yellow', 'STABILO BOSS Highlighter Yellow'],
        'BKS-0108' => ['Composition Notebook 80 Leaves', 'Veco Composition Notebook 80 Leaves'],
        'BKS-0109' => ['Cattleya-style Notebook 100 Leaves', 'Cattleya Notebook 100 Leaves'],
        'BKS-0110' => ['Intermediate Pad - 80 Sheets', 'Orions Intermediate Pad - 80 Sheets'],
        'BKS-0111' => ['Grade 1 Pad - 80 Sheets', 'Advance Grade 1 Pad - 80 Sheets'],
        'BKS-0112' => ['Yellow Pad - 80 Sheets', 'Veco Yellow Pad - 80 Sheets'],
        'BKS-0113' => ['Bond Paper A4 70gsm - 500 Sheets', 'Office Warehouse Copy Paper A4 70gsm - 500 Sheets'],
        'BKS-0114' => ['Bond Paper Short 70gsm - 500 Sheets', 'Advance Bond Paper Short - 500 Sheets'],
        'BKS-0115' => ['Index Card 3 x 5 - Pack of 100', 'Orions Index Card 3 x 5 - Pack of 100'],
        'BKS-0116' => ['Colored Pencils - 12 Colors', 'Faber-Castell Colored Pencils - 12 Colors'],
        'BKS-0117' => ['Watercolor Set - 12 Colors', 'Dong-A Watercolor Set - 12 Colors'],
        'BKS-0118' => ['Drawing Book A4 - 20 Sheets', 'Best Buy Drawing Book A4 - 20 Sheets'],
        'BKS-0119' => ['Poster Paint Red 15ml', 'Dong-A Poster Paint Red 15ml'],
        'BKS-0121' => ['Eraser White Medium', 'Faber-Castell White Eraser'],
        'BKS-0122' => ['Sharpener Single Hole', 'Mongol Single-Hole Sharpener'],
        'BKS-0123' => ['Scissors 5-inch Round Tip', 'Maped Round-Tip Scissors 5-inch'],
        'BKS-0124' => ['Correction Tape 5mm x 6m', 'HBW Correction Tape 5mm x 6m'],
        'BKS-0125' => ['Stapler No. 10', 'MAX Stapler No. 10'],
        'BKS-0126' => ['Staple Wire No. 10 - 1000 pcs', 'MAX Staple Wire No. 10 - 1000 pcs'],
        'BKS-0128' => ['Colored Paper A4 - 20 Sheets', 'Dong-A Colored Paper A4 - 20 Sheets'],
        'BKS-0129' => ['Long Brown Envelope', 'Veco Long Brown Envelope'],
        'BKS-0130' => ['Long Plastic Envelope with Button', 'HBW Long Plastic Envelope with Button'],
        'BKS-0131' => ['Clear Tape 18mm x 20m', 'Scotch Clear Tape 18mm x 20m'],
        'BKS-0132' => ['Glue White 40g', 'Elmer\'s White Glue 40g'],
        'BS-001' => ['Ballpen Blue', 'HBW Ballpen Blue'],
        'BS-002' => ['Pencil No. 2', 'Mongol Pencil No. 2'],
        'BS-003' => ['Spiral Notebook', 'Cattleya Spiral Notebook'],
        'BS-004' => ['Pad Paper', 'Orions Pad Paper'],
        'BS-005' => ['Crayon Set', 'Crayola Crayon Set'],
        'BS-006' => ['Glue Stick', 'Elmer\'s Glue Stick'],
        'BS-007' => ['Ruler 30cm', 'Maped Ruler 30cm'],
    ];

    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new \RuntimeException('Sample brand updates are limited to local/testing environments.');
        }

        DB::transaction(function (): void {
            foreach (self::PRODUCTS as $sku => [$previous, $name]) {
                $product = Product::where('sku', $sku)->where('name', $previous)->first();
                if ($product && ! Product::where('name', $name)->where('id', '!=', $product->id)->exists()) {
                    $product->update(['name' => $name]);
                }
            }
        });
    }
}
