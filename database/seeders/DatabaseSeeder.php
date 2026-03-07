<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Test kullanıcısı oluştur
        User::factory()->create([
            'name' => 'Test Kullanıcı',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        // Ek kullanıcılar oluştur
        User::factory(5)->create();

        // Ürün seed'lerini çalıştır
        $this->call(ProductSeeder::class);

        // Kategori görselleri seed'ini çalıştır
        $this->call(CategorySeeder::class);

        // Masa ve örnek adisyon verilerini çalıştır
        $this->call(RoomSeeder::class);
    }
}
