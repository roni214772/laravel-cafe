<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('menu_token', 32)->nullable()->unique()->after('remember_token');
        });

        // Mevcut kullanıcılara token ata
        DB::table('users')->whereNull('menu_token')->get()->each(function ($user) {
            DB::table('users')->where('id', $user->id)
                ->update(['menu_token' => Str::random(20)]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('menu_token');
        });
    }
};
