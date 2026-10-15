<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150)->unique();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 40)->unique();
            $table->string('name', 150)->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('supplier_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('minimum_stock')->default(5);
            $table->timestamps();
        });
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->enum('type', ['IN', 'OUT']);
            $table->unsignedInteger('quantity');
            $table->string('remarks', 255)->nullable();
            $table->timestamps();
            $table->index(['product_id', 'created_at']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('products');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('categories');
    }
};
