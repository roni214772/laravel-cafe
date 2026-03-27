<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::where('user_id', auth()->user()->effectiveOwnerId())->orderBy('category')->orderBy('name')->get()
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

    /**
     * URL'den menü ürünlerini çek (scrape) — önizleme
     */
    public function scrapeMenu(Request $request)
    {
        $request->validate(['url' => 'required|url|max:500']);
        $url = $request->url;
        $baseUrl = rtrim(parse_url($url, PHP_URL_SCHEME) . '://' . parse_url($url, PHP_URL_HOST), '/');

        try {
            $response = Http::timeout(15)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept'     => 'text/html,application/xhtml+xml,application/json',
            ])->get($url);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'error' => 'Sayfa yüklenemedi (HTTP ' . $response->status() . ')'], 422);
            }

            $html = $response->body();
            $items = [];

            // 1) JSON-LD yapısal veri dene (en güvenilir)
            if (preg_match_all('/<script[^>]*type=["\']application\/ld\+json["\'][^>]*>(.*?)<\/script>/si', $html, $jsonMatches)) {
                foreach ($jsonMatches[1] as $json) {
                    $data = json_decode(trim($json), true);
                    if (!$data) continue;
                    $menus = [];
                    if (isset($data['@type']) && $data['@type'] === 'Menu') $menus[] = $data;
                    if (isset($data['hasMenu'])) $menus[] = $data['hasMenu'];
                    if (isset($data['@graph'])) {
                        foreach ($data['@graph'] as $g) {
                            if (isset($g['@type']) && $g['@type'] === 'Menu') $menus[] = $g;
                        }
                    }
                    foreach ($menus as $menu) {
                        if (isset($menu['hasMenuSection'])) {
                            foreach ($menu['hasMenuSection'] as $sec) {
                                $cat = $sec['name'] ?? '';
                                if (isset($sec['hasMenuItem'])) {
                                    foreach ($sec['hasMenuItem'] as $mi) {
                                        $price = 0;
                                        if (isset($mi['offers']['price'])) $price = (float)$mi['offers']['price'];
                                        elseif (isset($mi['offers'][0]['price'])) $price = (float)$mi['offers'][0]['price'];
                                        $image = $mi['image'] ?? ($mi['image'][0] ?? null);
                                        if (is_array($image)) $image = $image['url'] ?? ($image[0] ?? null);
                                        $items[] = ['name' => $mi['name'] ?? '', 'price' => $price, 'category' => $cat, 'image' => $image];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // 2) JSON-LD bulamadıysa HTML'den çıkar
            if (empty($items)) {
                $items = $this->parseHtmlMenu($html, $baseUrl);
            }

            // Relative URL'leri absolute yap
            foreach ($items as &$item) {
                if (!empty($item['image']) && !str_starts_with($item['image'], 'http')) {
                    $item['image'] = $baseUrl . '/' . ltrim($item['image'], '/');
                }
            }
            unset($item);

            // Boş isimleri filtrele ve tekrarları kaldır
            $items = collect($items)
                ->filter(fn($i) => !empty(trim($i['name'] ?? '')))
                ->unique(fn($i) => $i['name'] . '|' . $i['price'])
                ->values()
                ->toArray();

            if (empty($items)) {
                return response()->json(['success' => false, 'error' => 'Bu sayfadan ürün bulunamadı. Farklı bir menü URL\'si deneyin.'], 422);
            }

            return response()->json(['success' => true, 'items' => $items, 'count' => count($items)]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Sayfa yüklenemedi: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Çekilen ürünleri veritabanına kaydet
     */
    public function importMenu(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:100',
            'items.*.price'  => 'required|numeric|min:0',
            'items.*.category' => 'nullable|string|max:60',
            'items.*.image'    => 'nullable|url|max:500',
        ]);

        $userId = auth()->id();
        $imported = 0;
        $skipped  = 0;

        foreach ($request->items as $item) {
            $name     = trim($item['name']);
            $category = trim($item['category'] ?? '');
            $price    = (float)$item['price'];

            // Aynı isimli ürün varsa atla
            $exists = Product::where('user_id', $userId)
                ->where('name', $name)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            Product::create([
                'user_id'   => $userId,
                'name'      => $name,
                'price'     => $price,
                'category'  => $category,
                'image_url' => !empty($item['image']) ? $item['image'] : null,
            ]);

            // Kategori yoksa oluştur
            if ($category && !Category::where('user_id', $userId)->where('name', $category)->exists()) {
                Category::create(['user_id' => $userId, 'name' => $category]);
            }

            $imported++;
        }

        return response()->json([
            'success'  => true,
            'imported' => $imported,
            'skipped'  => $skipped,
            'message'  => "$imported ürün eklendi" . ($skipped > 0 ? ", $skipped ürün zaten mevcuttu" : ''),
        ]);
    }

    /**
     * HTML'den menü ürünlerini çıkar (genel pattern'ler)
     */
    private function parseHtmlMenu(string $html, string $baseUrl = ''): array
    {
        $items = [];
        $doc = new \DOMDocument();
        @$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html);
        $xpath = new \DOMXPath($doc);

        // Yaygın menü CSS sınıfları
        $patterns = [
            // isim ve fiyat ayrı element'lerde
            ['name' => './/div[contains(@class,"product-name") or contains(@class,"item-name") or contains(@class,"menu-item-name") or contains(@class,"dish-name") or contains(@class,"food-name") or contains(@class,"product-title") or contains(@class,"item-title")]',
             'price' => './/div[contains(@class,"product-price") or contains(@class,"item-price") or contains(@class,"menu-item-price") or contains(@class,"dish-price") or contains(@class,"food-price") or contains(@class,"price")]//text()'],
            // span varyantları
            ['name' => './/span[contains(@class,"product-name") or contains(@class,"item-name") or contains(@class,"menu-item-name") or contains(@class,"dish-name")]',
             'price' => './/span[contains(@class,"product-price") or contains(@class,"item-price") or contains(@class,"price")]//text()'],
            // h3/h4 başlıkları
            ['name' => './/h3[contains(@class,"product") or contains(@class,"item") or contains(@class,"menu")]',
             'price' => './/span[contains(@class,"price")]//text()'],
        ];

        // Kart/liste container'larını bul
        $containers = $xpath->query('//div[contains(@class,"product-card") or contains(@class,"menu-item") or contains(@class,"menu-card") or contains(@class,"food-item") or contains(@class,"dish-card") or contains(@class,"product-item") or contains(@class,"product-list-item")]');

        if ($containers && $containers->length > 0) {
            foreach ($containers as $card) {
                $name = '';
                $price = 0;

                // İsim bul
                foreach (['h2','h3','h4','h5','.product-name','span.name','.item-name','.menu-item-name'] as $sel) {
                    $nameNodes = $xpath->query('.//' . (str_contains($sel, '.') ? "div[contains(@class,'" . ltrim($sel, '.') . "')]" : $sel), $card);
                    if ($nameNodes && $nameNodes->length > 0) {
                        $name = trim($nameNodes->item(0)->textContent);
                        break;
                    }
                }

                // Fiyat bul (sayı + TL/₺ pattern)
                $text = $card->textContent;
                if (preg_match('/(\d+[\.,]?\d*)\s*(?:₺|TL|tl)/u', $text, $m)) {
                    $price = (float)str_replace(',', '.', $m[1]);
                }

                // Kategori — parent'tan section/header bul
                $category = '';
                $parent = $card->parentNode;
                while ($parent && $parent->nodeType === XML_ELEMENT_NODE) {
                    $prevSibling = $parent->previousSibling;
                    while ($prevSibling) {
                        if ($prevSibling->nodeType === XML_ELEMENT_NODE &&
                            in_array(strtolower($prevSibling->nodeName), ['h2','h3','h4'])) {
                            $category = trim($prevSibling->textContent);
                            break 2;
                        }
                        $prevSibling = $prevSibling->previousSibling;
                    }
                    $parent = $parent->parentNode;
                }

                // Görsel bul
                $image = '';
                $imgNodes = $xpath->query('.//img', $card);
                if ($imgNodes && $imgNodes->length > 0) {
                    $img = $imgNodes->item(0);
                    $image = $img->getAttribute('src') ?: $img->getAttribute('data-src') ?: $img->getAttribute('data-lazy-src') ?: '';
                }

                if ($name) {
                    $items[] = ['name' => $name, 'price' => $price, 'category' => $category, 'image' => $image];
                }
            }
        }

        // Son çare: fiyat pattern'iyle satır satır tara
        if (empty($items)) {
            // "Ürün Adı ... 25,00 ₺" veya "Ürün Adı 25 TL" pattern'i
            if (preg_match_all('/([A-ZÇĞİÖŞÜa-zçğıöşü][A-ZÇĞİÖŞÜa-zçğıöşü\s\-\(\)\/&]+?)\s*[:\-–]?\s*(\d+[\.,]?\d*)\s*(?:₺|TL|tl)/u', strip_tags($html), $matches, PREG_SET_ORDER)) {
                foreach ($matches as $m) {
                    $name  = trim($m[1]);
                    $price = (float)str_replace(',', '.', $m[2]);
                    if (mb_strlen($name) >= 2 && mb_strlen($name) <= 80 && $price > 0) {
                        $items[] = ['name' => $name, 'price' => $price, 'category' => '', 'image' => ''];
                    }
                }
            }
        }

        return $items;
    }
}
