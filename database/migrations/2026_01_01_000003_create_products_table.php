<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('material')->nullable();
            $table->text('care')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_price')->nullable();
              $table->unsignedBigInteger('cost_price')->default(0);   // harga modal (untuk laporan laba)
            $table->unsignedInteger('weight')->default(300);        // gram
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sold_count')->default(0);
            $table->timestamps();
        });
 
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('color');
            $table->string('size');
            $table->string('sku')->unique();
            $table->unsignedInteger('stock')->default(0);
            $table->string('rack_location')->nullable();            // contoh: A-12
            $table->timestamps();
 
            $table->unique(['product_id', 'color', 'size']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};