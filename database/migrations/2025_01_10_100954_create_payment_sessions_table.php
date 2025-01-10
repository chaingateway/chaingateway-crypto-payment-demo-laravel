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
        Schema::create('payment_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('Pending'); // Pending, Completed, or Failed
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade'); // Links to Wallet
            $table->decimal('amount', 18, 8)->nullable(); // Amount sent to this session
            $table->string('currency')->default('TRX'); // Currency of the amount
            $table->decimal('received_amount', 18, 8)->nullable(); // Amount sent to this session
            $table->string('webhook_id')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_sessions');
    }
};
