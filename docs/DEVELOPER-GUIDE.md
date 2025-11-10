# Geliştirici Dokümantasyonu

## Kod Yapısı

### Dizin Yapısı

```
otoYedekParcaScript/
├── app/
│   ├── Console/
│   │   └── Commands/          # Artisan komutları
│   │       ├── XmlImport.php
│   │       └── XmlImportScheduled.php
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/         # Admin panel controller'ları
│   │   │   ├── Frontend/      # Frontend controller'ları
│   │   │   └── XmlIntegration/ # XML import controller'ları
│   │   ├── Middleware/        # Middleware'ler
│   │   │   ├── AdminMiddleware.php
│   │   │   └── RateLimitMiddleware.php
│   │   └── Requests/          # Form request'ler
│   │       └── BaseRequest.php
│   ├── Mail/                  # Mail sınıfları
│   │   ├── OrderConfirmation.php
│   │   ├── OrderShipped.php
│   │   └── OrderDelivered.php
│   ├── Models/                # Eloquent modeller
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   └── ...
│   ├── Notifications/          # Notification sınıfları
│   │   ├── OrderSmsNotification.php
│   │   └── Channels/
│   │       └── SmsChannel.php
│   └── Services/              # Service sınıfları
│       ├── CacheService.php
│       ├── PaymentService.php
│       ├── ShippingService.php
│       ├── SmsService.php
│       └── FileUploadService.php
├── database/
│   ├── migrations/            # Database migration'ları
│   └── seeders/              # Database seeder'ları
├── resources/
│   ├── views/
│   │   ├── admin/            # Admin panel view'ları
│   │   ├── frontend/         # Frontend view'ları
│   │   ├── layouts/          # Layout dosyaları
│   │   └── emails/           # Email template'leri
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php               # Web route'ları
│   ├── api.php               # API route'ları
│   └── console.php           # Console route'ları
└── docs/                     # Dokümantasyon dosyaları
    ├── API.md
    ├── ADMIN-GUIDE.md
    ├── XML-IMPORT-GUIDE.md
    └── DEVELOPER-GUIDE.md
```

---

## Database Schema

### Ana Tablolar

#### users
- `id` (bigint, primary key)
- `name` (string)
- `email` (string, unique)
- `email_verified_at` (timestamp, nullable)
- `password` (string)
- `phone` (string, nullable)
- `company_name` (string, nullable)
- `tax_number` (string, nullable)
- `user_type` (enum: 'admin', 'dealer', 'customer')
- `is_verified` (boolean)
- `notes` (text, nullable)
- `remember_token` (string, nullable)
- `created_at`, `updated_at` (timestamps)

#### categories
- `id` (bigint, primary key)
- `parent_id` (bigint, foreign key → categories.id, nullable)
- `name` (string)
- `slug` (string, unique)
- `description` (text, nullable)
- `image` (string, nullable)
- `is_active` (boolean)
- `sort_order` (integer)
- `meta_title` (string, nullable)
- `meta_description` (text, nullable)
- `created_at`, `updated_at` (timestamps)

#### products
- `id` (bigint, primary key)
- `category_id` (bigint, foreign key → categories.id, nullable)
- `supplier_id` (bigint, foreign key → suppliers.id, nullable)
- `sku` (string, unique)
- `oem_code` (string, nullable)
- `name` (string)
- `slug` (string, unique)
- `description` (text, nullable)
- `short_description` (text, nullable)
- `price` (decimal 10,2)
- `sale_price` (decimal 10,2, nullable)
- `stock` (integer)
- `min_stock_level` (integer)
- `tax_rate` (decimal 5,2)
- `status` (enum: 'active', 'inactive', 'draft')
- `is_featured` (boolean)
- `manufacturer` (string, nullable)
- `part_type` (enum: 'oem', 'aftermarket')
- `meta_title` (string, nullable)
- `meta_description` (text, nullable)
- `views` (integer)
- `sales_count` (integer)
- `created_at`, `updated_at` (timestamps)

#### orders
- `id` (bigint, primary key)
- `user_id` (bigint, foreign key → users.id)
- `coupon_id` (bigint, foreign key → coupons.id, nullable)
- `order_number` (string, unique)
- `status` (enum: 'pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned')
- `payment_status` (enum: 'pending', 'paid', 'failed', 'refunded')
- `payment_method` (string)
- `payment_transaction_id` (string, nullable)
- `subtotal` (decimal 10,2)
- `tax_amount` (decimal 10,2)
- `shipping_cost` (decimal 10,2)
- `discount_amount` (decimal 10,2)
- `total` (decimal 10,2)
- `coupon_code` (string, nullable)
- Shipping address fields (name, phone, city, district, address, postal_code)
- Billing address fields (name, phone, city, district, address, postal_code)
- `cargo_company` (string, nullable)
- `tracking_number` (string, nullable)
- `bank_transfer_receipt` (string, nullable)
- `bank_transfer_receipt_uploaded_at` (timestamp, nullable)
- `bank_transfer_notes` (text, nullable)
- `notes` (text, nullable)
- `created_at`, `updated_at` (timestamps)

#### cars_brands
- `id` (bigint, primary key)
- `name` (string)
- `slug` (string, unique)
- `is_active` (boolean)
- `sort_order` (integer)
- `created_at`, `updated_at` (timestamps)

#### cars_models
- `id` (bigint, primary key)
- `brand_id` (bigint, foreign key → cars_brands.id)
- `name` (string)
- `slug` (string)
- `is_active` (boolean)
- `sort_order` (integer)
- `created_at`, `updated_at` (timestamps)

#### cars_years
- `id` (bigint, primary key)
- `model_id` (bigint, foreign key → cars_models.id)
- `year` (year)
- `motor_type` (string, nullable)
- `engine_code` (string, nullable)
- `is_active` (boolean)
- `created_at`, `updated_at` (timestamps)

### İlişkiler

**User → Orders:**
```php
User::hasMany(Order::class)
```

**Category → Products:**
```php
Category::hasMany(Product::class)
Category::belongsTo(Category::class, 'parent_id') // self-referencing
```

**Product → OrderItems:**
```php
Product::hasMany(OrderItem::class)
```

**Order → OrderItems:**
```php
Order::hasMany(OrderItem::class)
```

**CarBrand → CarModel:**
```php
CarBrand::hasMany(CarModel::class, 'brand_id')
```

**CarModel → CarYear:**
```php
CarModel::hasMany(CarYear::class, 'model_id')
```

---

## Deployment Kılavuzu

### Gereksinimler

- **PHP:** 8.1 veya üzeri
- **Composer:** 2.x
- **Node.js:** 18.x veya üzeri
- **NPM:** 9.x veya üzeri
- **MySQL/MariaDB:** 5.7 veya üzeri
- **Apache/Nginx:** Web sunucusu

### Kurulum Adımları

#### 1. Projeyi İndirme

```bash
git clone <repository-url>
cd otoYedekParcaScript
```

#### 2. Composer Bağımlılıklarını Yükleme

```bash
composer install --optimize-autoloader --no-dev
```

#### 3. Environment Dosyasını Ayarlama

```bash
cp .env.example .env
php artisan key:generate
```

`.env` dosyasını düzenleyin:
```env
APP_NAME="Oto Yedek Parça"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your_email
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@your-domain.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### 4. Database Migration ve Seeder

```bash
php artisan migrate --force
php artisan db:seed
```

#### 5. Storage Link

```bash
php artisan storage:link
```

#### 6. Frontend Asset Build

```bash
npm install
npm run build
```

#### 7. Cache Optimizasyonu

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

#### 8. Queue Worker (Opsiyonel)

Eğer queue kullanıyorsanız:

```bash
php artisan queue:work --daemon
```

Veya supervisor ile:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
stopwaitsecs=3600
```

#### 9. Cron Job Kurulumu

```bash
crontab -e
```

Aşağıdaki satırı ekleyin:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

#### 10. Web Sunucusu Yapılandırması

**Apache (.htaccess):**
```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

**Nginx:**
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path-to-your-project/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Güvenlik Kontrolleri

1. **.env Dosyası:** Production'da `APP_DEBUG=false` olmalı
2. **Dosya İzinleri:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```
3. **HTTPS:** SSL sertifikası kurulmalı
4. **Firewall:** Gereksiz portlar kapatılmalı
5. **Backup:** Düzenli veritabanı yedekleme

### Performans Optimizasyonu

1. **OPcache:** PHP OPcache etkinleştirilmeli
2. **Redis:** Cache için Redis kullanılabilir
3. **CDN:** Statik dosyalar için CDN kullanılabilir
4. **Database Indexing:** Gerekli index'ler oluşturulmalı
5. **Queue:** Uzun süren işlemler queue'ya alınmalı

### Monitoring

- **Log Dosyaları:** `storage/logs/laravel.log`
- **Error Tracking:** Laravel Log viewer veya external service
- **Performance Monitoring:** New Relic, Datadog vb.

---

## API Geliştirme

### Yeni API Endpoint Ekleme

1. `routes/api.php` dosyasına route ekleyin:

```php
Route::get('/new-endpoint', [NewController::class, 'index']);
```

2. Controller oluşturun:

```php
php artisan make:controller Api/NewController
```

3. Rate limiting ekleyin:

```php
Route::middleware('throttle:60,1')->group(function () {
    Route::get('/new-endpoint', [NewController::class, 'index']);
});
```

### API Authentication

API authentication için Laravel Sanctum kullanılabilir:

```php
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

---

## Service Sınıfları

### CacheService

Kategori ve ürün cache yönetimi:

```php
use App\Services\CacheService;

// Kategori cache
$categories = CacheService::getActiveCategories();

// Ürün cache
$featuredProducts = CacheService::getFeaturedProducts(8);

// Cache temizleme
CacheService::clearCategoryCache();
CacheService::clearProductCache();
```

### FileUploadService

Güvenli dosya yükleme:

```php
use App\Services\FileUploadService;

// Görsel yükleme
$path = FileUploadService::uploadImage($file, 'folder', 2048); // 2MB max

// Doküman yükleme
$path = FileUploadService::uploadDocument($file, 'folder', 5120); // 5MB max

// Dosya silme
FileUploadService::deleteFile($path);
```

### PaymentService

Ödeme işlemleri:

```php
use App\Services\PaymentService;

// İyzico ödeme
$result = PaymentService::processIyzico($order, $cardData);

// PayTR ödeme
$result = PaymentService::processPaytr($order, $cardData);
```

### ShippingService

Kargo işlemleri:

```php
use App\Services\ShippingService;

// Kargo etiketi oluşturma
$result = ShippingService::createShippingLabel($shippingCompany, $order);

// Kargo takip
$status = ShippingService::trackShipping($shippingCompany, $trackingNumber);
```

---

## Test Geliştirme

### Unit Test

```bash
php artisan make:test ProductTest
```

### Feature Test

```bash
php artisan make:test OrderTest --feature
```

### Test Çalıştırma

```bash
php artisan test
```

---

## Kod Standartları

### PSR-12

Kod PSR-12 standartlarına uygun olmalıdır.

### Naming Conventions

- **Controller:** PascalCase (örn: `ProductController`)
- **Model:** PascalCase (örn: `Product`)
- **Service:** PascalCase (örn: `CacheService`)
- **Variable:** camelCase (örn: `$productName`)
- **Method:** camelCase (örn: `getProducts()`)
- **Constant:** UPPER_SNAKE_CASE (örn: `MAX_FILE_SIZE`)

### Documentation

- **PHPDoc:** Tüm public method'lar için PHPDoc yorumları
- **Inline Comments:** Karmaşık kod blokları için açıklayıcı yorumlar

---

## Version Control

### Git Workflow

1. **Feature Branch:** Yeni özellik için branch oluştur
2. **Commit Messages:** Açıklayıcı commit mesajları
3. **Pull Request:** Code review için PR oluştur
4. **Merge:** Review sonrası merge et

### Commit Message Format

```
feat: Yeni özellik eklendi
fix: Hata düzeltildi
docs: Dokümantasyon güncellendi
style: Kod formatı düzeltildi
refactor: Kod refactor edildi
test: Test eklendi
chore: Diğer değişiklikler
```

---

**Son Güncelleme:** 2025-11-05

