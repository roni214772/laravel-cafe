<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    // POST /categories/image  { name, image }
    public function uploadImage(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:60',
            'image' => 'required|image|max:4096',
        ]);
        $user = auth()->user();
        $category = Category::firstOrCreate(
            ['user_id' => $user->id, 'name' => $request->name]
        );
        if ($category->image_path) {
            Storage::disk('public')->delete($category->image_path);
        }
        $path = $request->file('image')->store('category_images', 'public');
        $category->update(['image_path' => $path]);
        return response()->json(['success' => true, 'url' => Storage::url($path)]);
    }

    // DELETE /categories/image  { name }
    public function deleteImage(Request $request)
    {
        $request->validate(['name' => 'required|string|max:60']);
        $user = auth()->user();
        $cat = Category::where('user_id', $user->id)->where('name', $request->name)->first();
        if ($cat && $cat->image_path) {
            Storage::disk('public')->delete($cat->image_path);
            $cat->update(['image_path' => null]);
        }
        return response()->json(['success' => true]);
    }

    // GET /categories/images
    public function listImages()
    {
        $user = auth()->user();
        $cats = Category::where('user_id', $user->id)->get()
            ->mapWithKeys(fn($c) => [$c->name => $c->image_url]);
        return response()->json($cats);
    }
}
