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
        Schema::create('automation_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->onDelete('cascade');
            $table->string('type'); // delay, send_whatsapp, send_sms
            $table->json('config'); // message, template_id, delay_minutes
            $table->integer('step_order')->default(0);
            $table->timestamps();
            
            $table->index('automation_id');
            $table->index('step_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_steps');
    }
};
