<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        if (!$user) return;

        $categories = [
            ['name' => 'Kahve',         'image_path' => 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=500&fit=crop&q=80'],
            ['name' => 'Soğuk Kahve',   'image_path' => 'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=500&fit=crop&q=80'],
            ['name' => 'Sıcak İçecek',  'image_path' => 'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=500&fit=crop&q=80'],
            ['name' => 'Soğuk İçecek',  'image_path' => 'https://images.unsplash.com/photo-1497534446932-c925b458314e?w=500&fit=crop&q=80'],
            ['name' => 'Milkshake',     'image_path' => 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=500&fit=crop&q=80'],
            ['name' => 'Smoothie',      'image_path' => 'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=500&fit=crop&q=80'],
            ['name' => 'Kahvaltı',      'image_path' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=500&fit=crop&q=80'],
            ['name' => 'Krep',          'image_path' => 'https://images.unsplash.com/photo-1519676867240-f03562e64548?w=500&fit=crop&q=80'],
            ['name' => 'Waffle',        'image_path' => 'https://images.unsplash.com/photo-1562376552-0d160a2f238d?w=500&fit=crop&q=80'],
            ['name' => 'Tatlı',         'image_path' => 'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=500&fit=crop&q=80'],
            ['name' => 'Pastane',       'image_path' => 'https://images.unsplash.com/photo-1517093157656-b9eccef91cb1?w=500&fit=crop&q=80'],
            ['name' => 'Ana Yemek',     'image_path' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=500&fit=crop&q=80'],
            ['name' => 'Burger',        'image_path' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=500&fit=crop&q=80'],
            ['name' => 'Pizza',         'image_path' => 'https://images.unsplash.com/photo-1594007654729-407eedc4be65?w=500&fit=crop&q=80'],
            ['name' => 'Salata',        'image_path' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=500&fit=crop&q=80'],
            ['name' => 'Çorba',         'image_path' => 'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=500&fit=crop&q=80'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['user_id' => $user->id, 'name' => $cat['name']],
                ['image_path' => $cat['image_path']]
            );
        }
    }
}
