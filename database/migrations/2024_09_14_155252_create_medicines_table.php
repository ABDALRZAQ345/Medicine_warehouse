<?php

use App\Models\Manufacturer;
use App\Models\Medicine;
use App\Models\User;
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
        Schema::create('medicines', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('trade_name');
            $table->string('type');
            $table->foreignIdFor(Manufacturer::class,'manufacturer_id');
            $table->integer('quantity');
            $table->float('price');
            $table->foreignIdFor(User::class,'creator_id');
            $table->timestamp('expires_at')->nullable();
            $table->fullText(['scientific_name','trade_name','type']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medicines');
    }
};
