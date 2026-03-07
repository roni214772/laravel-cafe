<p align="center">
  <h1>🍽️ Laravel Café - POS Sistemi</h1>
  <p>Modern, responsive, SQLite tabanlı Kafé/Restoran Adisyon ve Yönetim Sistemi</p>
</p>
🍽️ Espresso
---

## 🎯 Proje Özellikleri

### ✨ Ana Özellikleri
- ✅ **Kimlik Doğrulama**: Kullanıcı kayıt ve giriş sistemi
- ✅ **POS/Adisyon Sistemi**: Masa bazlı sipariş yönetimi
- ✅ **Ürün Yönetimi**: Ürün ekleme, düzenleme, silme
- ✅ **Ödeme Takibi**: Nakit, kart, karma ödeme seçenekleri
- ✅ **Mutfak Display**: Siparişleri mutfakta göster
- ✅ **Otomatik Hesaplama**: KDV, hizmet ücreti, indirim
- ✅ **Responsive Design**: Mobil, tablet, masaüstü uyumlu
- ✅ **SQLite Desteği**: Veri tabanı kurulumu kolay, migration'lar hazır

---

## 🛠️ Teknoloji Yığını

| Katman | Teknoloji |
|--------|-----------|
| **Framework** | Laravel 12 |
| **PHP Versiyonu** | 8.2+ |
| **Frontend** | Bootstrap 5, Custom CSS |
| **Database** | SQLite |
| **Build Tool** | Vite, Tailwind CSS |
| **Test Framework** | PHPUnit 11.5 |

---

## 📦 Kurulum

### 1. Proje Klonla
```bash
git clone <repo-url>
cd laravel-cafe
```

### 2. Bağımlılıkları Yükle
```bash
composer install
npm install
```

### 3. Environment Dosyasını Hazırla
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Veritabanını Oluştur
```bash
php artisan migrate:fresh --seed
```

### 5. Sunucuyu Başlat
```bash
php artisan serve
```

Tarayıcıda açılacak: [http://localhost:8000](http://localhost:8000)

---

## 🚀 Hızlı Başlangıç

### Test Hesabı
- **Email**: `test@example.com`
- **Şifre**: `214772`

### Örnek Veriler
Database'de hazır ürünler ve test kullanıcısı bulunmaktadır:

- 🍰 Croissant
- 🍫 Cheesecake
- 🧋 Iced Tea
- Ve daha fazlası...

---

## 📋 Modüller

### 🔐 Kimlik Doğrulama
- User kayıt ve giriş
- Şifre validasyonu
- Session yönetimi

**Routes:**
- `GET /login` - Giriş sayfası
- `POST /login` - Giriş işlemi
- `GET /register` - Kayıt sayfası
- `POST /register` - Kayıt işlemi
- `POST /logout` - Çıkış

### 🏪 POS/Adisyon Sistemi
- Masa oluştur/düzenle/sil
- Siparişleri masa bazında yönet
- Ürünleri siparişe ekle/kaldır
- Ödeme alımını takip et

**Routes:**
- `GET /adısyon` - POS Dashboard
- `API /api/pos/*` - REST API endpoints

**API Endpoints:**
```
POST   /api/pos/rooms             - Masa oluştur
POST   /api/pos/rooms/open        - Masa aç
POST   /api/pos/rooms/close       - Masa kapat
PUT    /api/pos/rooms/{id}        - Masa düzenle
DELETE /api/pos/rooms/{id}        - Masa sil
POST   /api/pos/add-to-order      - Siparişe ürün ekle
PUT    /api/pos/items/{id}        - Satır miktarı güncelle
DELETE /api/pos/items/{id}        - Satırı sil
POST   /api/pos/orders/{id}/payment - Ödeme ekle
POST   /api/pos/orders/{id}/close   - Sipariş kapat
POST   /api/pos/orders/{id}/void    - Siparişi boşalt
GET    /api/pos/orders/{id}       - Sipariş detayları
```

### 📦 Ürün Yönetimi
- Ürün listeleme
- Yeni ürün ekleme
- Ürün bilgisi düzenleme
- Ürün silme (siparişte kullanılmamışsa)

**Routes:**
```
GET    /products           - Ürün listesi
GET    /products/create    - Ürün oluştur formu
POST   /products           - Ürün kaydet
GET    /products/{id}      - Ürün detayı
GET    /products/{id}/edit - Düzenle formu
PUT    /products/{id}      - Ürün güncelle
DELETE /products/{id}      - Ürün sil
```

### 🍳 Mutfak Ekranı
- Yeni gelen siparişleri göster
- Hazır işaretle
- Kategori bazında filtreleme

**Routes:**
- `GET /mutfak` - Mutfak ekranı
- `POST /mutfak/mark-ready` - Sipariş hazır yap

### 👤 Kullanıcı Paneli
- Profil bilgileri
- Hızlı linkler
- Sistem navigasyonu

**Routes:**
- `GET /dashboard` - Kontrol paneli

---

## 💰 Hesaplama Sistemi

### Fiyatlandırma Akışı
```
Subtotal = Tüm satır fiyatları toplamı

Service = Subtotal × (service_rate / 100)

DiscountBase = Subtotal + Service

Discount = 
  - Yüzde Türü: DiscountBase × (discount_value / 100)
  - Sabit Tutar: discount_value

AfterDiscount = max(0, DiscountBase - Discount)

VAT = AfterDiscount × (vat_rate / 100)

Total = AfterDiscount + VAT

Due = max(0, Total - Paid)
```

### Masa Ayarları
Her masa için ayarlanabilir:
- **VAT Oranı** (varsayılan 10%)
- **Hizmet Ücreti Oranı** (varsayılan 0%)
- **İndirim Tipi** (none/percent/amount)
- **İndirim Değeri**

---

## 📊 Veri Modeli

### Tablolar
- **users** - Sistem kullanıcıları
- **products** - Ürün kataloğu
- **rooms** - Masalar/Mekanlar
- **pos_orders** - Siparişler (Adisyonlar)
- **order_items** - Sipariş satır kalemleri
- **payments** - Ödeme hareketleri

### İlişkiler
```
Room (1) ──→ (N) PosOrder
PosOrder (1) ──→ (N) OrderItem
PosOrder (1) ──→ (N) Payment
Product (1) ──→ (N) OrderItem
```

---

## 🎨 Responsive Design

Proje tüm cihazlarda optimize edilmiştir:
- 📱 **Mobile** (< 576px)
- 📱 **Tablet** (576px - 992px)
- 💻 **Desktop** (> 992px)

Bootstrap 5 ve custom CSS kullanılmıştır.

---

## ⚙️ Konfigürasyon

### Environment Variables
```env
APP_NAME=Laravel
APP_ENV=local
APP_DEBUG=true
DB_CONNECTION=sqlite
```

### Database Seeding
Test veri yüklemek için:
```bash
php artisan migrate:fresh --seed
```

---

## 🧪 Testing

Test çalıştırmak için:
```bash
php artisan test
```

---

## 📁 Proje Yapısı

```
laravel-cafe/
├── app/
│   ├── Http/Controllers/      # Controllers
│   │   ├── AuthController.php
│   │   ├── PosController.php
│   │   ├── ProductController.php
│   │   └── MutfakController.php
│   ├── Models/                # Eloquent Models
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Room.php
│   │   ├── PosOrder.php
│   │   ├── OrderItem.php
│   │   └── Payment.php
│   └── Exceptions/            # Exception Handling
├── database/
│   ├── migrations/            # Database Migrations
│   └── seeders/              # Database Seeders
├── resources/
│   └── views/                # Blade Templates
│       ├── auth/             # Auth views
│       ├── pos/              # POS views
│       ├── products/         # Product views
│       └── mutfak/           # Kitchen views
├── routes/
│   ├── web.php              # Web routes
│   └── api.php              # API routes
└── storage/                  # Logs, cache, uploads
```

---

## 🔒 Güvenlik

- **CSRF Koruması**: Tüm POST/PUT/DELETE işlemlerinde
- **Input Validasyonu**: Server-side validation
- **Şifre Hashleme**: Bcrypt algoritması
- **Kimlik Doğrulama**: Laravel Auth middleware
- **SQL Injection Önleme**: Prepared statements

---

## 🚨 Hata Yönetimi

Sistem aşağıdaki hataları işler:
- 404 - Sayfa/Kayıt bulunamadı
- 422 - Validasyon hatası
- 500 - Sunucu hatası

JSON API çağrıları için başarılı yanıtlar:
```json
{
  "success": true,
  "message": "İşlem başarılı",
  "data": { }
}
```

Hata yanıtları:
```json
{
  "success": false,
  "message": "Hata açıklaması",
  "errors": { }
}
```

---

## 📝 Lisans

MIT License

---

## 👥 Katkılar

Katkılar kabul edilmektedir. Lütfen pull request gönderin.

---

## 📞 Destek

Sorularınız için [GitHub Issues](../../issues) kullanınız.

---

**Geliştirici**: Proje Ekibi  
**Son Güncelleme**: Şubat 2026

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
