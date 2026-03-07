<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('subscription_status', ['none', 'pending', 'active', 'rejected', 'expired'])
                  ->default('none')
                  ->after('remember_token');
            $table->enum('subscription_type', ['monthly', 'quarterly', 'semi_yearly', 'yearly'])->nullable()->after('subscription_status');
            $table->timestamp('subscription_requested_at')->nullable()->after('subscription_type');
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_requested_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'subscription_status',
                'subscription_type',
                'subscription_requested_at',
                'subscription_expires_at',
            ]);
        });
    }
};
