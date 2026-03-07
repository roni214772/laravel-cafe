<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Varsayılan fiyatları ekle
        DB::table('settings')->insert([
            ['key' => 'price_monthly', 'value' => '299.00', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'price_yearly',  'value' => '2990.00','created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
