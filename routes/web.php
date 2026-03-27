<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MutfakController;
use App\Http\Controllers\AdisyonController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PackageOrderController;

// ── Auth (misafir) ─────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register',         [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',        [AuthController::class, 'doRegister']);
    Route::get('/forgot-password',  [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password',  [AuthController::class, 'resetPassword'])->name('password.update');
});

// Giriş sayfaları her zaman erişilebilir (oturum çakışmasını önlemek için)
Route::get('/login',       [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',      [AuthController::class, 'doLogin'])->middleware('throttle:5,1');
Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// iyzico callback kaldırıldı — havale onayı için POST
Route::post('/payment/confirm-transfer', [PaymentController::class, 'confirmTransfer'])
    ->name('payment.confirm-transfer')
    ->middleware('auth');

// QR Menü (public, token ile)
Route::get('/menu/{token}', [\App\Http\Controllers\MenuController::class, 'index'])->name('menu.index');

// ── Korumalı rotalar ───────────────────────────────────────────────
 Route::middleware('auth')->group(function () {

    // Ana sayfa → adisyon'a yönlendir
    Route::get('/', function () {
        return redirect()->route('adisyon.index');
    });

    // UI Tema ayarları
    Route::post('/settings/theme', [AuthController::class, 'saveTheme'])->name('settings.theme');

    // ── Abonelik rotaları (subscribed middleware'den muıf) ──
    Route::get('/subscription/select',   [SubscriptionController::class, 'select'])->name('subscription.select');
    Route::post('/subscription/request', [SubscriptionController::class, 'request'])->name('subscription.request');
    Route::get('/subscription/pending',  [SubscriptionController::class, 'pending'])->name('subscription.pending');

    // ── Ödeme rotaları (subscribed'dan muaf) ──
    Route::get('/payment/checkout', [PaymentController::class, 'checkout'])->name('payment.checkout');
    Route::get('/payment/result',   [PaymentController::class, 'result'])->name('payment.result');

    // Admin paneli (abonelikten muıf)
    Route::middleware('admin')->group(function () {
        Route::get('/admin',                          [AdminController::class, 'index'])->name('admin.index');
        Route::get('/admin/user/{user}',              [AdminController::class, 'show'])->name('admin.show');
        Route::delete('/admin/user/{user}',           [AdminController::class, 'deleteUser'])->name('admin.delete-user');
        Route::post('/admin/impersonate/{user}',      [AdminController::class, 'impersonate'])->name('admin.impersonate');
        Route::post('/admin/approve/{user}',          [AdminController::class, 'approveSubscription'])->name('admin.approve');
        Route::post('/admin/reject/{user}',           [AdminController::class, 'rejectSubscription'])->name('admin.reject');
        Route::post('/admin/cancel/{user}',           [AdminController::class, 'cancelSubscription'])->name('admin.cancel');
        Route::get('/admin/cancel/{user}',            fn() => redirect()->route('admin.index'));
        Route::post('/admin/prices',                  [AdminController::class, 'updatePrices'])->name('admin.update-prices');
        Route::post('/admin/bank-settings',           [AdminController::class, 'updateBankSettings'])->name('admin.update-bank');
        Route::post('/admin/user/{user}/change-password', [AdminController::class, 'changePassword'])->name('admin.change-password');
    });
    Route::post('/admin/stop-impersonate', [AdminController::class, 'stopImpersonate'])->name('admin.stop-impersonate');

    // ── Abonelik gerektiren rotalar ──
    Route::middleware('subscribed')->group(function () {

        // Mutfak
        Route::get('/mutfak', [MutfakController::class, 'index'])->name('mutfak.index');
        Route::get('/mutfak/orders', [MutfakController::class, 'poll'])->name('mutfak.poll');
        Route::post('/mutfak/mark-ready', [MutfakController::class, 'markReady'])->name('mutfak.mark-ready');

        // Adisyon (garson + owner erişebilir)
        Route::get('/adisyon', [AdisyonController::class, 'index'])->name('adisyon.index');
        Route::post('/adisyon/masa-olustur', [AdisyonController::class, 'masaOlustur'])->name('adisyon.masa-olustur');
        Route::get('/adisyon/ready-check-all', [AdisyonController::class, 'readyCheckAll'])->name('adisyon.ready-check-all');
        Route::get('/adisyon/masa/{room}/data', [AdisyonController::class, 'masaData'])->name('adisyon.masa-data');
        Route::get('/adisyon/masa/{room}/ready-check', [AdisyonController::class, 'readyCheck'])->name('adisyon.ready-check');
        Route::post('/adisyon/masa/{room}/ekle', [AdisyonController::class, 'ekle'])->name('adisyon.ekle');
        Route::post('/adisyon/masa/{room}/sil-item', [AdisyonController::class, 'silItem'])->name('adisyon.sil-item');
        Route::post('/adisyon/masa/{room}/qty', [AdisyonController::class, 'updateQty'])->name('adisyon.qty');
        Route::post('/adisyon/masa/{room}/item-note', [AdisyonController::class, 'updateItemNote'])->name('adisyon.item-note');
        Route::post('/adisyon/masa/{room}/rename', [AdisyonController::class, 'masaRename'])->name('adisyon.rename');
        Route::post('/adisyon/masa/{room}/toggle', [AdisyonController::class, 'masaToggle'])->name('adisyon.toggle');
        Route::post('/adisyon/masa/{room}/note', [AdisyonController::class, 'saveNote'])->name('adisyon.note');
        Route::post('/adisyon/masa/{room}/notified', [AdisyonController::class, 'markNotified'])->name('adisyon.notified');
        Route::post('/adisyon/masa/{room}/fire', [AdisyonController::class, 'fireTokitchen'])->name('adisyon.fire');
        Route::post('/adisyon/masa/{room}/transfer', [AdisyonController::class, 'transferMasa'])->name('adisyon.transfer');

        // Ürün listeleme (garson da görebilmeli)
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');

        // ── Sadece owner erişebilir (garson erişemez) ──
        Route::middleware('owner')->group(function () {
            // Ödeme
            Route::post('/adisyon/masa/{room}/odeme', [AdisyonController::class, 'odemeAl'])->name('adisyon.odeme');
            // Temizle & Sil
            Route::post('/adisyon/masa/{room}/temizle', [AdisyonController::class, 'temizle'])->name('adisyon.temizle');
            Route::delete('/adisyon/masa/{room}', [AdisyonController::class, 'masaSil'])->name('adisyon.masa-sil');

            // Rapor & Geçmiş
            Route::get('/adisyon/rapor', [AdisyonController::class, 'rapor'])->name('adisyon.rapor');
            Route::get('/adisyon/gecmis', [AdisyonController::class, 'odemeGecmisi'])->name('adisyon.gecmis');
            Route::delete('/adisyon/order/{order}', [AdisyonController::class, 'deleteOrder'])->name('adisyon.delete-order');

            // Ürün CRUD (ekleme/düzenleme/silme)
            Route::post('/products', [ProductController::class, 'store'])->name('products.store');
            Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
            Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

            // Kategori görselleri
            Route::get('/categories/images',   [CategoryController::class, 'listImages'])->name('categories.images');
            Route::post('/categories/image',   [CategoryController::class, 'uploadImage'])->name('categories.upload');
            Route::delete('/categories/image', [CategoryController::class, 'deleteImage'])->name('categories.delete');

            // Garson Yönetimi
            Route::get('/waiters',           [WaiterController::class, 'index'])->name('waiters.index');
            Route::post('/waiters',          [WaiterController::class, 'store'])->name('waiters.store');
            Route::delete('/waiters/{user}', [WaiterController::class, 'destroy'])->name('waiters.destroy');

            // Paket Sipariş Yönetimi
            Route::get('/paket-siparis',               [PackageOrderController::class, 'index']);
            Route::post('/paket-siparis',              [PackageOrderController::class, 'store']);
            Route::get('/paket-siparis/stats',         [PackageOrderController::class, 'stats']);
            Route::get('/paket-siparis/settings',      [PackageOrderController::class, 'getSettings']);
            Route::post('/paket-siparis/settings',     [PackageOrderController::class, 'saveSettings']);
            Route::post('/paket-siparis/test-connection', [PackageOrderController::class, 'testConnection']);
            Route::post('/paket-siparis/{packageOrder}/status', [PackageOrderController::class, 'updateStatus']);
            Route::delete('/paket-siparis/{packageOrder}', [PackageOrderController::class, 'destroy']);
        });

    }); // end subscribed

}); // end auth

// ── Webhook: Dış platformlardan sipariş al (public) ────────────────
Route::post('/api/paket-siparis/webhook', [PackageOrderController::class, 'webhook']);
