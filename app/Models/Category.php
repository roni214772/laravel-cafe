<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $fillable = ['user_id', 'name', 'image_path'];

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_path) return null;
        // Har ici URL ise direkt döndür
        if (str_starts_with($this->image_path, 'http')) return $this->image_path;
        return Storage::url($this->image_path);
    }
}
