<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(string $token)
    {
        $user = User::where('menu_token', $token)->firstOrFail();

        $products = Product::where('user_id', $user->id)
            ->orderBy('category')->orderBy('name')
            ->get()->groupBy('category');

        // { "Kahve" => "https://..." veya "/storage/..." }
        $catImages = Category::where('user_id', $user->id)
            ->whereNotNull('image_path')
            ->get()
            ->pluck('image_url', 'name');

        return response()
            ->view('menu.index', compact('products', 'catImages', 'user'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
