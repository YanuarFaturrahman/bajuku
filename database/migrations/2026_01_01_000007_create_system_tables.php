<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
          Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type');                  // in | out | adjustment
            $table->integer('quantity');             // bertanda: + masuk, - keluar
            $table->unsignedInteger('stock_after');
            $table->string('reference')->nullable(); // no. order / no. PO
            $table->string('note')->nullable();
            $table->timestamps();
        });
 
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
 
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('stock_movements');
    }
};