<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\SupplierController as AdminSupplierController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CarBrandController as AdminCarBrandController;
use App\Http\Controllers\XmlIntegration\XmlImportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Locale Change
Route::get('/dil/{locale}', [\App\Http\Controllers\LocaleController::class, 'change'])->name('locale.change');

// Super Admin Routes (no tenant middleware)
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'super-admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    // Tenants
    Route::resource('tenants', \App\Http\Controllers\SuperAdmin\TenantController::class);
    Route::post('tenants/{tenant}/suspend', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/activate', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'activate'])->name('tenants.activate');
    Route::post('tenants/{id}/restore', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'restore'])->name('tenants.restore');
    Route::delete('tenants/{id}/force-delete', [\App\Http\Controllers\SuperAdmin\TenantController::class, 'forceDelete'])->name('tenants.force-delete');
});

// Chat Routes (Frontend)
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\ChatController::class, 'index'])->name('index');
    Route::get('/yeni', [\App\Http\Controllers\Frontend\ChatController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Frontend\ChatController::class, 'store'])->name('store');
    Route::get('/{chatRoom}', [\App\Http\Controllers\Frontend\ChatController::class, 'show'])->name('show');
    Route::post('/{chatRoom}/mesaj', [\App\Http\Controllers\Frontend\ChatController::class, 'sendMessage'])->name('send-message');
    Route::get('/{chatRoom}/mesajlar', [\App\Http\Controllers\Frontend\ChatController::class, 'getMessages'])->name('get-messages');
});

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Product Routes
Route::prefix('urunler')->name('products.')->group(function () {
    Route::get('/', [FrontendProductController::class, 'index'])->name('index');
    Route::get('/kategori/{slug}', [FrontendProductController::class, 'category'])->name('category');
    Route::get('/aracla-parca-bul', [FrontendProductController::class, 'findByCar'])->name('find-by-car');
    Route::get('/arama', [FrontendProductController::class, 'search'])->name('search');
    Route::get('/{slug}', [FrontendProductController::class, 'show'])->name('show');
    
    // Product Reviews
    Route::prefix('{slug}/yorumlar')->name('reviews.')->group(function () {
        Route::get('/yeni', [\App\Http\Controllers\Frontend\ProductReviewController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Frontend\ProductReviewController::class, 'store'])->name('store');
    });
});

// Cart Routes
Route::prefix('sepet')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    // Rate limit for cart operations (30 requests per minute)
    Route::middleware('throttle:30,1')->group(function () {
        Route::post('/ekle', [CartController::class, 'add'])->name('add');
        Route::post('/guncelle', [CartController::class, 'update'])->name('update');
        Route::delete('/sil/{id}', [CartController::class, 'remove'])->name('remove');
        Route::post('/temizle', [CartController::class, 'clear'])->name('clear');
        Route::post('/kupon-uygula', [CartController::class, 'applyCoupon'])->name('apply-coupon');
        Route::post('/kupon-kaldir', [CartController::class, 'removeCoupon'])->name('remove-coupon');
    });
});

// Checkout Routes - Rate limited for form submissions (10 requests per minute)
Route::prefix('odeme')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    
    // Step-by-step checkout
    Route::get('/adim-1', [CheckoutController::class, 'step1'])->name('step1');
    Route::get('/adim-2', [CheckoutController::class, 'step2'])->name('step2');
    Route::get('/adim-3', [CheckoutController::class, 'step3'])->name('step3');
    Route::get('/adim-4', [CheckoutController::class, 'step4'])->name('step4');
    Route::get('/onay/{order}', [CheckoutController::class, 'confirm'])->name('confirm');
    
    // Rate limited POST routes
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/adim-1', [CheckoutController::class, 'storeStep1'])->name('storeStep1');
        Route::post('/adim-2', [CheckoutController::class, 'storeStep2'])->name('storeStep2');
        Route::post('/adim-3', [CheckoutController::class, 'storeStep3'])->name('storeStep3');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });
});

// Payment Routes
Route::prefix('odeme-islemleri')->name('payment.')->middleware('auth')->group(function () {
    Route::post('/islem/{order}', [\App\Http\Controllers\Frontend\PaymentController::class, 'process'])->name('process');
    Route::get('/havale/{order}', [\App\Http\Controllers\Frontend\PaymentController::class, 'showBankTransfer'])->name('bank-transfer.show');
    Route::post('/havale/{order}/dekont', [\App\Http\Controllers\Frontend\PaymentController::class, 'uploadReceipt'])->name('bank-transfer.upload');
    Route::get('/paytr-form', [\App\Http\Controllers\Frontend\PaymentController::class, 'paytrForm'])->name('paytr.form');
    Route::get('/basarili/{order}', [\App\Http\Controllers\Frontend\PaymentController::class, 'success'])->name('success');
    Route::get('/basarisiz', [\App\Http\Controllers\Frontend\PaymentController::class, 'fail'])->name('fail');
});

// Payment Callbacks (no auth required)
Route::prefix('payment-callback')->name('payment.')->group(function () {
    Route::post('/iyzico', [\App\Http\Controllers\Frontend\PaymentController::class, 'iyzicoCallback'])->name('iyzico.callback');
    Route::post('/paytr', [\App\Http\Controllers\Frontend\PaymentController::class, 'paytrCallback'])->name('paytr.callback');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', AdminProductController::class);
    Route::post('products/bulk-action', [AdminProductController::class, 'bulkAction'])->name('products.bulk-action');
    Route::post('products/{product}/duplicate', [AdminProductController::class, 'duplicate'])->name('products.duplicate');
    Route::get('products/export', [AdminProductController::class, 'export'])->name('products.export');
    Route::post('products/import', [AdminProductController::class, 'import'])->name('products.import');
    Route::post('products/{product}/update-image-order', [AdminProductController::class, 'updateImageOrder'])->name('products.update-image-order');
    Route::delete('products/{product}/images/{imageId}', [AdminProductController::class, 'deleteImage'])->name('products.delete-image');
    
    // Orders
    Route::resource('orders', AdminOrderController::class);
    
    // Product Reviews
    Route::get('reviews', [\App\Http\Controllers\Admin\ProductReviewController::class, 'index'])->name('reviews.index');
    
    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('index');
        Route::get('/sales', [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [\App\Http\Controllers\Admin\ReportController::class, 'products'])->name('products');
        Route::get('/customers', [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('customers');
    });
    
    // Chat
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ChatController::class, 'index'])->name('index');
        Route::get('/{chatRoom}', [\App\Http\Controllers\Admin\ChatController::class, 'show'])->name('show');
        Route::post('/{chatRoom}/mesaj', [\App\Http\Controllers\Admin\ChatController::class, 'sendMessage'])->name('send-message');
        Route::post('/{chatRoom}/durum', [\App\Http\Controllers\Admin\ChatController::class, 'updateStatus'])->name('update-status');
        Route::post('/{chatRoom}/ata', [\App\Http\Controllers\Admin\ChatController::class, 'assign'])->name('assign');
        Route::post('/{chatRoom}/kapat', [\App\Http\Controllers\Admin\ChatController::class, 'close'])->name('close');
        Route::get('/{chatRoom}/mesajlar', [\App\Http\Controllers\Admin\ChatController::class, 'getMessages'])->name('get-messages');
    });
    Route::get('reviews/{review}/edit', [\App\Http\Controllers\Admin\ProductReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('reviews/{review}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'update'])->name('reviews.update');
    Route::post('reviews/{review}/approve', [\App\Http\Controllers\Admin\ProductReviewController::class, 'approve'])->name('reviews.approve');
    Route::post('reviews/{review}/reject', [\App\Http\Controllers\Admin\ProductReviewController::class, 'reject'])->name('reviews.reject');
    Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ProductReviewController::class, 'destroy'])->name('reviews.destroy');
    Route::post('reviews/bulk-action', [\App\Http\Controllers\Admin\ProductReviewController::class, 'bulkAction'])->name('reviews.bulk-action');
    Route::post('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::post('orders/{order}/tracking', [AdminOrderController::class, 'updateTracking'])->name('orders.tracking');
    Route::post('orders/{order}/notes', [AdminOrderController::class, 'notes'])->name('orders.notes');
    Route::put('orders/{order}/cancel', [AdminOrderController::class, 'cancel'])->name('orders.cancel');
    Route::put('orders/{order}/return', [AdminOrderController::class, 'return'])->name('orders.return');
    Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
    Route::post('orders/{order}/create-shipping-label', [AdminOrderController::class, 'createShippingLabel'])->name('orders.create-shipping-label');
    Route::get('orders/{order}/track-shipping', [AdminOrderController::class, 'trackShipping'])->name('orders.track-shipping');
    
    // Customers
    Route::resource('customers', AdminCustomerController::class);
    
    // Suppliers
    Route::resource('suppliers', AdminSupplierController::class);
    Route::post('suppliers/{supplier}/import', [AdminSupplierController::class, 'import'])->name('suppliers.import');
    
    // XML Mapping
    Route::prefix('suppliers/{supplier}/xml-mapping')->name('xml-mapping.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\XmlMappingController::class, 'index'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\XmlMappingController::class, 'store'])->name('store');
        Route::post('/test', [\App\Http\Controllers\Admin\XmlMappingController::class, 'test'])->name('test');
    });
    
    // Categories
    Route::resource('categories', AdminCategoryController::class);
    Route::post('categories/update-order', [AdminCategoryController::class, 'updateOrder'])->name('categories.update-order');
    
    // Car Brands
    Route::resource('car-brands', AdminCarBrandController::class);
    Route::get('car-brands/export', [AdminCarBrandController::class, 'export'])->name('car-brands.export');
    Route::post('car-brands/import', [AdminCarBrandController::class, 'import'])->name('car-brands.import');
    
    // Car Models
    Route::resource('car-models', \App\Http\Controllers\Admin\CarModelController::class);
    
    // Car Years
    Route::resource('car-years', \App\Http\Controllers\Admin\CarYearController::class);
    
    // Pages (CMS)
    Route::resource('pages', \App\Http\Controllers\Admin\PageController::class);
    
    // Coupons
    Route::resource('coupons', \App\Http\Controllers\Admin\CouponController::class);
    Route::get('coupons/reports', [\App\Http\Controllers\Admin\CouponController::class, 'reports'])->name('coupons.reports');
    
    // Campaigns
    Route::resource('campaigns', \App\Http\Controllers\Admin\CampaignController::class);
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('settings.update');
    
    // Bank Transfers
    Route::prefix('havale-eft')->name('bank-transfers.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\BankTransferController::class, 'index'])->name('index');
        Route::get('/{order}', [\App\Http\Controllers\Admin\BankTransferController::class, 'show'])->name('show');
        Route::post('/{order}/onayla', [\App\Http\Controllers\Admin\BankTransferController::class, 'approve'])->name('approve');
        Route::post('/{order}/reddet', [\App\Http\Controllers\Admin\BankTransferController::class, 'reject'])->name('reject');
    });
    
    // Shipping Companies
    Route::resource('shipping-companies', \App\Http\Controllers\Admin\ShippingCompanyController::class);
});

// XML Import Routes
Route::prefix('xml')->name('xml.')->middleware(['auth', 'admin'])->group(function () {
    Route::post('/import/{supplier}', [XmlImportController::class, 'import'])->name('import');
    Route::get('/logs/{supplier}', [XmlImportController::class, 'logs'])->name('logs');
});

// Auth Routes
Route::get('/giris', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

Route::get('/login', function () {
    return redirect()->route('login');
});

Route::get('/kayit', function () {
    return view('auth.register');
})->name('register')->middleware('guest');

Route::post('/kayit', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'phone' => 'nullable|string|max:20',
    ]);

    $user = \App\Models\User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => bcrypt($validated['password']),
        'phone' => $validated['phone'] ?? null,
        'user_type' => 'customer',
    ]);

    \Illuminate\Support\Facades\Auth::login($user);

    // Send registration confirmation email
    try {
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RegistrationConfirmation($user));
    } catch (\Exception $e) {
        // Log error but don't fail the request
        \Log::error('Registration confirmation email gönderim hatası: ' . $e->getMessage());
    }

    return redirect()->route('home')->with('success', 'Kayıt başarılı! Hoş geldiniz.');
})->middleware('guest');

Route::post('/giris', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        $user = \Illuminate\Support\Facades\Auth::user();
        
        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }
        
        return redirect()->intended(route('home'));
    }

    return back()->withErrors([
        'email' => 'Giriş bilgileri hatalı.',
    ]);
})->middleware('guest');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    return redirect()->route('login');
})->middleware('guest');

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout')->middleware('auth');

// Account Routes
Route::prefix('hesabim')->name('account.')->middleware('auth')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\AccountController::class, 'index'])->name('index');
    Route::get('/profil', [\App\Http\Controllers\Frontend\AccountController::class, 'profile'])->name('profile');
    Route::put('/profil', [\App\Http\Controllers\Frontend\AccountController::class, 'updateProfile'])->name('update-profile');
    Route::get('/siparisler', [\App\Http\Controllers\Frontend\AccountController::class, 'orders'])->name('orders');
    Route::get('/siparis/{order}', [\App\Http\Controllers\Frontend\AccountController::class, 'orderDetail'])->name('order-detail');
    Route::get('/adresler', [\App\Http\Controllers\Frontend\AccountController::class, 'addresses'])->name('addresses');
    Route::post('/adresler', [\App\Http\Controllers\Frontend\AccountController::class, 'storeAddress'])->name('store-address');
    Route::get('/araclarim', [\App\Http\Controllers\Frontend\AccountController::class, 'cars'])->name('cars');
    Route::get('/favoriler', [\App\Http\Controllers\Frontend\WishlistController::class, 'index'])->name('wishlist');
});

// Wishlist Routes (AJAX)
Route::prefix('favoriler')->name('wishlist.')->middleware('auth')->group(function () {
    Route::post('/ekle/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'add'])->name('add');
    Route::delete('/kaldir/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'remove'])->name('remove');
    Route::post('/toggle/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'toggle'])->name('toggle');
    Route::get('/kontrol/{product}', [\App\Http\Controllers\Frontend\WishlistController::class, 'check'])->name('check');
});

// Static Pages
Route::get('/hakkimizda', function () {
    return view('frontend.pages.about');
})->name('about');

Route::get('/iletisim', function () {
    return view('frontend.pages.contact');
})->name('contact');

// Contact form - Rate limited (5 requests per minute)
Route::post('/iletisim', [\App\Http\Controllers\Frontend\ContactController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/kvkk', function () {
    return view('frontend.pages.privacy');
})->name('privacy');

Route::get('/mesafeli-satis-sozlesmesi', function () {
    return view('frontend.pages.distance-sales');
})->name('distance-sales');

Route::get('/iade-degisim-kosullari', function () {
    return view('frontend.pages.return-policy');
})->name('return-policy');

Route::get('/sss', function () {
    return view('frontend.pages.faq');
})->name('faq');

// Dynamic CMS Pages
Route::get('/sayfa/{slug}', [\App\Http\Controllers\Frontend\PageController::class, 'show'])->name('pages.show');

// Campaign Routes
Route::prefix('kampanyalar')->name('campaigns.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\CampaignController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\Frontend\CampaignController::class, 'show'])->name('show');
});
