# Otomobil Yedek Parça E-Ticaret Scripti

Çoklu tenant mimarisi, canlı destek ve XML tedarikçi entegrasyonları ile otomobil yedek parça satışı için geliştirilmiş uçtan uca e-ticaret platformu.

## 🚀 Öne Çıkan Yetkinlikler

### Çoklu Tenant Mimarisi
- 🌐 Alt alan adı veya özel alan adına göre otomatik tenant seçimi
- 🏪 Super Admin panelinden sınırsız mağaza yönetimi
- 🪪 Tenant bazlı veri izolasyonu (kategori, ürün, sipariş, müşteri, ayarlar vb.)
- 🎨 Tenant başına marka kimliği (logo, favicon, renkler, metinler)
- 🏠 Ana alan adı üzerinde tüm tenant ürünlerini listeleyen birleşik ana sayfa

### Satış & Operasyon
- ✅ Araç uyumluluk filtreleme (marka, model, yıl, motor)
- ✅ Ürün, stok, fiyat, varyasyon ve görsel yönetimi
- ✅ Sipariş, kargo, iade ve ödeme durum takibi
- ✅ Kampanya, kupon ve dinamik fiyatlandırma
- ✅ Sepet, ödeme adımları, havale dekontu yükleme

### Canlı Destek Sistemi
- 💬 Gerçek zamanlı müşteri temsilcisi sohbetleri
- 👥 Admin panelinde oda, mesaj, durum yönetimi
- 🔔 Okunmamış mesaj sayacı ve atama akışı

### XML Entegrasyonları
- 🔄 Çoklu tedarikçiden ürün ve stok çekme
- 🧩 XML mapping arayüzü
- 🗒️ Import logları, hata takibi ve raporlama
- ⏱️ Planlanmış görevlerle otomatik senkronizasyon

## 📋 Gereksinimler

- PHP ≥ 8.1
- Composer
- MySQL / MariaDB
- Node.js & npm / pnpm / yarn (Vite + Tailwind derlemeleri için)
- Redis (önerilen, cache & kuyruklar için opsiyonel)

## 🔧 Kurulum Adımları

1. **Kaynak kodu alın**
   ```bash
   git clone <repo-url> otoYedekParcaScript
   cd otoYedekParcaScript
   ```

2. **Bağımlılıkları yükleyin**
   ```bash
   composer install
   npm install
   ```

3. **Ortam dosyasını hazırlayın**
   ```bash
   cp .env.example .env    # Windows için: copy .env.example .env
   php artisan key:generate
   ```

4. **.env yapılandırması**
   Minimum gerekli değişkenler:
   ```
   APP_NAME="Yedek Parça"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=yedekparca_db
   DB_USERNAME=root
   DB_PASSWORD=

   DEFAULT_TENANT=   # (opsiyonel) varsayılan tenant slug/subdomain
   TENANT_AUTO_FALLBACK=true
   TENANT_FALLBACK_FIRST_ACTIVE=true
   ```
   > İsteğe bağlı olarak cache, kuyruk, mail ve SMS yapılandırmalarını ekleyin.

5. **Veritabanını hazırlayın**
   ```bash
   php artisan migrate
   php artisan storage:link
   ```

6. **Ön yüz varlıklarını derleyin**
   ```bash
   npm run dev        # Geliştirme
   npm run build      # Üretim
   ```

7. **Önbellekleri temizleyin (gerekli durumlarda)**
   ```bash
   php artisan optimize:clear
   ```

## 🧑‍💼 İlk Kullanıcı ve Tenant Oluşturma

1. **Süper admin hesabı oluşturun**
   ```bash
   php artisan tinker
   ```
   ```php
   $user = new App\Models\User();
   $user->name = 'Super Admin';
   $user->email = 'superadmin@example.com';
   $user->password = bcrypt('password');
   $user->user_type = 'admin';
   $user->tenant_id = null; // null => tüm tenantlara erişimi olan süper admin
   $user->save();
   ```

2. **Super Admin paneline giriş yapın**
   - URL: `http://127.0.0.1:8000/super-admin/dashboard`
   - Buradan yeni tenant (mağaza) oluşturabilir, alt alan adı / alan adını tanımlayabilirsiniz.

3. **Tenant yöneticisi ekleyin**
   - Admin panelinden kullanıcı oluştururken `tenant_id` otomatik atanır.
   - Tenant limitleri (ürün, kullanıcı sayısı vb.) Tenant modelindeki plan ayarlarına göre kontrol edilir.

## 🏠 Ana Alan Adı (Aggregator) Davranışı

- `http://127.0.0.1:8000/` adresinde tüm aktif tenantların ürünleri listelenir.
- Tenant seçimi yapılmadığında sistem marka kartları, popüler kategoriler ve son eklenen ürünleri çoklu tenant üzerinden gösterir.
- Her ürün/kategori kartı ilgili tenant'a yönlendiren bağlantılar içerir (`?tenant=slug` veya alt alan adı).

## 📦 XML Komutları

- Belirli tedarikçiyi içe aktar:  
  `php artisan xml:import {supplier_id}`
- Tüm tedarikçiler:  
  `php artisan xml:import`
- Örnek cron (6 saatte bir):
  ```
  * */6 * * * cd /path-to-project && php artisan xml:import >> /dev/null 2>&1
  ```

## 📂 Klasör Yapısı (Özet)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/           # Tenant aware admin panelleri
│   │   ├── Frontend/        # Çoklu tenant destekli frontend
│   │   └── SuperAdmin/      # Tenant yönetimi
│   ├── Middleware/
│   │   ├── TenantMiddleware.php
│   │   └── SetLocale.php
│   └── Kernel.php
├── Models/
│   ├── Tenant.php
│   ├── Product.php (tenant scope)
│   └── ...
├── Scopes/
│   └── TenantScope.php
└── Services/
    └── TenantService.php

resources/views/
├── layouts/app.blade.php       # Dinamik marka kimliği
├── frontend/home.blade.php     # Aggregator + tenant landing
└── admin/...                   # Yönetim arayüzleri
```

## 🔄 Route Özeti

Tüm rotalar: `php artisan route:list`

| Bölüm                | Örnek Route                                   |
|----------------------|-----------------------------------------------|
| Frontend             | `GET /`, `GET /urunler`, `GET /kampanyalar`  |
| Hesap                | `GET /hesabim`, `POST /hesabim/adresler`     |
| Sepet & Ödeme        | `POST /sepet/ekle`, `POST /odeme/adim-1`     |
| Canlı Destek         | `GET /chat`, `POST /chat/{room}/mesaj`       |
| Admin Panel          | `GET /admin/products`, `GET /admin/orders`   |
| Super Admin          | `GET /super-admin/tenants`, `POST /super-admin/tenants` |

## 🔒 Güvenlik & Yetkilendirme

- CSRF, XSS ve SQL Injection korumaları (Laravel varsayılanları)
- Tenant bazlı global scope ile veri izolasyonu
- `TenantMiddleware` ile alt alan adına göre kimliklendirme
- Super admin için tenant bypass ve yönetim paneli
- Şifreler bcrypt ile hash’lenir

## 🧰 Yararlı Artisan Komutları

```bash
php artisan optimize:clear   # Önbellekleri temizler
php artisan queue:work       # Kuyrukları dinler
```

## 📝 Notlar

- Ödeme entegrasyonları örnek amaçlıdır, canlı sistemde PCI uyumu göz önünde bulundurun.
- Alt alan adı yönlendirmeleri için local geliştirmede hosts dosyasına kayıt ekleyebilirsiniz (`tenant1.local.test` vb.).
- Aggregator ana sayfasında görüntülenen ürün ve kategoriler cache servisleri üzerinden yönetilir.

## 📄 Lisans

Bu proje özel/kapatılmış lisans altındadır. Yalnızca yetkili ekipler kullanabilir.

---

**Son Güncelleme:** 2025-11-10
