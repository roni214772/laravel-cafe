<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        $uid  = $user?->id;

        $products = [
            // ── Kahve ───────────────────────────────────────
            ['name'=>'Espresso',         'description'=>'Güçlü ve yoğun tek shot espresso',            'price'=>35,  'category'=>'Kahve',        'sku'=>'ESP001','image_url'=>'https://images.unsplash.com/photo-1510591509098-f4fdc6d0ff04?w=300&fit=crop&q=80'],
            ['name'=>'Doppio',           'description'=>'Çift shot espresso',                             'price'=>45,  'category'=>'Kahve',        'sku'=>'DOP001','image_url'=>'https://images.unsplash.com/photo-1579888944880-d98341245702?w=300&fit=crop&q=80'],
            ['name'=>'Americano',        'description'=>'Espresso + sıcak su',                         'price'=>40,  'category'=>'Kahve',        'sku'=>'AME001','image_url'=>'https://images.unsplash.com/photo-1504627298434-2f63da63fc13?w=300&fit=crop&q=80'],
            ['name'=>'Cappuccino',       'description'=>'Espresso, süt ve süt köpüğü',             'price'=>55,  'category'=>'Kahve',        'sku'=>'CAP001','image_url'=>'https://images.unsplash.com/photo-1534778101976-62847782c213?w=300&fit=crop&q=80'],
            ['name'=>'Latte',            'description'=>'Espresso ve bol sütlü',                      'price'=>60,  'category'=>'Kahve',        'sku'=>'LAT001','image_url'=>'https://images.unsplash.com/photo-1561882468-9110e03e0f78?w=300&fit=crop&q=80'],
            ['name'=>'Flat White',       'description'=>'Yoğun sütlü ristretto bazlı kahve',       'price'=>65,  'category'=>'Kahve',        'sku'=>'FLW001','image_url'=>'https://images.unsplash.com/photo-1517256064527-09c73fc73e38?w=300&fit=crop&q=80'],
            ['name'=>'Mocha',            'description'=>'Espresso, çikolata ve sütlü',               'price'=>70,  'category'=>'Kahve',        'sku'=>'MOC001','image_url'=>'https://images.unsplash.com/photo-1578314675249-a6910f80cc4e?w=300&fit=crop&q=80'],
            ['name'=>'Macchiato',        'description'=>'Espresso üstüne az süt köpüğü',          'price'=>50,  'category'=>'Kahve',        'sku'=>'MAC001','image_url'=>'https://images.unsplash.com/photo-1485808191679-5f86510440a2?w=300&fit=crop&q=80'],
            ['name'=>'Türk Kahvesi',     'description'=>'Geleneksel pişirmeyyöntemiyle',            'price'=>45,  'category'=>'Kahve',        'sku'=>'TRK001','image_url'=>'https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=300&fit=crop&q=80'],
            ['name'=>'Cortado',          'description'=>'Eşit oranda espresso ve süt',              'price'=>55,  'category'=>'Kahve',        'sku'=>'COR001','image_url'=>'https://images.unsplash.com/photo-1496318447583-f524534e9ce1?w=300&fit=crop&q=80'],
            // ── Soğuk Kahve ────────────────────────────────
            ['name'=>'Iced Americano',   'description'=>'Buzlu americano',                           'price'=>55,  'category'=>'Soğuk Kahve',   'sku'=>'ICA001','image_url'=>'https://images.unsplash.com/photo-1461023058943-07fcbe16d735?w=300&fit=crop&q=80'],
            ['name'=>'Iced Latte',       'description'=>'Buzlu sütlü espresso',                    'price'=>65,  'category'=>'Soğuk Kahve',   'sku'=>'ICL001','image_url'=>'https://images.unsplash.com/photo-1621417840022-d6d754d36960?w=300&fit=crop&q=80'],
            ['name'=>'Frappe',           'description'=>'Buzlu blendırlanmış kahve',              'price'=>75,  'category'=>'Soğuk Kahve',   'sku'=>'FRP001','image_url'=>'https://images.unsplash.com/photo-1585494156145-1c60a4fe952b?w=300&fit=crop&q=80'],
            ['name'=>'Cold Brew',        'description'=>'12 saat soğuk demlenmiş kahve',          'price'=>80,  'category'=>'Soğuk Kahve',   'sku'=>'CLB001','image_url'=>'https://images.unsplash.com/photo-1509042239860-f550ce710b93?w=300&fit=crop&q=80'],
            ['name'=>'Dalgona',          'description'=>'Çirpilmiş kahve köpüğü + soğuk süt',     'price'=>85,  'category'=>'Soğuk Kahve',   'sku'=>'DLG001','image_url'=>'https://images.unsplash.com/photo-1592663527359-cf6642f54cff?w=300&fit=crop&q=80'],
            // ── Sıcak İçecek ────────────────────────────────
            ['name'=>'Çay',              'description'=>'Demlik çay – 2 bardak',                    'price'=>30,  'category'=>'Sıcak İçecek', 'sku'=>'CAY001','image_url'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&fit=crop&q=80'],
            ['name'=>'Yeşil Çay',       'description'=>'Organik yeşil çay',                     'price'=>40,  'category'=>'Sıcak İçecek', 'sku'=>'YES001','image_url'=>'https://images.unsplash.com/photo-1627435601361-ec25f5b1d0e5?w=300&fit=crop&q=80'],
            ['name'=>'Bitki Çayı',      'description'=>'Mevsim bitki çayı karışımı',            'price'=>45,  'category'=>'Sıcak İçecek', 'sku'=>'BTK001','image_url'=>'https://images.unsplash.com/photo-1576092768241-dec231879fc3?w=300&fit=crop&q=80'],
            ['name'=>'Sıcak Çikolata',  'description'=>'Kremalı sıcak çikolata',                  'price'=>65,  'category'=>'Sıcak İçecek', 'sku'=>'SCC001','image_url'=>'https://images.unsplash.com/photo-1542990253-0d0f5be5f0ed?w=300&fit=crop&q=80'],
            ['name'=>'Salep',            'description'=>'Geleneksel Osmanlı salebi',               'price'=>50,  'category'=>'Sıcak İçecek', 'sku'=>'SLP001','image_url'=>'https://images.unsplash.com/photo-1612970154611-83f8d4a7e5e8?w=300&fit=crop&q=80'],
            // ── Soğuk İçecek ────────────────────────────────
            ['name'=>'Limonata',         'description'=>'Taze sıkılmış limon suyu',               'price'=>50,  'category'=>'Soğuk İçecek', 'sku'=>'LMN001','image_url'=>'https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=300&fit=crop&q=80'],
            ['name'=>'Ayran',            'description'=>'Ev yapımı soğuk ayran',                  'price'=>30,  'category'=>'Soğuk İçecek', 'sku'=>'AYR001','image_url'=>'https://images.unsplash.com/photo-1550583724-b2692b85b150?w=300&fit=crop&q=80'],
            ['name'=>'Ice Tea Şeftali',  'description'=>'Şeftali aromalı soğuk çay',             'price'=>40,  'category'=>'Soğuk İçecek', 'sku'=>'ITS001','image_url'=>'https://images.unsplash.com/photo-1556679343-c7306c1976bc?w=300&fit=crop&q=80'],
            ['name'=>'Meyve Suyu',       'description'=>'Taze sıkılmış portakal suyu',             'price'=>45,  'category'=>'Soğuk İçecek', 'sku'=>'MEY001','image_url'=>'https://images.unsplash.com/photo-1600271886742-f049cd451bba?w=300&fit=crop&q=80'],
            // ── Milkshake ───────────────────────────────────
            ['name'=>'Milkshake Cilek',  'description'=>'Taze çilek ve dondurmalı',               'price'=>80,  'category'=>'Milkshake',    'sku'=>'MSC001','image_url'=>'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=300&fit=crop&q=80'],
            ['name'=>'Milkshake Cikolata','description'=>'Çikolata sos ve dondurmalı',             'price'=>85,  'category'=>'Milkshake',    'sku'=>'MSK001','image_url'=>'https://images.unsplash.com/photo-1541658016709-82535e94bc69?w=300&fit=crop&q=80'],
            ['name'=>'Milkshake Muz',    'description'=>'Muz, süt ve vanilya dondurma',          'price'=>80,  'category'=>'Milkshake',    'sku'=>'MSM001','image_url'=>'https://images.unsplash.com/photo-1568901839119-631418a3910d?w=300&fit=crop&q=80'],
            ['name'=>'Milkshake Karamel','description'=>'Karamel sos ve kremalı dondurma',        'price'=>90,  'category'=>'Milkshake',    'sku'=>'MSR001','image_url'=>'https://images.unsplash.com/photo-1581636625402-29b2a704ef13?w=300&fit=crop&q=80'],
            // ── Smoothie ────────────────────────────────────
            ['name'=>'Tropik Smoothie',  'description'=>'Mango, ananas ve hindistan cevizli',     'price'=>75,  'category'=>'Smoothie',     'sku'=>'SMT001','image_url'=>'https://images.unsplash.com/photo-1553530666-ba11a7da3888?w=300&fit=crop&q=80'],
            ['name'=>'Berry Smoothie',   'description'=>'Karışık meyveli smoothie',              'price'=>75,  'category'=>'Smoothie',     'sku'=>'SMB001','image_url'=>'https://images.unsplash.com/photo-1502741126161-b048400d1222?w=300&fit=crop&q=80'],
            ['name'=>'Yeşil Smoothie',  'description'=>'Ǭspanak, elma ve zencefilli',                'price'=>80,  'category'=>'Smoothie',     'sku'=>'SMY001','image_url'=>'https://images.unsplash.com/photo-1610970881699-44a5587cabec?w=300&fit=crop&q=80'],
            // ── Kahvaltı ──────────────────────────────────
            ['name'=>'Serpme Kahvaltı',  'description'=>'Zengin serpme tabağı (2 kişilik)',     'price'=>280,'category'=>'Kahvaltı',     'sku'=>'KAH001','image_url'=>'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?w=300&fit=crop&q=80'],
            ['name'=>'Sahanda Yumurta',  'description'=>'Tereyaglı sahanda yumurta',              'price'=>80, 'category'=>'Kahvaltı',     'sku'=>'SAH001','image_url'=>'https://images.unsplash.com/photo-1525351484163-7529414344d8?w=300&fit=crop&q=80'],
            ['name'=>'Menemen',          'description'=>'Domates, biber ve yumurtalı menemen',  'price'=>95, 'category'=>'Kahvaltı',     'sku'=>'MEN001','image_url'=>'https://images.unsplash.com/photo-1574926054530-14e999dd70e9?w=300&fit=crop&q=80'],
            ['name'=>'Tost',             'description'=>'Kaşar peynirli kasap tost',             'price'=>70, 'category'=>'Kahvaltı',     'sku'=>'TOS001','image_url'=>'https://images.unsplash.com/photo-1528736235302-52922df5c122?w=300&fit=crop&q=80'],
            ['name'=>'Avokadolü Ekmek', 'description'=>'Ekstra yumurtalı avokadolü dilim ekmek','price'=>110,'category'=>'Kahvaltı',    'sku'=>'AVK001','image_url'=>'https://images.unsplash.com/photo-1541519227354-08fa5d50c820?w=300&fit=crop&q=80'],
            // ── Krep ───────────────────────────────────────
            ['name'=>'Çikolatalı Krep',  'description'=>'Nutella ve çilek soslu krep',            'price'=>95, 'category'=>'Krep',         'sku'=>'KRP001','image_url'=>'https://images.unsplash.com/photo-1519676867240-f03562e64548?w=300&fit=crop&q=80'],
            ['name'=>'Meyvel Krep',     'description'=>'Taze mevsim meyveli krep',             'price'=>90, 'category'=>'Krep',         'sku'=>'KRP002','image_url'=>'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=300&fit=crop&q=80'],
            ['name'=>'Karamel Krep',    'description'=>'Karamel sos ve cevizli krep',           'price'=>100,'category'=>'Krep',         'sku'=>'KRP003','image_url'=>'https://images.unsplash.com/photo-1559561853-08451507cbe7?w=300&fit=crop&q=80'],
            ['name'=>'Lor Peynirli Krep','description'=>'Lor peyniri, bal ve cevizli krep',     'price'=>95, 'category'=>'Krep',         'sku'=>'KRP004','image_url'=>'https://images.unsplash.com/photo-1519676867240-f03562e64548?w=300&fit=crop&q=80'],
            // ── Waffle ──────────────────────────────────────
            ['name'=>'Klasik Waffle',    'description'=>'Muz ve çikolata soslu waffle',          'price'=>110,'category'=>'Waffle',       'sku'=>'WAF001','image_url'=>'https://images.unsplash.com/photo-1562376552-0d160a2f238d?w=300&fit=crop&q=80'],
            ['name'=>'Lotus Waffle',     'description'=>'Lotus bisküvi ve karamel soslu',       'price'=>130,'category'=>'Waffle',       'sku'=>'WAF002','image_url'=>'https://images.unsplash.com/photo-1604382354936-07c5d9983bd3?w=300&fit=crop&q=80'],
            ['name'=>'Çilek Waffle',     'description'=>'Taze çilek ve çift kremali waffle',      'price'=>120,'category'=>'Waffle',       'sku'=>'WAF003','image_url'=>'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&fit=crop&q=80'],
            ['name'=>'Karamelli Waffle', 'description'=>'Karamel ve pekan cevizli waffle',       'price'=>125,'category'=>'Waffle',       'sku'=>'WAF004','image_url'=>'https://images.unsplash.com/photo-1562376552-0d160a2f238d?w=300&fit=crop&q=80'],
            // ── Tatlı ────────────────────────────────────────
            ['name'=>'Cheesecake',       'description'=>'New York usulü kremalı cheesecake',  'price'=>90, 'category'=>'Tatlı',        'sku'=>'CHE001','image_url'=>'https://images.unsplash.com/photo-1565958011703-44f9829ba187?w=300&fit=crop&q=80'],
            ['name'=>'Tiramisu',         'description'=>'İtalyan usulü tiramisu',               'price'=>95, 'category'=>'Tatlı',        'sku'=>'TIR001','image_url'=>'https://images.unsplash.com/photo-1571877227200-a0d98ea607e9?w=300&fit=crop&q=80'],
            ['name'=>'Brownie',          'description'=>'Sıcak çikolatalı brownie + dondurma', 'price'=>100,'category'=>'Tatlı',        'sku'=>'BRW001','image_url'=>'https://images.unsplash.com/photo-1590080876329-56375c9cf693?w=300&fit=crop&q=80'],
            ['name'=>'Künefe',           'description'=>'Antep fıstıklı sıcak künefe',         'price'=>120,'category'=>'Tatlı',        'sku'=>'KNF001','image_url'=>'https://images.unsplash.com/photo-1551024601-bec78aea704b?w=300&fit=crop&q=80'],
            ['name'=>'Sütlaç',          'description'=>'Fırında pişmiş kremalı sütlç',        'price'=>75, 'category'=>'Tatlı',        'sku'=>'SLC001','image_url'=>'https://images.unsplash.com/photo-1488477181946-6428a0291777?w=300&fit=crop&q=80'],
            ['name'=>'Crème Brûlée',     'description'=>'Karamelizeli Fransiz tatlısı',       'price'=>110,'category'=>'Tatlı',        'sku'=>'CRB001','image_url'=>'https://images.unsplash.com/photo-1470124182917-cc6e71b22ecc?w=300&fit=crop&q=80'],
            // ── Pastane ──────────────────────────────────────
            ['name'=>'Croissant',        'description'=>'Tereyaglı fransiz croissanı',          'price'=>55, 'category'=>'Pastane',      'sku'=>'CRO001','image_url'=>'https://images.unsplash.com/photo-1555507036-ab1f4038808a?w=300&fit=crop&q=80'],
            ['name'=>'Muffin Cikolata',  'description'=>'Çikolata parçalı yumuşak muffin',      'price'=>50, 'category'=>'Pastane',      'sku'=>'MUF001','image_url'=>'https://images.unsplash.com/photo-1587668178277-295251f900ce?w=300&fit=crop&q=80'],
            ['name'=>'Kek Dilimi',       'description'=>'Günlük taze kek – çeşit sorunuz',    'price'=>60, 'category'=>'Pastane',      'sku'=>'KEK001','image_url'=>'https://images.unsplash.com/photo-1464349095431-e9a21285b5f3?w=300&fit=crop&q=80'],
            ['name'=>'Poğaça',           'description'=>'Ev yapımı peynirli poğaça',              'price'=>35, 'category'=>'Pastane',      'sku'=>'POG001','image_url'=>'https://images.unsplash.com/photo-1608198093002-ad4e005484ec?w=300&fit=crop&q=80'],
            ['name'=>'Cevizli Rolls',    'description'=>'Tarcinlı cevizli rüle',                 'price'=>70, 'category'=>'Pastane',      'sku'=>'CVZ001','image_url'=>'https://images.unsplash.com/photo-1509365465985-25d11c17e812?w=300&fit=crop&q=80'],
            ['name'=>'Macaron',          'description'=>'Farklı tat seçenekleriyle Fransiz macaron','price'=>45,'category'=>'Pastane',     'sku'=>'MCR001','image_url'=>'https://images.unsplash.com/photo-1558326567-98ae2405596b?w=300&fit=crop&q=80'],
            // ── Ana Yemek ───────────────────────────────────
            ['name'=>'Club Sandviç',     'description'=>'Tavuk, domates, marul ve bacon',      'price'=>160,'category'=>'Ana Yemek',    'sku'=>'CLB001','image_url'=>'https://images.unsplash.com/photo-1528736235302-52922df5c122?w=300&fit=crop&q=80'],
            ['name'=>'Izgara Köfte',     'description'=>'El yapımı ızgara köfte + pilav',       'price'=>195,'category'=>'Ana Yemek',    'sku'=>'KFT001','image_url'=>'https://images.unsplash.com/photo-1529042410759-befb1204b468?w=300&fit=crop&q=80'],
            ['name'=>'Tavuk Şiş',        'description'=>'Marine edilmiş ızgara tavuk şiş',    'price'=>185,'category'=>'Ana Yemek',    'sku'=>'TVK001','image_url'=>'https://images.unsplash.com/photo-1561043433-aaf687c4cf04?w=300&fit=crop&q=80'],
            ['name'=>'Makarna',          'description'=>'Domates soslu penne makarna',          'price'=>150,'category'=>'Ana Yemek',    'sku'=>'MAK001','image_url'=>'https://images.unsplash.com/photo-1598866594230-a7c12756260f?w=300&fit=crop&q=80'],
            // ── Burger ───────────────────────────────────────
            ['name'=>'Classic Burger',   'description'=>'180g kıymalı, domates, marul, tursu',   'price'=>160,'category'=>'Burger',      'sku'=>'BGR001','image_url'=>'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=300&fit=crop&q=80'],
            ['name'=>'Cheese Burger',    'description'=>'Cheddar peynirli çift katlı burger',   'price'=>180,'category'=>'Burger',      'sku'=>'BGR002','image_url'=>'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=300&fit=crop&q=80'],
            ['name'=>'Tavuk Burger',     'description'=>'Crispy tavuk fileto burger',           'price'=>155,'category'=>'Burger',      'sku'=>'BGR003','image_url'=>'https://images.unsplash.com/photo-1606755962773-d324e9a13086?w=300&fit=crop&q=80'],
            ['name'=>'Mantar Burger',    'description'=>'Karimizelmiş sogan ve mantar burger',   'price'=>170,'category'=>'Burger',      'sku'=>'BGR004','image_url'=>'https://images.unsplash.com/photo-1572802419224-296b0aeee0d9?w=300&fit=crop&q=80'],
            // ── Pizza ────────────────────────────────────────
            ['name'=>'Margarita',        'description'=>'Domates sos, mozarella, taze fısıl',  'price'=>170,'category'=>'Pizza',       'sku'=>'PZZ001','image_url'=>'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=300&fit=crop&q=80'],
            ['name'=>'Pepperoni',        'description'=>'Pepperoni ve mozzarella',               'price'=>190,'category'=>'Pizza',       'sku'=>'PZZ002','image_url'=>'https://images.unsplash.com/photo-1594007654729-407eedc4be65?w=300&fit=crop&q=80'],
            ['name'=>'Karnabahar Pizza', 'description'=>'Vegana gluten free karnabahar bazlı',  'price'=>185,'category'=>'Pizza',       'sku'=>'PZZ003','image_url'=>'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=300&fit=crop&q=80'],
            ['name'=>'BBQ Tavu Pizza',   'description'=>'BBQ tavuk ve karamelize sogan',        'price'=>195,'category'=>'Pizza',       'sku'=>'PZZ004','image_url'=>'https://images.unsplash.com/photo-1506354666786-959d6d497f1a?w=300&fit=crop&q=80'],
            // ── Salata ───────────────────────────────────────
            ['name'=>'Sezar Salata',     'description'=>'Romaine marul, parmesan, kruton',      'price'=>140,'category'=>'Salata',      'sku'=>'SEZ001','image_url'=>'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=300&fit=crop&q=80'],
            ['name'=>'Yeşil Salata',    'description'=>'Mevsim yeşillikleri ve nar eksi sos',  'price'=>110,'category'=>'Salata',      'sku'=>'YSL001','image_url'=>'https://images.unsplash.com/photo-1540420773420-3366772f4999?w=300&fit=crop&q=80'],
            ['name'=>'Ton Balıklı',   'description'=>'Ton balığı, kapari ve zeytinli salata',  'price'=>130,'category'=>'Salata',      'sku'=>'TON001','image_url'=>'https://images.unsplash.com/photo-1431184628564-8c3ff61ce84a?w=300&fit=crop&q=80'],
            // ── Çorba ────────────────────────────────────────
            ['name'=>'Mercimek Çorbası', 'description'=>'Geleneksel kırmızı mercimek çorbası',  'price'=>75, 'category'=>'Çorba',        'sku'=>'MRC001','image_url'=>'https://images.unsplash.com/photo-1547592166-23ac45744acd?w=300&fit=crop&q=80'],
            ['name'=>'Domates Çorbası',  'description'=>'Taze domatesli kremali çorba',          'price'=>80, 'category'=>'Çorba',        'sku'=>'DMT001','image_url'=>'https://images.unsplash.com/photo-1509136561942-977de35b1d33?w=300&fit=crop&q=80'],
            ['name'=>'Mantar Çorbası',   'description'=>'Kremali mantar çorbası',                 'price'=>85, 'category'=>'Çorba',        'sku'=>'MNT001','image_url'=>'https://images.unsplash.com/photo-1476718406336-bb5a9690ee2a?w=300&fit=crop&q=80'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['sku' => $p['sku']],
                array_merge($p, ['user_id' => $uid, 'quantity' => 100])
            );
        }
    }
}
