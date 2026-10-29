<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('company_name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();
        });
 
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
 
            // sent -> confirmed -> shipped -> received   (atau rejected / cancelled)
            $table->string('status')->default('sent');
            $table->string('payment_status')->default('unpaid');   // unpaid | paid
              $table->date('expected_date')->nullable();        // target penerimaan
            $table->date('due_date')->nullable();             // jatuh tempo pembayaran
            $table->date('estimated_ready_date')->nullable(); // estimasi siap kirim (diisi supplier)
 
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('tax')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->text('notes')->nullable();
            $table->text('reject_reason')->nullable();
 
            $table->string('courier')->nullable();            // nama armada / kurir
            $table->string('vehicle_plate')->nullable();      // plat / resi
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
 
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('qty_ordered');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('qty_received')->default(0);
            $table->unsignedInteger('qty_rejected')->default(0);
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};