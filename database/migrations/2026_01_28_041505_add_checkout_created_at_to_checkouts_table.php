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
        Schema::table('checkouts', function (Blueprint $table) {
            $table->timestamp('checkout_created_at')->nullable()->after('data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkouts', function (Blueprint $table) {
            $table->dropColumn('checkout_created_at');
        });
    }
};
