<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TrendyolService;
use App\Services\YemeksepetiService;
use App\Services\GetirService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchPlatformOrders extends Command
{
    protected $signature = 'orders:fetch-platforms';
    protected $description = 'Trendyol, Yemeksepeti ve Getir platformlarından yeni siparişleri çek';

    public function handle(): int
    {
        $users = User::where('role', 'owner')
            ->where('subscription_status', 'active')
            ->get();

        $totalNew = 0;

        foreach ($users as $user) {
            // Trendyol
            if ($user->getSetting('trendyol_api_key')) {
                $count = (new TrendyolService())->fetchNewOrders($user);
                if ($count > 0) {
                    $totalNew += $count;
                    $this->info("Trendyol: {$count} yeni sipariş ({$user->name})");
                    $this->broadcastKitchen($user);
                }
            }

            // Yemeksepeti
            if ($user->getSetting('ys_api_key')) {
                $count = (new YemeksepetiService())->fetchNewOrders($user);
                if ($count > 0) {
                    $totalNew += $count;
                    $this->info("Yemeksepeti: {$count} yeni sipariş ({$user->name})");
                    $this->broadcastKitchen($user);
                }
            }

            // Getir
            if ($user->getSetting('getir_api_token')) {
                $count = (new GetirService())->fetchNewOrders($user);
                if ($count > 0) {
                    $totalNew += $count;
                    $this->info("Getir: {$count} yeni sipariş ({$user->name})");
                    $this->broadcastKitchen($user);
                }
            }
        }

        if ($totalNew > 0) {
            $this->info("Toplam {$totalNew} yeni paket sipariş eklendi.");
        }

        return self::SUCCESS;
    }

    private function broadcastKitchen(User $user): void
    {
        try {
            $mutfak = app(\App\Http\Controllers\MutfakController::class);
            broadcast(new \App\Events\KitchenUpdated(
                $user->id,
                $mutfak->getOrdersPublic()
            ))->toOthers();
        } catch (\Throwable $e) {
            // Reverb yoksa sessizce geç
        }
    }
}
