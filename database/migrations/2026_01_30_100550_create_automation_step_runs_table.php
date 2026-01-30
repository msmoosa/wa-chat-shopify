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
        Schema::create('automation_step_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id')->constrained()->onDelete('cascade');
            $table->foreignId('automation_step_id')->constrained()->onDelete('cascade');
            $table->foreignId('checkout_id')->constrained()->onDelete('cascade');
            $table->string('channel')->nullable(); // whatsapp, sms
            $table->string('status')->default('pending'); // pending, sent, failed, skipped
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('provider')->nullable(); // twilio, meta, etc.
            $table->string('provider_message_id')->nullable();
            $table->decimal('cost', 10, 4)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            
            $table->index('automation_id');
            $table->index('automation_step_id');
            $table->index('checkout_id');
            $table->index('status');
            $table->index('scheduled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('automation_step_runs');
    }
};
