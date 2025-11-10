# Multi-Tenant Sistem Yapılacaklar Listesi

## 📋 GENEL BAKIŞ

Bu doküman, mevcut e-ticaret sistemini multi-tenant (çoklu kiracı) yapıya dönüştürmek için gereken tüm adımları içermektedir.

## 🎯 HEDEF YAPILANMA

- **Tenant Identification**: Subdomain-based (örn: tenant1.site.com, tenant2.site.com)
- **Database Strategy**: Shared database, tenant_id ile izolasyon
- **Super Admin**: Tüm tenant'ları yönetebilen merkezi admin paneli
- **Tenant Admin**: Her tenant'ın kendi admin paneli
- **Data Isolation**: Her tenant'ın verileri tamamen izole

---

## 🔴 ÖNCELİKLİ - TEMEL YAPILANMA

### 1. DATABASE YAPISI

#### 1.1 Tenants Tablosu
- [ ] `tenants` tablosu oluşturulması
  - `id` (primary key)
  - `name` (tenant adı)
  - `slug` (unique, subdomain için)
  - `domain` (custom domain - nullable)
  - `subdomain` (unique, zorunlu)
  - `email` (tenant iletişim emaili)
  - `phone` (tenant telefon)
  - `logo` (tenant logosu)
  - `favicon` (tenant favicon)
  - `primary_color` (ana renk)
  - `secondary_color` (ikincil renk)
  - `status` (active, suspended, inactive)
  - `subscription_plan` (free, basic, premium, enterprise)
  - `subscription_expires_at` (abonelik bitiş tarihi)
  - `max_products` (maksimum ürün sayısı - nullable)
  - `max_users` (maksimum kullanıcı sayısı - nullable)
  - `settings` (JSON - tenant-specific ayarlar)
  - `created_at`, `updated_at`
  - `deleted_at` (soft delete)

#### 1.2 Mevcut Tablolara tenant_id Ekleme
- [ ] `users` tablosuna `tenant_id` (nullable - super admin için)
- [ ] `categories` tablosuna `tenant_id`
- [ ] `products` tablosuna `tenant_id`
- [ ] `orders` tablosuna `tenant_id`
- [ ] `addresses` tablosuna `tenant_id`
- [ ] `coupons` tablosuna `tenant_id`
- [ ] `campaigns` tablosuna `tenant_id`
- [ ] `suppliers` tablosuna `tenant_id`
- [ ] `shipping_companies` tablosuna `tenant_id`
- [ ] `pages` tablosuna `tenant_id`
- [ ] `settings` tablosuna `tenant_id`
- [ ] `car_brands` tablosuna `tenant_id` (veya global olarak kalabilir)
- [ ] `car_models` tablosuna `tenant_id` (veya global olarak kalabilir)
- [ ] `car_years` tablosuna `tenant_id` (veya global olarak kalabilir)
- [ ] `chat_rooms` tablosuna `tenant_id`
- [ ] `chat_messages` tablosuna `tenant_id`
- [ ] `product_reviews` tablosuna `tenant_id`
- [ ] `wishlist` tablosuna `tenant_id`
- [ ] `xml_import_logs` tablosuna `tenant_id`
- [ ] `supplier_xml_mappings` tablosuna `tenant_id`

#### 1.3 Index'ler
- [ ] Tüm `tenant_id` kolonlarına index eklenmesi
- [ ] Composite index'ler (tenant_id + diğer sık kullanılan kolonlar)

---

### 2. MODELS

#### 2.1 Tenant Model
- [ ] `Tenant` model oluşturulması
  - Relationships (users, products, orders, vb.)
  - Scopes (active, suspended, vb.)
  - Helper methods (isActive, canCreateProduct, vb.)

#### 2.2 Mevcut Modellere Tenant Scope Ekleme
- [ ] `User` model - tenant relationship
- [ ] `Category` model - tenant relationship + global scope
- [ ] `Product` model - tenant relationship + global scope
- [ ] `Order` model - tenant relationship + global scope
- [ ] `Address` model - tenant relationship + global scope
- [ ] `Coupon` model - tenant relationship + global scope
- [ ] `Campaign` model - tenant relationship + global scope
- [ ] `Supplier` model - tenant relationship + global scope
- [ ] `ShippingCompany` model - tenant relationship + global scope
- [ ] `Page` model - tenant relationship + global scope
- [ ] `Setting` model - tenant relationship + global scope
- [ ] `ChatRoom` model - tenant relationship + global scope
- [ ] `ChatMessage` model - tenant relationship + global scope
- [ ] `ProductReview` model - tenant relationship + global scope
- [ ] `Wishlist` model - tenant relationship + global scope
- [ ] `XmlImportLog` model - tenant relationship + global scope
- [ ] `SupplierXmlMapping` model - tenant relationship + global scope

#### 2.3 Global Scope
- [ ] `TenantScope` oluşturulması
  - Otomatik olarak tenant_id filtresi uygulama
  - Super admin için scope bypass

---

### 3. MIDDLEWARE & TENANT IDENTIFICATION

#### 3.1 Tenant Middleware
- [ ] `TenantMiddleware` oluşturulması
  - Subdomain'den tenant tespiti
  - Custom domain desteği
  - Tenant'ı session'a kaydetme
  - Tenant aktiflik kontrolü
  - Abonelik kontrolü

#### 3.2 Tenant Service
- [ ] `TenantService` oluşturulması
  - `getCurrentTenant()` - mevcut tenant'ı getirme
  - `setTenant($tenant)` - tenant ayarlama
  - `isSuperAdmin()` - super admin kontrolü
  - `switchTenant($tenantId)` - tenant değiştirme (super admin için)

---

### 4. ROUTING

#### 4.1 Route Yapısı
- [ ] Subdomain routing yapılandırması
- [ ] Super admin routes (merkezi domain)
- [ ] Tenant admin routes (subdomain)
- [ ] Frontend routes (subdomain)
- [ ] Route middleware grupları

#### 4.2 Route Service Provider
- [ ] `RouteServiceProvider` güncellemesi
  - Subdomain detection
  - Tenant-based route loading

---

### 5. CONTROLLERS

#### 5.1 Super Admin Controllers
- [ ] `SuperAdmin\TenantController` - tenant CRUD
- [ ] `SuperAdmin\DashboardController` - tüm tenant'ların özeti
- [ ] `SuperAdmin\UserController` - super admin kullanıcıları
- [ ] Mevcut admin controller'ları super admin için uyarlama

#### 5.2 Tenant Admin Controllers
- [ ] Mevcut admin controller'ları tenant scope ile güncelleme
- [ ] Tenant-specific data filtreleme
- [ ] Tenant limit kontrolleri (max_products, max_users)

#### 5.3 Frontend Controllers
- [ ] Tüm frontend controller'ları tenant scope ile güncelleme
- [ ] Tenant-specific branding

---

### 6. VIEWS & BRANDING

#### 6.1 Layout Güncellemeleri
- [ ] `layouts/app.blade.php` - tenant logo, renkler
- [ ] `layouts/admin.blade.php` - tenant branding
- [ ] Tenant-specific CSS variables

#### 6.2 Super Admin Views
- [ ] `super-admin/tenants/index.blade.php` - tenant listesi
- [ ] `super-admin/tenants/create.blade.php` - yeni tenant
- [ ] `super-admin/tenants/edit.blade.php` - tenant düzenleme
- [ ] `super-admin/tenants/show.blade.php` - tenant detay
- [ ] `super-admin/dashboard.blade.php` - super admin dashboard

#### 6.3 Tenant Settings
- [ ] Tenant ayarlar sayfası (logo, renkler, domain)
- [ ] Tenant subscription yönetimi

---

### 7. AUTHENTICATION & AUTHORIZATION

#### 7.1 User Types
- [ ] Super Admin (tenant_id = null)
- [ ] Tenant Admin (tenant_id != null, user_type = 'admin')
- [ ] Tenant User (tenant_id != null, user_type = 'customer'/'dealer')

#### 7.2 Middleware Güncellemeleri
- [ ] `AdminMiddleware` - tenant admin kontrolü
- [ ] `SuperAdminMiddleware` - super admin kontrolü
- [ ] Login/Register - tenant'a göre kullanıcı oluşturma

---

### 8. SETTINGS & CONFIGURATION

#### 8.1 Settings Tablosu
- [ ] `settings` tablosuna `tenant_id` ekleme
- [ ] Global settings (tenant_id = null)
- [ ] Tenant-specific settings

#### 8.2 Setting Model
- [ ] `Setting::getValue()` - tenant-aware
- [ ] `Setting::set()` - tenant-aware

---

### 9. FILE STORAGE

#### 9.1 Storage Yapısı
- [ ] Tenant-specific storage paths
  - `storage/app/tenants/{tenant_id}/products/`
  - `storage/app/tenants/{tenant_id}/logos/`
  - `storage/app/tenants/{tenant_id}/documents/`
- [ ] `FileUploadService` güncellemesi

#### 9.2 Public Storage
- [ ] Symbolic link yapısı
- [ ] Tenant-specific public URLs

---

### 10. CACHE & SESSION

#### 10.1 Cache
- [ ] Tenant-specific cache keys
- [ ] `CacheService` güncellemesi
- [ ] Cache prefix (tenant_id)

#### 10.2 Session
- [ ] Tenant ID session'da saklama
- [ ] Tenant-specific session data

---

### 11. QUEUE & JOBS

#### 11.1 Queue
- [ ] Tenant-aware queue jobs
- [ ] Queue connection tenant ID ile

#### 11.2 Jobs
- [ ] XML import jobs - tenant scope
- [ ] Email jobs - tenant branding
- [ ] SMS jobs - tenant settings

---

### 12. EMAIL & NOTIFICATIONS

#### 12.1 Email Templates
- [ ] Tenant-specific email templates
- [ ] Tenant branding (logo, renkler)
- [ ] Email sender (tenant email)

#### 12.2 Notifications
- [ ] Tenant-aware notifications
- [ ] SMS - tenant settings

---

### 13. XML IMPORT

#### 13.1 XML Import
- [ ] Supplier'lar tenant-specific
- [ ] Import logs tenant-specific
- [ ] XML mappings tenant-specific

---

### 14. MIGRATION STRATEGY

#### 14.1 Mevcut Veri
- [ ] Mevcut verileri default tenant'a taşıma stratejisi
- [ ] Migration script'i
- [ ] Veri yedekleme

#### 14.2 Migration Dosyaları
- [ ] `create_tenants_table.php`
- [ ] `add_tenant_id_to_*_tables.php` (her tablo için)
- [ ] `create_default_tenant.php` (seeder)
- [ ] `migrate_existing_data_to_tenant.php` (seeder)

---

### 15. SEEDERS

#### 15.1 Tenant Seeders
- [ ] `TenantSeeder` - default tenant oluşturma
- [ ] Mevcut seeders'ı tenant-aware yapma

---

### 16. CONFIGURATION

#### 16.1 Config Dosyaları
- [ ] `config/tenant.php` - tenant yapılandırması
- [ ] `config/app.php` - tenant-aware ayarlar

#### 16.2 Environment
- [ ] `.env` - tenant domain ayarları
- [ ] Super admin domain

---

### 17. TESTING

#### 17.1 Test Senaryoları
- [ ] Tenant izolasyon testleri
- [ ] Super admin testleri
- [ ] Tenant admin testleri
- [ ] Frontend tenant testleri
- [ ] Data leakage testleri

---

### 18. DOCUMENTATION

#### 18.1 Dokümantasyon
- [ ] Multi-tenant architecture dokümantasyonu
- [ ] Tenant yönetim kılavuzu
- [ ] Super admin kılavuzu
- [ ] Migration kılavuzu

---

## ⚠️ DİKKAT EDİLMESİ GEREKENLER

### Güvenlik
- [ ] Tenant data leakage önleme
- [ ] SQL injection koruması (tenant_id injection)
- [ ] Cross-tenant access önleme
- [ ] File access izolasyonu

### Performans
- [ ] Database index optimizasyonu
- [ ] Query optimization (tenant_id her zaman WHERE'de)
- [ ] Cache strategy
- [ ] Eager loading (tenant scope ile)

### Ölçeklenebilirlik
- [ ] Tenant limit kontrolleri
- [ ] Resource usage monitoring
- [ ] Subscription plan limits

---

## 📊 TAHMİNİ İŞ YÜKÜ

- **Database & Models**: ~8-10 saat
- **Middleware & Routing**: ~4-6 saat
- **Controllers & Views**: ~10-12 saat
- **Settings & Configuration**: ~3-4 saat
- **File Storage**: ~2-3 saat
- **Cache & Queue**: ~2-3 saat
- **Email & Notifications**: ~2-3 saat
- **Migration & Seeding**: ~3-4 saat
- **Testing**: ~4-6 saat
- **Documentation**: ~2-3 saat

**Toplam Tahmini Süre**: ~40-55 saat

---

## 🚀 UYGULAMA SIRASI (ÖNERİLEN)

1. **Faz 1: Temel Yapı** (Database, Models, Middleware)
2. **Faz 2: Super Admin** (Tenant CRUD, Dashboard)
3. **Faz 3: Tenant Admin** (Mevcut admin paneli tenant-aware)
4. **Faz 4: Frontend** (Tenant branding, data isolation)
5. **Faz 5: Settings & Storage** (Tenant-specific ayarlar, dosyalar)
6. **Faz 6: Migration** (Mevcut veriyi taşıma)
7. **Faz 7: Testing & Documentation**

---

## ❓ KARAR VERİLMESİ GEREKENLER

1. **Car Database**: Global mi yoksa tenant-specific mi?
   - Öneri: Global (tüm tenant'lar aynı araç veritabanını kullanabilir)

2. **Super Admin Domain**: 
   - Öneri: `admin.site.com` veya `manage.site.com`

3. **Default Tenant**: 
   - Mevcut verileri hangi tenant'a taşıyalım?
   - Öneri: `default` veya `main` subdomain'i

4. **Subscription Plans**:
   - Hangi planlar olacak?
   - Limitler neler olacak?

5. **Tenant Suspension**:
   - Suspended tenant'ların verileri ne olacak?
   - Soft delete mi hard delete mi?

---

## 📝 NOTLAR

- Tüm değişiklikler backward compatible olmalı (mümkün olduğunca)
- Migration sırasında veri kaybı olmamalı
- Her adım test edilmeli
- Production'a geçmeden önce staging'de test edilmeli

