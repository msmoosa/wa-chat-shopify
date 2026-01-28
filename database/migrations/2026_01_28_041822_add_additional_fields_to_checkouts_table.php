<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Enums\CheckoutStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->string('checkout_token')->nullable()->after('shopify_checkout_id');
            $table->string('cart_token')->nullable()->after('checkout_token');
            $table->text('abandoned_checkout_url')->nullable()->after('cart_token');
            $table->timestamp('checkout_updated_at')->nullable()->after('checkout_created_at');
            $table->string('currency', 3)->nullable()->after('total_price');
            $table->decimal('total_price_usd', 10, 2)->nullable()->after('currency');
            $table->string('status')->nullable()->after('total_price_usd')->default(CheckoutStatus::OPEN);
            
            // Indexes for common queries
            $table->index('checkout_token');
            $table->index('cart_token');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn([
                'checkout_token',
                'cart_token',
                'abandoned_checkout_url',
                'checkout_updated_at',
                'currency',
                'total_price_usd',
                'status',
            ]);
        });
    }
};
