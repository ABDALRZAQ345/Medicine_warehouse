<?php

use App\Models\Medicine;
use App\Models\Order;
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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Medicine::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(Order::class)->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity');
            $table->float('price');
            $table->string('scientific_name');
            $table->string('trade_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
