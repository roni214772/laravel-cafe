#!/bin/bash
#════════════════════════════════════════════════════════════════
#  CafePro VDS Kurulum Scripti (Ubuntu 22.04 / 24.04)
#  Kullanım: SSH ile sunucuya bağlan, bu script'i çalıştır
#  bash deploy-vds.sh
#════════════════════════════════════════════════════════════════

set -e

DOMAIN="_"                          # domain gelince güncellenecek
APP_DIR="/var/www/cafepro"
DB_NAME="cafepro"
DB_USER="cafepro_user"
DB_PASS="CafeP0s_2025!xQ9z"       # güçlü şifre
REPO_URL="https://github.com/roni214772/laravel-cafe.git"

echo "════════════════════════════════════════"
echo "  CafePro VDS Kurulumu Başlıyor..."
echo "════════════════════════════════════════"

# ── 1. Sistem Güncelleme ─────────────────────────────────────
echo "[1/9] Sistem güncelleniyor..."
apt update && apt upgrade -y

# ── 2. Gerekli Paketler ──────────────────────────────────────
echo "[2/9] PHP, Nginx, MySQL, Node.js kuruluyor..."
apt install -y software-properties-common
apt update
apt install -y \
    nginx \
    mysql-server \
    php8.4-fpm php8.4-cli php8.4-mysql php8.4-mbstring php8.4-xml \
    php8.4-curl php8.4-zip php8.4-gd php8.4-intl php8.4-bcmath \
    php8.4-readline php8.4-tokenizer \
    supervisor \
    git unzip curl

# Node.js 20 LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ── 3. MySQL Veritabanı ──────────────────────────────────────
echo "[3/9] MySQL veritabanı oluşturuluyor..."
mysql -u root <<EOF
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
EOF

# ── 4. Proje Dosyaları ───────────────────────────────────────
echo "[4/9] Proje dosyaları kuruluyor..."
mkdir -p ${APP_DIR}

if [ -n "$REPO_URL" ]; then
    git clone --branch master ${REPO_URL} ${APP_DIR}
else
    echo "  → REPO_URL boş. Dosyaları elle yüklemen gerekiyor:"
    echo "    scp -r ./laravel-cafe/* root@SUNUCU-IP:${APP_DIR}/"
    echo "    veya FileZilla/WinSCP ile yükle"
    echo "  Dosyalar yüklendikten sonra bu script'i tekrar çalıştır"
fi

# ── 5. Laravel Kurulumu ──────────────────────────────────────
echo "[5/9] Laravel yapılandırılıyor..."
cd ${APP_DIR}

# .env dosyasını kopyala ve düzenle
if [ -f .env.production ]; then
    cp .env.production .env
fi

# Bağımlılıklar
composer install --no-dev --optimize-autoloader --no-interaction
npm ci --production=false
npm run build

# Laravel komutları
php artisan key:generate --force
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 6. Dosya İzinleri ────────────────────────────────────────
echo "[6/9] Dosya izinleri ayarlanıyor..."
chown -R www-data:www-data ${APP_DIR}
chmod -R 755 ${APP_DIR}
chmod -R 775 ${APP_DIR}/storage ${APP_DIR}/bootstrap/cache

# ── 7. Nginx ─────────────────────────────────────────────────
echo "[7/9] Nginx yapılandırılıyor..."
cp ${APP_DIR}/deploy/nginx-cafepro.conf /etc/nginx/sites-available/cafepro
ln -sf /etc/nginx/sites-available/cafepro /etc/nginx/sites-enabled/cafepro
rm -f /etc/nginx/sites-enabled/default
# Domain adını güncelle
sed -i "s/SENIN-DOMAIN.com/${DOMAIN}/g" /etc/nginx/sites-available/cafepro
nginx -t && systemctl reload nginx

# ── 8. SSL (Let's Encrypt) ───────────────────────────────────
echo "[8/9] SSL sertifikası..."
if [ "$DOMAIN" = "_" ]; then
    echo "  → Domain yok, SSL atlanıyor. Domain gelince çalıştır:"
    echo "    certbot --nginx -d DOMAIN -d www.DOMAIN --agree-tos -m admin@DOMAIN"
else
    apt install -y certbot python3-certbot-nginx
    certbot --nginx -d ${DOMAIN} -d www.${DOMAIN} --non-interactive --agree-tos -m admin@${DOMAIN}
fi

# ── 9. Supervisor ────────────────────────────────────────────
echo "[9/9] Supervisor yapılandırılıyor..."
cp ${APP_DIR}/deploy/supervisor-cafepro.conf /etc/supervisor/conf.d/cafepro.conf
supervisorctl reread
supervisorctl update
supervisorctl start all

# ── Güvenlik Duvarı ──────────────────────────────────────────
ufw allow 22/tcp      # SSH
ufw allow 80/tcp      # HTTP
ufw allow 443/tcp     # HTTPS
ufw --force enable

echo ""
echo "════════════════════════════════════════"
echo "  ✓ CafePro başarıyla kuruldu!"
echo "  https://${DOMAIN}"
echo "════════════════════════════════════════"
echo ""
echo "Önemli notlar:"
echo "  • .env dosyasındaki şifreleri güncelle"
echo "  • iyzico production anahtarlarını yaz"
echo "  • İlk admin kullanıcısını oluştur:"
echo "    php artisan tinker"
echo "    User::first()->update(['role'=>'admin'])"
echo ""
