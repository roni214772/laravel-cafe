<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ImageOptimizer
{
    /**
     * Görseli optimize edip kaydet: sıkıştırma + resize + WebP dönüşümü + thumbnail
     *
     * @return array{main: string, thumb: string|null}  /storage/ ile başlayan URL'ler
     */
    public static function optimizeAndStore(UploadedFile $file, string $folder, bool $generateThumb = true): array
    {
        $maxWidth   = 800;
        $maxHeight  = 800;
        $thumbSize  = 150;
        $quality    = 80;

        // Orijinal görseli GD'ye yükle
        $source = self::createImageFromFile($file->getPathname(), $file->getMimeType());
        if (!$source) {
            // GD desteklemiyorsa orijinal olarak kaydet
            $path = $file->store($folder, 'public');
            return ['main' => '/storage/' . $path, 'thumb' => null];
        }

        $origWidth  = imagesx($source);
        $origHeight = imagesy($source);

        // --- Ana görsel: resize + WebP ---
        $resized = self::resizeImage($source, $origWidth, $origHeight, $maxWidth, $maxHeight);
        $mainFilename = uniqid() . '_' . time() . '.webp';
        $mainPath = $folder . '/' . $mainFilename;
        $mainFullPath = Storage::disk('public')->path($mainPath);

        // Klasörü oluştur
        $dir = dirname($mainFullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagewebp($resized, $mainFullPath, $quality);

        // --- Thumbnail: küçük kare ---
        $thumbPath = null;
        if ($generateThumb) {
            $thumb = self::createThumbnail($source, $origWidth, $origHeight, $thumbSize);
            $thumbFilename = 'thumb_' . $mainFilename;
            $thumbStorePath = $folder . '/' . $thumbFilename;
            $thumbFullPath = Storage::disk('public')->path($thumbStorePath);
            imagewebp($thumb, $thumbFullPath, 70);
            imagedestroy($thumb);
            $thumbPath = '/storage/' . $thumbStorePath;
        }

        // Belleği temizle
        imagedestroy($source);
        if ($resized !== $source) {
            imagedestroy($resized);
        }

        return [
            'main'  => '/storage/' . $mainPath,
            'thumb' => $thumbPath,
        ];
    }

    /**
     * Mevcut bir görseli optimize et (dosya yolundan)
     */
    public static function optimizeExisting(string $storagePath, string $folder): ?array
    {
        $fullPath = Storage::disk('public')->path($storagePath);
        if (!file_exists($fullPath)) {
            return null;
        }

        $mime = mime_content_type($fullPath);
        $source = self::createImageFromFile($fullPath, $mime);
        if (!$source) {
            return null;
        }

        $origWidth  = imagesx($source);
        $origHeight = imagesy($source);

        // Zaten WebP ve küçükse atla
        if ($mime === 'image/webp' && $origWidth <= 800 && $origHeight <= 800) {
            imagedestroy($source);
            return null;
        }

        $resized = self::resizeImage($source, $origWidth, $origHeight, 800, 800);
        $mainFilename = uniqid() . '_' . time() . '.webp';
        $mainPath = $folder . '/' . $mainFilename;
        $mainFullPath = Storage::disk('public')->path($mainPath);

        $dir = dirname($mainFullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        imagewebp($resized, $mainFullPath, 80);

        // Thumbnail
        $thumb = self::createThumbnail($source, $origWidth, $origHeight, 150);
        $thumbFilename = 'thumb_' . $mainFilename;
        $thumbStorePath = $folder . '/' . $thumbFilename;
        imagewebp($thumb, Storage::disk('public')->path($thumbStorePath), 70);

        imagedestroy($source);
        if ($resized !== $source) {
            imagedestroy($resized);
        }
        imagedestroy($thumb);

        // Eski dosyayı sil
        Storage::disk('public')->delete($storagePath);

        return [
            'main'  => '/storage/' . $mainPath,
            'thumb' => '/storage/' . $thumbStorePath,
        ];
    }

    private static function createImageFromFile(string $path, ?string $mime): ?\GdImage
    {
        return match ($mime) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($path),
            'image/png'               => @imagecreatefrompng($path),
            'image/gif'               => @imagecreatefromgif($path),
            'image/webp'              => @imagecreatefromwebp($path),
            default                   => null,
        } ?: null;
    }

    private static function resizeImage(\GdImage $source, int $origW, int $origH, int $maxW, int $maxH): \GdImage
    {
        // Zaten küçükse dokunma
        if ($origW <= $maxW && $origH <= $maxH) {
            return $source;
        }

        $ratio = min($maxW / $origW, $maxH / $origH);
        $newW  = (int) round($origW * $ratio);
        $newH  = (int) round($origH * $ratio);

        $resized = imagecreatetruecolor($newW, $newH);
        // PNG/WebP transparanlığını koru
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        $transparent = imagecolorallocatealpha($resized, 0, 0, 0, 127);
        imagefill($resized, 0, 0, $transparent);

        imagecopyresampled($resized, $source, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        return $resized;
    }

    private static function createThumbnail(\GdImage $source, int $origW, int $origH, int $size): \GdImage
    {
        // Kare kırpma: ortadan kes
        $cropSize = min($origW, $origH);
        $cropX    = (int) round(($origW - $cropSize) / 2);
        $cropY    = (int) round(($origH - $cropSize) / 2);

        $thumb = imagecreatetruecolor($size, $size);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);
        $transparent = imagecolorallocatealpha($thumb, 0, 0, 0, 127);
        imagefill($thumb, 0, 0, $transparent);

        imagecopyresampled($thumb, $source, 0, 0, $cropX, $cropY, $size, $size, $cropSize, $cropSize);
        return $thumb;
    }
}
