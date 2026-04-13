<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Services\ImageOptimizer;
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
        // File upload takes priority — optimize + WebP + thumbnail
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($existing && str_starts_with($existing, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $existing);
                Storage::disk('public')->delete($oldPath);
                // Eski thumbnail'ı da sil
                $oldDir  = dirname($oldPath);
                $oldName = basename($oldPath);
                Storage::disk('public')->delete($oldDir . '/thumb_' . $oldName);
            }
            $result = ImageOptimizer::optimizeAndStore($request->file('image'), 'products');
            return $result['main'];
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
            // 0. Bilinen QR menü platformlarını API'den çek (en güvenilir)
            $knownItems = $this->tryKnownPlatformApi($url);
            if (!empty($knownItems)) {
                return response()->json(['success' => true, 'items' => $knownItems, 'count' => count($knownItems)]);
            }

            // 1. Doğrudan HTML çek
            $response = Http::withoutVerifying()->timeout(15)->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                'Accept'     => 'text/html,application/xhtml+xml,application/json',
            ])->get($url);

            if (!$response->successful()) {
                return response()->json(['success' => false, 'error' => 'Sayfa yüklenemedi (HTTP ' . $response->status() . ')'], 422);
            }

            $html = $response->body();
            $items = [];

            // 2. JSON-LD yapısal veri dene (en güvenilir)
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

            // 3. JSON-LD bulamadıysa HTML'den çıkar
            if (empty($items)) {
                $items = $this->parseHtmlMenu($html, $baseUrl);
            }

            // 4. HTML'den de bulamadıysa SPA olabilir — Jina Reader ile render et
            if (empty($items)) {
                $items = $this->scrapeViaSpaRenderer($url, $baseUrl);
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
     * Bilinen QR menü platformlarının API'lerinden veri çek
     */
    private function tryKnownPlatformApi(string $url): array
    {
        // KirazSoft / KirazMenu QR menü sistemi
        if (preg_match('/qr\.kirazsoft\.com|kirazmenu\.com/', $url)) {
            return $this->scrapeKirazsoft($url);
        }

        return [];
    }

    /**
     * KirazSoft QR Menü API'sinden ürünleri çek
     */
    private function scrapeKirazsoft(string $url): array
    {
        try {
            $apiBase = 'https://qrapi.kirazsoft.com/api/v1/';
            $appSecret = '296259c642294f9eb7c223198f10b4f0';
            $imageBase = 'https://qrapi.kirazsoft.com/';

            // URL'den parametreleri çıkar: c=companyKey, b=branchKey, ci=companyId, bi=branchId
            $params = [];
            // Hash fragment'ı da parse et
            $fragment = parse_url($url, PHP_URL_FRAGMENT);
            if ($fragment && str_contains($fragment, '?')) {
                parse_str(substr($fragment, strpos($fragment, '?') + 1), $params);
            }
            // Normal query params
            $query = parse_url($url, PHP_URL_QUERY);
            if ($query) {
                parse_str($query, $queryParams);
                $params = array_merge($params, $queryParams);
            }

            $companyKey = $params['c'] ?? null;
            $branchKey  = $params['b'] ?? null;

            if (!$companyKey) return [];

            // 1. Menü key'ini al — branch key ile tablo opsiyonlarını sorgula
            $menuKey = null;
            $branchId = $params['bi'] ?? null;

            if ($branchKey) {
                $r = Http::withoutVerifying()->timeout(10)->get($apiBase . "Branches/GetBranchTableOptions", [
                    'appSecret' => $appSecret,
                    'branchKey' => $branchKey,
                ]);
                if ($r->successful()) {
                    $data = $r->json();
                    $menuKey  = $data['BranchMenus'][0]['Menu']['MenuKey'] ?? null;
                    $branchId = $data['BranchMenus'][0]['BranchId'] ?? $branchId;
                }
            }

            // Branch key ile bulamadıysa CompanyBranches dene
            if (!$menuKey) {
                $r2 = Http::withoutVerifying()->timeout(10)->get($apiBase . "Branches/GetBranchOptionsByCompanyKeyAndMenuKey", [
                    'appSecret' => $appSecret,
                    'companyKey' => $companyKey,
                    'menuKey' => $branchKey ?? $companyKey,
                ]);
                if ($r2->successful()) {
                    $data2 = $r2->json();
                    $menuKey  = $data2['BranchMenus'][0]['Menu']['MenuKey'] ?? null;
                    $branchId = $data2['BranchMenus'][0]['BranchId'] ?? $branchId;
                }
            }

            if (!$menuKey || !$branchId) return [];

            // 2. Menü ürünlerini çek
            $r3 = Http::withoutVerifying()->timeout(15)->get($apiBase . "Menus/GetMenuByCompanyKeyAndMenuKeyV2", [
                'appSecret'      => $appSecret,
                'companyKey'     => $companyKey,
                'menuKey'        => $menuKey,
                'branchId'       => $branchId,
                'branchMenuType' => 2,
            ]);

            if (!$r3->successful()) return [];

            $menuData = $r3->json();
            $categories = $menuData['menu']['Categories'] ?? [];
            $items = [];

            foreach ($categories as $cat) {
                $catName = $cat['Name'] ?? '';
                $products = $cat['Products'] ?? [];

                foreach ($products as $product) {
                    if (!($product['IsActive'] ?? false)) continue;

                    $name  = $product['Name'] ?? '';
                    $price = 0;

                    // Fiyat
                    if (!empty($product['ProductPrices'])) {
                        $price = (float)($product['ProductPrices'][0]['Price'] ?? 0);
                    }
                    // Porsiyon fiyatları
                    if ($price == 0 && !empty($product['ProductPortions'])) {
                        foreach ($product['ProductPortions'] as $portion) {
                            if (!empty($portion['ProductPortionPrices'])) {
                                $price = (float)($portion['ProductPortionPrices'][0]['Price'] ?? 0);
                                break;
                            }
                        }
                    }

                    // Görsel
                    $image = '';
                    if (!empty($product['ProductImages'])) {
                        $imgPath = $product['ProductImages'][0]['ImageUrl'] ?? '';
                        if ($imgPath) {
                            $image = str_starts_with($imgPath, 'http') ? $imgPath : $imageBase . $imgPath;
                        }
                    }

                    if ($name) {
                        $items[] = [
                            'name'     => $name,
                            'price'    => $price,
                            'category' => $catName,
                            'image'    => $image,
                        ];
                    }
                }
            }

            return $items;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * SPA sayfalarını Jina Reader ile render edip ürün çıkar
     */
    private function scrapeViaSpaRenderer(string $url, string $baseUrl): array
    {
        try {
            // Jina Reader: JavaScript render eder ve markdown olarak döndürür (ücretsiz)
            $rendered = Http::withoutVerifying()->timeout(30)->withHeaders([
                'Accept' => 'text/plain',
            ])->get('https://r.jina.ai/' . $url);

            if (!$rendered->successful()) return [];

            $text = $rendered->body();
            $items = [];
            $currentCategory = '';

            // Markdown formatında "## KATEGORİ" başlıkları ve "₺430.00 ÜRÜN ADI" pattern'leri
            $lines = explode("\n", $text);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) continue;

                // Kategori başlığı: ## KATEGORİ ADI veya # KATEGORİ ADI
                if (preg_match('/^#{1,4}\s+(.+)$/', $line, $m)) {
                    $cat = trim($m[1]);
                    // Navigasyon/header öğelerini atla
                    if (mb_strlen($cat) <= 40 && !preg_match('/menu|ana\s*sayfa|footer|header|nav|cookie|gizlilik|iletişim/iu', $cat)) {
                        $currentCategory = $cat;
                    }
                    continue;
                }

                // Fiyat pattern'leri: "₺430.00 ÜRÜN ADI" veya "ÜRÜN ADI ₺430.00" veya "430,00 ₺"
                $name = '';
                $price = 0;

                // ₺XXX.XX ÜRÜN_ADI
                if (preg_match('/₺\s*(\d+[\.,]?\d*)\s+(.+)/u', $line, $m)) {
                    $price = (float)str_replace(',', '.', $m[1]);
                    $name = trim($m[2]);
                }
                // ÜRÜN_ADI ₺XXX.XX veya ÜRÜN_ADI XXX,XX ₺ veya ÜRÜN_ADI XXX TL
                elseif (preg_match('/^(.+?)\s+₺?\s*(\d+[\.,]?\d*)\s*(?:₺|TL)?$/u', $line, $m)) {
                    $name = trim($m[1]);
                    $price = (float)str_replace(',', '.', $m[2]);
                }
                // XXX.XX₺ veya XXX,XX₺ (yapışık)
                elseif (preg_match('/(\d+[\.,]\d+)₺\s+(.+)/u', $line, $m)) {
                    $price = (float)str_replace(',', '.', $m[1]);
                    $name = trim($m[2]);
                }

                // Görsel URL'si — aynı veya önceki/sonraki satırda olabilir
                $image = '';
                if (preg_match('/!\[.*?\]\((https?:\/\/[^\s\)]+)\)/', $line, $imgM)) {
                    $image = $imgM[1];
                }

                if ($name && $price > 0) {
                    // İsimden markdown/gereksiz karakterleri temizle
                    $name = preg_replace('/\[.*?\]\(.*?\)/', '', $name); // markdown linkleri
                    $name = preg_replace('/!\[.*?\]\(.*?\)/', '', $name); // markdown görselleri
                    $name = trim(preg_replace('/[*_`#\[\]]+/', '', $name)); // markdown formatları
                    $name = preg_replace('/\s{2,}/', ' ', $name);
                    $name = rtrim($name, ' .');

                    if (mb_strlen($name) >= 2 && mb_strlen($name) <= 80) {
                        $items[] = [
                            'name'     => $name,
                            'price'    => $price,
                            'category' => $currentCategory,
                            'image'    => $image,
                        ];
                    }
                }
            }

            // Görselleri ürünlerle eşleştirmeye çalış (markdown'da görsel ayrı satırda olabilir)
            if (!empty($items)) {
                $allImages = [];
                if (preg_match_all('/!\[.*?\]\((https?:\/\/[^\s\)]+(?:\.png|\.jpg|\.jpeg|\.webp)[^\s\)]*)\)/i', $text, $imgMatches)) {
                    $allImages = $imgMatches[1];
                }
                $imgIdx = 0;
                foreach ($items as &$item) {
                    if (empty($item['image']) && isset($allImages[$imgIdx])) {
                        $item['image'] = $allImages[$imgIdx];
                    }
                    $imgIdx++;
                }
                unset($item);
            }

            return $items;
        } catch (\Exception $e) {
            return [];
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
