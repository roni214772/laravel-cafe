<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ActivateAllUsers extends Command
{
    protected $signature   = 'cafe:activate-all';
    protected $description = 'Activate all existing users with 1 year subscription';

    public function handle(): int
    {
        $count = User::where('subscription_status', 'none')
            ->where('email', '!=', 'bruskefrin47@gmail.com')
            ->update([
                'subscription_status'      => 'active',
                'subscription_type'        => 'monthly',
                'subscription_expires_at'  => now()->addYear(),
            ]);

        $this->info("{$count} kullanıcı aktifleştirildi (1 yıllık abonelik).");

        return self::SUCCESS;
    }
}
