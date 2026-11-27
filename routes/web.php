<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\ShopController;
use App\Http\Controllers\Shop\BillplzController;
use Illuminate\Support\Facades\Route;

// Root → storefront (the admin lives at /login → /dashboard)
Route::get('/', [ShopController::class, 'index'])->name('home');

// ─── Admin Panel ────────────────────────────────────────────
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('dashboard/admin')->name('admin.')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/{order}/renew-payment', [BillplzController::class, 'renew'])->name('orders.renew');

        Route::get('customers', [CustomerController::class, 'index'])->name('customers.index');

        Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    });

    Route::prefix('dashboard')->name('profile.')->group(function () {
        Route::get('profile', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('profile', [ProfileController::class, 'update'])->name('update');
        Route::delete('profile', [ProfileController::class, 'destroy'])->name('destroy');
    });
});

// ─── Storefront ─────────────────────────────────────────────
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shop.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/thank-you/{order}', [CheckoutController::class, 'thankYou'])->middleware('signed')->name('checkout.thank-you');
Route::get('/payment/retry/{order}', [BillplzController::class, 'retry'])->middleware('signed')->name('payment.retry');
Route::post('/payment/renew/{order}', [BillplzController::class, 'renew'])->middleware('signed')->name('payment.renew');

// Legal pages
Route::view('/privacy-policy', 'legal.privacy-policy')->name('legal.privacy');
Route::view('/terms-and-conditions', 'legal.terms')->name('legal.terms');
Route::view('/return-policy', 'legal.return-policy')->name('legal.returns');

// ─── Billplz Webhook (no CSRF) ──────────────────────────────
Route::post('/webhook/billplz', [BillplzController::class, 'webhook'])->name('billplz.webhook');
Route::get('/payment/callback', [BillplzController::class, 'callback'])->name('billplz.callback');

// ─── Public demo (DEMO_MODE=true only) ──────────────────────
if (config('demo.enabled')) {
    Route::prefix('demo')->name('demo.')->group(function () {
        Route::post('enter', [\App\Http\Controllers\DemoController::class, 'enter'])->name('enter');
        Route::get('as/{role}', [\App\Http\Controllers\DemoController::class, 'as'])->name('as');
        Route::post('reset', [\App\Http\Controllers\DemoController::class, 'reset'])->name('reset');
        Route::get('pay/{order}', [\App\Http\Controllers\DemoController::class, 'pay'])->middleware('signed')->name('pay');
        Route::post('pay/{order}', [\App\Http\Controllers\DemoController::class, 'finish'])->middleware('signed')->name('pay.finish');
    });
}

require __DIR__.'/auth.php';
