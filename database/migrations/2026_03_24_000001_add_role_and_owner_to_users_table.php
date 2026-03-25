<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('owner')->after('password');
            $table->unsignedBigInteger('owner_id')->nullable()->after('role');
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Mevcut tüm kullanıcılar owner olarak ayarla
        DB::table('users')->whereNull('owner_id')->update(['role' => 'owner']);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['role', 'owner_id']);
        });
    }
};
