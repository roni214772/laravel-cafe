<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Category;
use App\Services\ImageOptimizer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Mevcut ürün ve kategori görsellerini optimize et (WebP + sıkıştırma + thumbnail)';

    public function handle()
    {
        $this->info('Görsel optimizasyonu başlıyor...');
        $optimized = 0;
        $skipped   = 0;
        $totalSaved = 0;

        // --- Ürün görselleri ---
        $products = Product::whereNotNull('image_url')
            ->where('image_url', 'like', '/storage/%')
            ->get();

        $this->info("Ürün görselleri: {$products->count()} adet");

        foreach ($products as $product) {
            $storagePath = str_replace('/storage/', '', $product->image_url);

            if (!Storage::disk('public')->exists($storagePath)) {
                $skipped++;
                continue;
            }

            $oldSize = Storage::disk('public')->size($storagePath);
            $result = ImageOptimizer::optimizeExisting($storagePath, 'products');

            if ($result) {
                $newStoragePath = str_replace('/storage/', '', $result['main']);
                $newSize = Storage::disk('public')->exists($newStoragePath)
                    ? Storage::disk('public')->size($newStoragePath)
                    : $oldSize;
                $saved = $oldSize - $newSize;
                $totalSaved += max(0, $saved);

                $product->update(['image_url' => $result['main']]);
                $optimized++;
                $this->line("  ✓ {$product->name}: " . self::formatBytes($oldSize) . ' → ' . self::formatBytes($newSize));
            } else {
                $skipped++;
            }
        }

        // --- Kategori görselleri ---
        $categories = Category::whereNotNull('image_path')
            ->where('image_path', 'not like', 'http%')
            ->get();

        $this->info("Kategori görselleri: {$categories->count()} adet");

        foreach ($categories as $category) {
            if (!Storage::disk('public')->exists($category->image_path)) {
                $skipped++;
                continue;
            }

            $oldSize = Storage::disk('public')->size($category->image_path);
            $result = ImageOptimizer::optimizeExisting($category->image_path, 'category_images');

            if ($result) {
                $newPath = str_replace('/storage/', '', $result['main']);
                $newSize = Storage::disk('public')->exists($newPath)
                    ? Storage::disk('public')->size($newPath)
                    : $oldSize;
                $saved = $oldSize - $newSize;
                $totalSaved += max(0, $saved);

                $category->update(['image_path' => $newPath]);
                $optimized++;
                $this->line("  ✓ {$category->name}: " . self::formatBytes($oldSize) . ' → ' . self::formatBytes($newSize));
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Tamamlandı! {$optimized} görsel optimize edildi, {$skipped} atlandı.");
        $this->info("Toplam tasarruf: " . self::formatBytes($totalSaved));

        return Command::SUCCESS;
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1) . 'KB';
        return $bytes . 'B';
    }
}
