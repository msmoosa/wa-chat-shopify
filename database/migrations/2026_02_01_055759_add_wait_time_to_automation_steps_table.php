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
        Schema::table('automation_steps', function (Blueprint $table) {
            $table->integer('wait_time')->nullable()->after('step_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('automation_steps', function (Blueprint $table) {
            $table->dropColumn('wait_time');
        });
    }
};
