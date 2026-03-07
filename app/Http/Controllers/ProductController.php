<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::where('user_id', auth()->id())->orderBy('category')->orderBy('name')->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'price'     => 'required|numeric|min:0',
            'category'  => 'nullable|string|max:60',
            'image'     => 'nullable|image|max:4096',
            'image_url' => 'nullable|string|max:500',
        ]);

        $imageUrl = $this->handleImage($request, null);

        $product = Product::create([
            'user_id'   => auth()->id(),
            'name'      => $request->name,
            'price'     => $request->price,
            'category'  => $request->category ?? '',
            'image_url' => $imageUrl,
        ]);
        return response()->json(['success' => true, 'product' => $product]);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id !== auth()->id()) abort(403);
        $request->validate([
            'name'      => 'required|string|max:100',
            'price'     => 'required|numeric|min:0',
            'category'  => 'nullable|string|max:60',
            'image'     => 'nullable|image|max:4096',
            'image_url' => 'nullable|string|max:500',
        ]);

        $imageUrl = $this->handleImage($request, $product->image_url);

        $product->update([
            'name'      => $request->name,
            'price'     => $request->price,
            'category'  => $request->category ?? '',
            'image_url' => $imageUrl,
        ]);
        return response()->json(['success' => true, 'product' => $product]);
    }

    public function destroy(Product $product)
    {
        if ($product->user_id !== auth()->id()) abort(403);
        try {
            if ($product->image_url && str_starts_with($product->image_url, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $product->image_url));
            }
            $product->delete();
        } catch (QueryException $e) {
            // Ürün siparişlerde kullanılıyor — silinemez
            if ($e->getCode() === '23000') {
                return response()->json([
                    'success' => false,
                    'error'   => 'Bu ürün geçmiş siparişlerde kullanıldığı için silinemiyor.',
                ], 422);
            }
            throw $e;
        }
        return response()->json(['success' => true]);
    }

    private function handleImage(Request $request, ?string $existing): ?string
    {
        // User explicitly cleared the image
        if ($request->input('image_clear') === '1') {
            if ($existing && str_starts_with($existing, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $existing));
            }
            return null;
        }
        // File upload takes priority — use 'public' disk so files land in storage/app/public/
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($existing && str_starts_with($existing, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $existing));
            }
            $path = $request->file('image')->store('products', 'public');
            return '/storage/' . $path;
        }
        // Explicit URL provided
        if ($request->filled('image_url')) {
            return $request->image_url;
        }
        // Keep existing
        return $existing;
    }
}
