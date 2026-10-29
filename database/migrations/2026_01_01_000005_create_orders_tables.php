<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
 
            // Snapshot alamat (supaya tidak berubah walau alamat master diedit)
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('shipping_address');
 
            $table->string('courier');
            $table->string('courier_service');
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('discount')->default(0);
            $table->string('voucher_code')->nullable();
            $table->unsignedBigInteger('service_fee')->default(0);
            $table->unsignedBigInteger('total');
 
            $table->string('payment_method');
            $table->string('payment_status')->default('pending');   // pending | paid | refunded
            $table->string('status')->default('pending_payment');
            $table->string('tracking_number')->nullable();
            $table->text('cancel_reason')->nullable();
            $table->text('return_reason')->nullable();
 
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('packed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
 
            $table->index('status');
        });
 
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_label');
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('cost_price')->default(0);
            $table->unsignedInteger('quantity');
            $table->timestamps();
        });
 
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
               $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
 
            $table->unique(['user_id', 'product_id', 'order_id']);
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};