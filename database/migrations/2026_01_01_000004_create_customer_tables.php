database/migrations/2026_01_01_000004_create_customer_tables.php
<?php
 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
 
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label');                 // Rumah, Kantor, dst.
            $table->string('recipient_name');
            $table->string('phone');
            $table->text('address');
            $table->string('city');
             $table->string('postal_code', 10);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
 
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
 
            $table->unique(['user_id', 'product_variant_id']);
        });
 
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
 
            $table->unique(['user_id', 'product_id']);
        });
 
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('description')->nullable();
            $table->string('type');                          // percent | fixed
            $table->unsignedBigInteger('value');
            $table->unsignedBigInteger('min_purchase')->default(0);
            $table->unsignedBigInteger('max_discount')->nullable();
            $table->unsignedInteger('quota')->default(100);
            $table->unsignedInteger('used')->default(0);
            $table->date('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('addresses');
    }
};