# Kafe POS → Google Play Store Yükleme Rehberi

## Ön Gereksinimler
- ✅ HTTPS aktif: https://caffe-pos.com
- ✅ PWA manifest.json mevcut
- ✅ Service Worker (sw.js) mevcut
- ✅ 192x192 ve 512x512 ikonlar mevcut
- ✅ Google Play Developer hesabı ($25 tek sefer)

---

## YÖNTEM 1: PWABuilder (ÖNERİLEN - En Kolay)

### Adım 1: PWABuilder'da AAB Oluşturma
1. Tarayıcıda **https://www.pwabuilder.com** adresine gidin
2. URL kutusuna `https://caffe-pos.com` yazın ve "Start" butonuna tıklayın
3. PWA analiz edildikten sonra **"Package for stores"** butonuna tıklayın
4. **"Android"** seçeneğini seçin → **"Generate Package"** tıklayın
5. Açılan formda şu bilgileri girin:
   - **Package ID**: `com.caffepos.app`
   - **App name**: `Kafe POS`
   - **App version**: `1`
   - **App version name**: `1.0.0`
   - **Host**: `caffe-pos.com`
   - **Start URL**: `/adisyon`
   - **Theme color**: `#27A0B1`
   - **Background color**: `#0a0a0a`
   - **Status bar color**: `#27A0B1`
   - **Nav color**: `#0a0a0a`
   - **Signing key**: "Create new" seçin (yeni keystore oluşturur)
     - **Key alias**: `caffepos`
     - **Key password**: (güçlü bir şifre belirleyin, NOT ALIN!)
     - **Key store password**: (aynı veya farklı güçlü şifre, NOT ALIN!)
6. **"Download"** butonuna tıklayın
7. ZIP dosyası inecek, içinde:
   - `app-release-signed.aab` → bu dosyayı Google Play'e yükleyeceksiniz
   - `signing-key-info.txt` → imzalama bilgileri
   - `assetlinks.json` → bunu web sitenize yüklemeniz gerekiyor

### Adım 2: Digital Asset Links (assetlinks.json) Yükleme
PWABuilder'dan indirdiğiniz ZIP'deki `assetlinks.json` dosyasındaki SHA-256 fingerprint'i kullanın.

Bu dosya zaten şu yolda hazır:
```
laravel-cafe/public/.well-known/assetlinks.json
```

PWABuilder'dan aldığınız `assetlinks.json`'daki sha256_cert_fingerprints değerini bu dosyaya kopyalayın.

Dosyanın şu adreste erişilebilir olduğunu doğrulayın:
```
https://caffe-pos.com/.well-known/assetlinks.json
```

### Adım 3: Sunucuya Deploy Etme
```bash
# Sunucuda .well-known klasörünün erişilebilir olduğundan emin olun
# Nginx config'de bu zaten var: location ~ /\.(?!well-known)
# Yanlızca dosyayı deploy edin
```

---

## YÖNTEM 2: Bubblewrap CLI (Manuel)

### Adım 1: İlk kurulum (zaten yapıldı)
```powershell
npm install -g @bubblewrap/cli
```

### Adım 2: TWA Projesi Başlatma
```powershell
cd laravel-cafe/android-twa
bubblewrap init --manifest="https://caffe-pos.com/manifest.json"
```
İnteraktif sorulara şu cevapları verin:
- JDK kurulsun mu? → **Yes**
- Android SDK kurulsun mu? → **Yes**
- Package ID → `com.caffepos.app`
- Diğer değerler manifest'ten otomatik gelir, Enter ile geçin
- Signing key → Yeni oluştur, şifreleri NOT ALIN

### Adım 3: Build
```powershell
cd laravel-cafe/android-twa
bubblewrap build
```
Bu komut `app-release-signed.aab` dosyasını oluşturur.

### Adım 4: SHA-256 Fingerprint Alma
```powershell
# Bubblewrap build sonrasında fingerprint gösterilir
# Veya keytool ile:
keytool -list -v -keystore ./android.keystore -alias caffepos
```
Fingerprint'i `public/.well-known/assetlinks.json` dosyasına ekleyin.

---

## Google Play Console'a Yükleme

### 1. Uygulama Oluşturma
1. https://play.google.com/console adresine gidin
2. **"Uygulama oluştur"** tıklayın
3. Bilgileri doldurun:
   - **Uygulama adı**: `Kafe POS`
   - **Varsayılan dil**: Türkçe
   - **Uygulama türü**: Uygulama
   - **Ücretsiz / Ücretli**: İstediğinizi seçin
   - Tüm beyanları onaylayın

### 2. Mağaza Listesi (Store Listing)
Sol menüden "Mağaza listesi" > "Ana mağaza listesi" bölümüne gidin:
- **Kısa açıklama** (max 80 karakter):
  `Kafe ve restoran için modern adisyon ve sipariş takip sistemi`
- **Tam açıklama** (max 4000 karakter):
  ```
  Kafe POS, kafe ve restoran işletmeleri için geliştirilmiş modern bir
  adisyon ve sipariş takip sistemidir.

  Özellikler:
  • Masa bazlı adisyon yönetimi
  • Gerçek zamanlı mutfak ekranı
  • Paket sipariş takibi
  • Ürün ve kategori yönetimi
  • Garson sipariş takibi
  • Ödeme yönetimi
  • Çoklu oda/bölüm desteği

  Hızlı, kolay ve güvenilir POS çözümü.
  ```

### 3. Grafikler (Zorunlu)
Google Play şu görselleri ister:
- **Uygulama simgesi**: 512 x 512 px (icon-512.png'yi kullanın)
- **Öne çıkan görsel**: 1024 x 500 px (oluşturmanız gerekiyor)
- **Ekran görüntüleri**: En az 2 adet (telefon: 320-3840px arası)
  - Adisyon/masa sayfasının ekran görüntüsü
  - Mutfak ekranının ekran görüntüsü
  - Sipariş detayı ekran görüntüsü

### 4. İçerik Derecelendirme
Sol menüden "İçerik derecelendirme" bölümüne gidin:
- Anketi doldurun (iş uygulaması olduğu için genelde "Herkes" çıkar)

### 5. Uygulama İçeriği
- **Gizlilik politikası URL'si**: Bir gizlilik politikası sayfası oluşturmanız gerekiyor
- **Reklam**: "Hayır, reklam içermiyor" seçin
- **Hedef kitle**: 18+ (iş uygulaması)

### 6. AAB Yükleme
1. Sol menü → **"Sürüm"** → **"Üretim"**
2. **"Yeni sürüm oluştur"** tıklayın
3. **App Signing**: Google Play App Signing'i etkinleştirin
4. PWABuilder veya Bubblewrap'tan aldığınız `app-release-signed.aab` dosyasını yükleyin
5. Sürüm notları yazın: "İlk sürüm - Kafe POS v1.0.0"
6. **"İncelemeye gönder"** tıklayın

### 7. İnceleme Süreci
- Google inceleme süreci genellikle 1-7 gün sürer
- TWA uygulamalar genellikle hızlı onaylanır
- Sorun olursa Google Play Console'dan bildirim gelir

---

## Önemli Notlar

### assetlinks.json Hakkında
- Bu dosya `https://caffe-pos.com/.well-known/assetlinks.json` adresinde erişilebilir OLMALIDIR
- Content-Type: `application/json` olmalıdır
- SHA-256 fingerprint'in doğru olduğundan emin olun
- Bu dosya yanlış olursa uygulama Chrome Custom Tab olarak açılır (adres çubuğu görünür)

### Keystore Hakkında
- Keystore dosyasını ve şifrelerini **GÜVENLİ** bir yerde saklayın
- Keystore kaybederseniz güncelleme yükleyemezsiniz!
- Google Play App Signing etkinse Google yedek tutar

### Gizlilik Politikası
Google Play'e yüklemek için bir gizlilik politikası sayfası zorunludur.
Örnek URL: `https://caffe-pos.com/privacy-policy`

---

## Hızlı Kontrol Listesi

- [ ] manifest.json düzeltildi (icon purpose ayrıldı)
- [ ] PWABuilder veya Bubblewrap ile AAB oluşturuldu
- [ ] assetlinks.json sunucuya yüklendi ve erişilebilir
- [ ] Google Play Console'da uygulama oluşturuldu
- [ ] Mağaza listesi dolduruldu (açıklama, ekran görüntüleri)
- [ ] İçerik derecelendirme tamamlandı
- [ ] Gizlilik politikası sayfası oluşturuldu
- [ ] AAB dosyası yüklendi
- [ ] İncelemeye gönderildi
