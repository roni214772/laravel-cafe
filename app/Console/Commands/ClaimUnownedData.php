<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Room;
use App\Models\User;
use Illuminate\Console\Command;

class ClaimUnownedData extends Command
{
    protected $signature   = 'cafe:claim-data {email : The email of the user to assign unowned rooms and products to}';
    protected $description = 'Assign all unowned (user_id = NULL) rooms and products to the specified user';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error("No user found with email: {$this->argument('email')}");
            return self::FAILURE;
        }

        $rooms    = Room::whereNull('user_id')->update(['user_id' => $user->id]);
        $products = Product::whereNull('user_id')->update(['user_id' => $user->id]);

        $this->info("Assigned {$rooms} room(s) and {$products} product(s) to {$user->name} ({$user->email})");

        return self::SUCCESS;
    }
}
