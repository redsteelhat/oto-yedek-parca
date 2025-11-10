# Otomobil Yedek Parça E-Ticaret Scripti

Otomobil yedek parça satışı için geliştirilmiş, XML entegrasyonlu, tam özellikli e-ticaret sistemi.

## 🚀 Özellikler

### Temel Özellikler
- ✅ Araç uyumluluk filtreleme (Marka/Model/Yıl/Motor Tipi)
- ✅ Ürün yönetimi (Kategori, Stok, Fiyat, Görseller)
- ✅ Sipariş yönetimi (Durum takibi, Kargo bilgisi)
- ✅ Müşteri yönetimi (Adres, Sipariş geçmişi)
- ✅ Sepet ve Ödeme sistemi
- ✅ Kupon ve Kampanya sistemi

### XML Entegrasyonu
- ✅ Tedarikçi XML'inden ürün çekme
- ✅ Otomatik stok ve fiyat güncelleme
- ✅ XML mapping sistemi
- ✅ Import logları ve hata yönetimi
- ✅ Cron job ile otomatik güncelleme

### Admin Paneli
- ✅ Ürün yönetimi (CRUD)
- ✅ Sipariş yönetimi
- ✅ Müşteri yönetimi
- ✅ Tedarikçi yönetimi
- ✅ Kategori yönetimi
- ✅ Araç marka/model/yıl yönetimi

## 📋 Gereksinimler

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Node.js & NPM (Frontend assets için)

## 🔧 Kurulum

1. **Projeyi klonlayın veya indirin**
   ```bash
   cd otoYedekParcaScript
   ```

2. **Bağımlılıkları yükleyin**
   ```bash
   composer install
   npm install
   ```

3. **Ortam dosyasını oluşturun**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Veritabanı ayarlarını yapın**
   `.env` dosyasında veritabanı bilgilerinizi güncelleyin:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=yedekparca_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Migration'ları çalıştırın**
   ```bash
   php artisan migrate
   ```

6. **Storage linkini oluşturun**
   ```bash
   php artisan storage:link
   ```

7. **Frontend assets'leri derleyin**
   ```bash
   npm run dev
   # veya production için
   npm run build
   ```

## 🔐 Admin Kullanıcı Oluşturma

Veritabanına admin kullanıcı eklemek için:

```bash
php artisan tinker
```

Tinker içinde:
```php
$user = new App\Models\User();
$user->name = 'Admin';
$user->email = 'admin@example.com';
$user->password = bcrypt('password');
$user->user_type = 'admin';
$user->save();
```

## 📦 XML İçe Aktarma

### Manuel İçe Aktarma
```bash
php artisan xml:import {supplier_id}
```

### Tüm Tedarikçileri İçe Aktarma
```bash
php artisan xml:import
```

### Cron Job Kurulumu (Otomatik Güncelleme)
Crontab'a ekleyin:
```bash
* */6 * * * cd /path-to-project && php artisan xml:import >> /dev/null 2>&1
```
(Her 6 saatte bir güncelleme yapar)

## 🗂️ Proje Yapısı

```
app/
├── Console/
│   └── Commands/
│       └── XmlImport.php          # XML import komutu
├── Http/
│   ├── Controllers/
│   │   ├── Admin/                 # Admin paneli controller'ları
│   │   ├── Frontend/              # Frontend controller'ları
│   │   └── XmlIntegration/       # XML entegrasyon controller'ları
│   └── Middleware/
│       └── AdminMiddleware.php     # Admin yetkilendirme
├── Models/                         # Eloquent modelleri
└── ...

database/
├── migrations/                     # Veritabanı migration'ları
└── seeders/                       # Veritabanı seed'leri

routes/
└── web.php                         # Web route'ları

resources/
├── views/                         # Blade view'ları
│   ├── admin/                     # Admin paneli view'ları
│   └── frontend/                  # Frontend view'ları
└── ...
```

## 📊 Veritabanı Tabloları

### Ana Tablolar
- `cars_brands` - Araç markaları
- `cars_models` - Araç modelleri
- `cars_years` - Araç yılları ve motor tipleri
- `categories` - Ürün kategorileri
- `products` - Ürünler
- `product_car_compatibility` - Ürün-araç uyumluluğu
- `product_images` - Ürün görselleri
- `suppliers` - Tedarikçiler
- `supplier_xml_mappings` - XML mapping'leri
- `xml_import_logs` - Import logları
- `orders` - Siparişler
- `order_items` - Sipariş kalemleri
- `addresses` - Müşteri adresleri
- `coupons` - Kuponlar
- `campaigns` - Kampanyalar

## 🔄 API Endpoints

### Frontend
- `GET /` - Ana sayfa
- `GET /urunler` - Ürün listesi
- `GET /urunler/{slug}` - Ürün detayı
- `POST /sepet/ekle` - Sepete ekle
- `GET /odeme` - Ödeme sayfası

### Admin
- `GET /admin/dashboard` - Dashboard
- `GET /admin/products` - Ürün listesi
- `GET /admin/orders` - Sipariş listesi
- `GET /admin/suppliers` - Tedarikçi listesi
- `POST /admin/suppliers/{id}/import` - XML içe aktarma

## 🎨 Frontend Tema

Frontend view'ları henüz oluşturulmadı. Şu adımlarla devam edilebilir:

1. TailwindCSS veya Bootstrap kurulumu
2. Layout dosyaları oluşturma
3. Ana sayfa view'ı
4. Ürün listesi ve detay sayfaları
5. Sepet ve ödeme sayfaları

## 🔒 Güvenlik

- CSRF koruması aktif
- SQL Injection koruması (Eloquent ORM)
- XSS koruması (Blade template engine)
- Admin yetkilendirme middleware'i
- Şifreler bcrypt ile hash'lenir

## 📝 Notlar

- View'lar henüz oluşturulmadı (sıradaki adım)
- Authentication sistemi (Laravel Breeze/Jetstream) kurulmadı
- Ödeme entegrasyonu henüz eklenmedi
- Kargo entegrasyonu henüz yapılmadı

## 📄 Lisans

Bu proje özel bir projedir.

## 👨‍💻 Geliştirici

Geliştirme süreci devam etmektedir.

---

**Son Güncelleme:** 2025-11-05
# oto-yedek-parca
