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
        Schema::create('trades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buy_order_id');
            $table->foreignId('sell_order_id');
            $table->string('symbol');
            $table->decimal('price', 18, 8);
            $table->decimal('amount', 18, 8);
            $table->decimal('usd_value', 18, 8);
            $table->decimal('commission', 18, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trades');
    }
};
