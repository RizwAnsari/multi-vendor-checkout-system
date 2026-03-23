<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('vendor_id')->constrained();
            $table->decimal('subtotal', 10, 2);
            $table->enum('status', ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'])
                ->default('pending');
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
