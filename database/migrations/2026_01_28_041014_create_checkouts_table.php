<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('shopify_checkout_id')->unique()->index();
            $table->string('order_id')->nullable()->index();
            $table->decimal('total_price', 10, 2)->nullable();
            $table->string('customer_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->boolean('buyer_accepts_marketing')->default(false);
            $table->text('tags')->nullable();
            $table->json('data')->nullable(); // Full JSON data from Shopify
            $table->timestamps();
            
            // Indexes for common queries
            $table->index('user_id');
            $table->index('shopify_checkout_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
