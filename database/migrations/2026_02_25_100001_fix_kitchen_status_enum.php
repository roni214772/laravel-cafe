<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('kitchen_status', ['draft', 'pending', 'sent', 'ready', 'notified'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->enum('kitchen_status', ['pending', 'ready'])
                  ->default('pending')
                  ->change();
        });
    }
};
