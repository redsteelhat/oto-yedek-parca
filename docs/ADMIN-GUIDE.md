# Admin Panel Kullanım Kılavuzu

## Giriş

Admin paneline giriş yapmak için `/admin` URL'ine gidin veya ana sayfadan "Admin" linkine tıklayın.

**Varsayılan Admin Giriş Bilgileri (Seeder'dan):**
- **Email:** `admin@example.com`
- **Şifre:** `password`

> ⚠️ **Güvenlik Uyarısı:** İlk kurulumdan sonra mutlaka admin şifresini değiştirin!

---

## Dashboard

Dashboard sayfası (`/admin/dashboard`) ana kontrol paneli ve istatistikleri gösterir:

### İstatistikler
- **Toplam Siparişler:** Sistemdeki toplam sipariş sayısı
- **Toplam Ürünler:** Sistemdeki toplam ürün sayısı
- **Toplam Müşteriler:** Kayıtlı müşteri sayısı
- **Toplam Gelir:** Toplam gelir tutarı

### Grafikler
- **Satış Grafiği:** Günlük/haftalık/aylık satış grafiği
- **Gelir Grafiği:** Günlük/haftalık/aylık gelir grafiği
- **En Çok Satan Ürünler:** En çok satan ürünler listesi

### Uyarılar
- **Düşük Stok Uyarıları:** Minimum stok seviyesinin altına düşen ürünler
- **Tükendi Ürünler:** Stokta olmayan ürünler
- **Son XML Import Durumları:** Son XML import işlemlerinin durumu

### Filtreler
- **Tarih Aralığı:** Grafikler için tarih aralığı seçimi (günlük, haftalık, aylık)

---

## Ürün Yönetimi

### Ürün Listesi

`/admin/products` sayfasından tüm ürünleri görüntüleyebilir ve yönetebilirsiniz.

**Özellikler:**
- Ürün arama (SKU, isim, OEM kodu)
- Durum filtreleme (aktif, pasif, taslak)
- Kategori filtreleme
- Sayfalama (20 ürün/sayfa)

### Ürün Ekleme

1. "Yeni Ürün Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **SKU:** Ürün stok kodu (zorunlu, benzersiz)
   - **OEM Kodu:** Orijinal ekipman kodu
   - **Ürün Adı:** Ürün adı (zorunlu)
   - **Kategori:** Ürün kategorisi
   - **Fiyat:** Satış fiyatı (zorunlu)
   - **İndirimli Fiyat:** İndirimli fiyat (opsiyonel)
   - **Stok:** Mevcut stok miktarı
   - **Minimum Stok Seviyesi:** Stok uyarısı için minimum seviye
   - **KDV Oranı:** KDV oranı (varsayılan: %20)
   - **Durum:** Aktif, Pasif veya Taslak
   - **Öne Çıkan:** Ana sayfada gösterilsin mi?
   - **Açıklama:** Ürün açıklaması
   - **Kısa Açıklama:** Ürün kısa açıklaması
   - **Üretici:** Üretici adı
   - **Parça Tipi:** OEM veya Aftermarket
   - **Araç Uyumluluğu:** Hangi araçlara uyumlu (çoklu seçim)
   - **Görseller:** Ürün görselleri (drag & drop ile yükleme)
3. "Kaydet" butonuna tıklayın

### Ürün Düzenleme

1. Ürün listesinden düzenlemek istediğiniz ürünün yanındaki "Düzenle" butonuna tıklayın
2. Bilgileri güncelleyin
3. "Güncelle" butonuna tıklayın

### Ürün Görselleri

- **Görsel Yükleme:** Drag & drop ile görsel yükleyebilirsiniz
- **Görsel Sıralama:** Görselleri sürükleyip bırakarak sıralayabilirsiniz
- **Birincil Görsel:** İlk görsel otomatik olarak birincil görsel olarak işaretlenir
- **Görsel Silme:** Her görselin yanındaki "Sil" butonuna tıklayarak silebilirsiniz

### Toplu İşlemler

1. Ürün listesinde işlem yapmak istediğiniz ürünleri seçin (checkbox)
2. Üst kısımdaki "Toplu İşlem" dropdown'ından işlemi seçin:
   - **Aktif Yap:** Seçili ürünleri aktif yapar
   - **Pasif Yap:** Seçili ürünleri pasif yapar
   - **Sil:** Seçili ürünleri siler
3. "Uygula" butonuna tıklayın

### Ürün İçe/Dışa Aktarma

**CSV İçe Aktarma:**
1. "İçe Aktar (CSV)" butonuna tıklayın
2. CSV dosyasını seçin
3. "Yükle" butonuna tıklayın

**CSV Formatı:**
```
SKU,OEM Kodu,Ürün Adı,Kategori,Tedarikçi,Fiyat,İndirimli Fiyat,Stok,Minimum Stok,KDV Oranı,Durum,Açıklama
MYF-001,OE-12345,Motor Yağ Filtresi,Filtreler,Örnek Tedarikçi,150.00,120.00,50,10,20,active,Yüksek kaliteli motor yağ filtresi
```

**CSV Dışa Aktarma:**
1. "Dışa Aktar (CSV)" butonuna tıklayın
2. CSV dosyası otomatik olarak indirilecektir

### Ürün Kopyalama

1. Ürün listesinden kopyalamak istediğiniz ürünün yanındaki "Kopyala" butonuna tıklayın
2. Yeni ürün oluşturulur (SKU değiştirilir)

---

## Sipariş Yönetimi

### Sipariş Listesi

`/admin/orders` sayfasından tüm siparişleri görüntüleyebilirsiniz.

**Filtreler:**
- Sipariş durumu (Beklemede, İşleniyor, Kargoya Verildi, Teslim Edildi, İptal, İade)
- Ödeme durumu (Beklemede, Ödendi, İade Edildi)
- Arama (Sipariş numarası, müşteri adı, email)

### Sipariş Detayı

1. Sipariş listesinden detayını görmek istediğiniz siparişe tıklayın
2. Sipariş detay sayfasında:
   - **Müşteri Bilgileri:** Müşteri adı, email, telefon
   - **Sipariş Bilgileri:** Sipariş numarası, tarih, durum
   - **Adres Bilgileri:** Teslimat ve fatura adresi
   - **Sipariş Kalemleri:** Ürünler, miktarlar, fiyatlar
   - **Ödeme Bilgileri:** Ödeme yöntemi, durum, tutar
   - **Kargo Bilgileri:** Kargo firması, takip numarası

### Sipariş Durumu Değiştirme

1. Sipariş detay sayfasında "Durum" dropdown'ından yeni durumu seçin
2. "Güncelle" butonuna tıklayın

**Durumlar:**
- **Beklemede:** Sipariş alındı, henüz işlenmedi
- **İşleniyor:** Sipariş hazırlanıyor
- **Kargoya Verildi:** Sipariş kargoya teslim edildi
- **Teslim Edildi:** Sipariş müşteriye teslim edildi
- **İptal:** Sipariş iptal edildi
- **İade:** Sipariş iade edildi

### Kargo Takip Numarası Ekleme

1. Sipariş detay sayfasında "Kargo Takip Numarası" bölümüne takip numarasını girin
2. "Kargo Firması" seçin
3. "Kaydet" butonuna tıklayın

**Kargo Firmaları:**
- Yurtiçi Kargo
- Aras Kargo
- MNG Kargo
- Sürat Kargo

### Otomatik Kargo Etiketi Oluşturma

1. Sipariş detay sayfasında "Kargo Etiketi Oluştur" butonuna tıklayın
2. Sistem otomatik olarak kargo firması API'sini çağırır ve takip numarası alır

### Kargo Takip

1. Sipariş detay sayfasında "Kargo Durumu Sorgula" butonuna tıklayın
2. Sistem kargo firması API'sinden güncel durumu çeker

### Sipariş Notları

1. Sipariş detay sayfasında "Sipariş Notları" bölümüne not ekleyin
2. "Kaydet" butonuna tıklayın

### Sipariş İptal/İade

**İptal:**
1. Sipariş detay sayfasında "İptal Et" butonuna tıklayın
2. Sipariş iptal edilir ve stok geri yüklenir

**İade:**
1. Sipariş detay sayfasında "İade Et" butonuna tıklayın
2. Sipariş iade olarak işaretlenir ve stok geri yüklenir

### Fatura Oluşturma

1. Sipariş detay sayfasında "Fatura Yazdır" butonuna tıklayın
2. Fatura HTML formatında açılır (PDF için dompdf paketi eklenebilir)

---

## Müşteri Yönetimi

### Müşteri Listesi

`/admin/customers` sayfasından tüm müşterileri görüntüleyebilirsiniz.

**Filtreler:**
- Müşteri tipi (Normal, Bayi)
- Arama (Ad, email, telefon)

### Müşteri Detayı

1. Müşteri listesinden detayını görmek istediğiniz müşteriye tıklayın
2. Müşteri detay sayfasında:
   - **Kişisel Bilgiler:** Ad, email, telefon, şirket adı, vergi numarası
   - **Sipariş İstatistikleri:** Toplam sipariş, toplam harcama
   - **Adresler:** Müşteri adresleri
   - **Sipariş Geçmişi:** Müşterinin tüm siparişleri
   - **Notlar:** Admin notları

### Müşteri Düzenleme

1. Müşteri detay sayfasında "Düzenle" butonuna tıklayın
2. Bilgileri güncelleyin:
   - Ad, email, telefon
   - Şirket adı, vergi numarası
   - Müşteri tipi (Normal, Bayi)
   - Şifre (değiştirmek için)
   - Notlar
3. "Güncelle" butonuna tıklayın

---

## Kategori Yönetimi

### Kategori Listesi

`/admin/categories` sayfasından tüm kategorileri görüntüleyebilirsiniz.

**Görünümler:**
- **Tree View:** Hiyerarşik ağaç görünümü
- **List View:** Düz liste görünümü

### Kategori Ekleme

1. "Yeni Kategori Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Kategori Adı:** Kategori adı (zorunlu)
   - **Üst Kategori:** Ana kategori veya alt kategori (opsiyonel)
   - **Açıklama:** Kategori açıklaması
   - **Görsel:** Kategori görseli
   - **Sıra:** Sıralama numarası
   - **SEO:** Meta başlık ve açıklama
3. "Kaydet" butonuna tıklayın

### Kategori Sıralama

1. Tree view'da kategorileri sürükleyip bırakarak sıralayabilirsiniz
2. Değişiklikler otomatik olarak kaydedilir

---

## Araç Veritabanı Yönetimi

### Marka Yönetimi

`/admin/car-brands` sayfasından araç markalarını yönetebilirsiniz.

**Özellikler:**
- Marka ekleme, düzenleme, silme
- CSV içe/dışa aktarma
- Marka listesi ve model sayıları

### Model Yönetimi

`/admin/car-models` sayfasından araç modellerini yönetebilirsiniz.

**Filtreler:**
- Marka bazlı filtreleme
- Model arama

### Yıl Yönetimi

`/admin/car-years` sayfasından araç yıllarını yönetebilirsiniz.

**Filtreler:**
- Model bazlı filtreleme
- Yıl, motor tipi, motor kodu arama

**Toplu İçe Aktarma:**
- CSV formatında marka, model, yıl, motor tipi, motor kodu bilgilerini toplu olarak içe aktarabilirsiniz

---

## Kupon Yönetimi

### Kupon Listesi

`/admin/coupons` sayfasından tüm kuponları görüntüleyebilirsiniz.

### Kupon Ekleme

1. "Yeni Kupon Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Kupon Kodu:** Kupon kodu (zorunlu, benzersiz)
   - **Kupon Adı:** Kupon adı
   - **Açıklama:** Kupon açıklaması
   - **İndirim Tipi:** Yüzde veya Sabit Tutar
   - **İndirim Değeri:** İndirim miktarı
   - **Minimum Alışveriş Tutarı:** Kuponun uygulanabilmesi için minimum tutar
   - **Maksimum İndirim Tutarı:** Maksimum indirim limiti (yüzde indirimler için)
   - **Kullanım Limiti:** Toplam kullanım limiti
   - **Kullanıcı Başına Limit:** Her kullanıcı için kullanım limiti
   - **Başlangıç Tarihi:** Kuponun geçerli olmaya başladığı tarih
   - **Bitiş Tarihi:** Kuponun geçerliliğinin bittiği tarih
   - **Uygulanabilir Kategoriler:** Hangi kategorilere uygulanabilir (opsiyonel)
   - **Uygulanabilir Ürünler:** Hangi ürünlere uygulanabilir (opsiyonel)
3. "Kaydet" butonuna tıklayın

### Kupon Kullanım Raporları

1. Kupon listesinden raporunu görmek istediğiniz kuponun yanındaki "Raporlar" butonuna tıklayın
2. Kupon kullanım istatistiklerini görüntüleyebilirsiniz:
   - Toplam kullanım sayısı
   - Kalan kullanım hakkı
   - Toplam indirim tutarı
   - Kullanıcı bazlı kullanım detayları
   - Tarih aralığı filtreleme

---

## Kampanya Yönetimi

### Kampanya Listesi

`/admin/campaigns` sayfasından tüm kampanyaları görüntüleyebilirsiniz.

### Kampanya Ekleme

1. "Yeni Kampanya Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Kampanya Adı:** Kampanya adı (zorunlu)
   - **Slug:** URL-friendly ad (otomatik oluşturulur)
   - **Açıklama:** Kampanya açıklaması
   - **Görsel:** Kampanya görseli
   - **Kampanya Tipi:** Ürün bazlı, Kategori bazlı, Genel
   - **İndirim Tipi:** Yüzde veya Sabit Tutar
   - **İndirim Değeri:** İndirim miktarı
   - **Minimum Alışveriş Tutarı:** Kampanyanın uygulanabilmesi için minimum tutar
   - **Başlangıç Tarihi:** Kampanyanın başladığı tarih
   - **Bitiş Tarihi:** Kampanyanın bittiği tarih
   - **Uygulanabilir Ürünler:** Hangi ürünlere uygulanabilir (opsiyonel)
   - **Uygulanabilir Kategoriler:** Hangi kategorilere uygulanabilir (opsiyonel)
3. "Kaydet" butonuna tıklayın

---

## Tedarikçi Yönetimi

### Tedarikçi Listesi

`/admin/suppliers` sayfasından tüm tedarikçileri görüntüleyebilirsiniz.

### Tedarikçi Ekleme

1. "Yeni Tedarikçi Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Tedarikçi Adı:** Tedarikçi adı (zorunlu)
   - **Kod:** Tedarikçi kodu (zorunlu, benzersiz)
   - **XML URL:** XML import URL'i
   - **XML Kullanıcı Adı:** XML erişimi için kullanıcı adı
   - **XML Şifre:** XML erişimi için şifre
   - **XML Tipi:** XML formatı (standard, custom)
   - **Güncelleme Sıklığı:** Otomatik güncelleme sıklığı (saatlik, günlük, haftalık)
   - **Notlar:** Tedarikçi notları
3. "Kaydet" butonuna tıklayın

### XML Import

1. Tedarikçi detay sayfasında "XML Import" butonuna tıklayın
2. XML import işlemi başlatılır
3. İlerleme durumunu takip edebilirsiniz

---

## Kargo Firması Yönetimi

### Kargo Firması Listesi

`/admin/shipping-companies` sayfasından tüm kargo firmalarını görüntüleyebilirsiniz.

### Kargo Firması Ekleme

1. "Yeni Kargo Firması Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Firma Adı:** Kargo firması adı (zorunlu)
   - **Kod:** Kargo firması kodu (benzersiz)
   - **API Tipi:** API tipi (Yurtiçi, Aras, MNG, Sürat)
   - **API Bilgileri:** API URL, Key, Secret, Username, Password
   - **Fiyat Ayarları:** Temel fiyat, kilo başı fiyat, desi başı fiyat
   - **Ücretsiz Kargo Limiti:** Ücretsiz kargo için minimum tutar
   - **Tahmini Teslimat Süresi:** Gün cinsinden tahmini teslimat süresi
3. "Kaydet" butonuna tıklayın

---

## Havale/EFT Onayları

### Onay Bekleyen Havaleler

`/admin/bank-transfers` sayfasından onay bekleyen havale/EFT işlemlerini görüntüleyebilirsiniz.

### Havale Onaylama

1. Havale listesinden onaylamak istediğiniz havale işlemine tıklayın
2. Dekont görselini kontrol edin
3. "Onayla" butonuna tıklayın
4. Sipariş durumu "İşleniyor" olarak güncellenir

### Havale Reddetme

1. Havale listesinden reddetmek istediğiniz havale işlemine tıklayın
2. "Reddet" butonuna tıklayın
3. Sipariş durumu "İptal" olarak güncellenir

---

## CMS Sayfa Yönetimi

### Sayfa Listesi

`/admin/pages` sayfasından tüm CMS sayfalarını görüntüleyebilirsiniz.

### Sayfa Ekleme

1. "Yeni Sayfa Ekle" butonuna tıklayın
2. Gerekli bilgileri doldurun:
   - **Başlık:** Sayfa başlığı (zorunlu)
   - **Slug:** URL-friendly ad (otomatik oluşturulur)
   - **İçerik:** Sayfa içeriği (Quill Editor ile)
   - **SEO:** Meta başlık ve açıklama
3. "Kaydet" butonuna tıklayın

**Sayfa Görüntüleme:**
- Frontend'de `/sayfa/{slug}` URL'inden erişilebilir

---

## Ayarlar

### Genel Ayarlar

`/admin/settings` sayfasından tüm sistem ayarlarını yönetebilirsiniz.

**Ayarlar:**
- **Site Adı:** Site adı
- **Site Açıklaması:** Site açıklaması
- **Logo:** Site logosu (dosya yükleme)
- **Favicon:** Site favicon'u (dosya yükleme)
- **İletişim Bilgileri:** Email, telefon, adres

### Ödeme Ayarları

**İyzico:**
- API Key
- Secret Key
- Base URL

**PayTR:**
- Merchant ID
- Merchant Key
- Merchant Salt

**Havale/EFT:**
- Banka hesap bilgileri (JSON formatında)

**Kapıda Ödeme:**
- Aktif/Pasif
- Kapıda ödeme ücreti

### Kargo Ayarları

- Varsayılan kargo firması
- Ücretsiz kargo limiti

### E-posta Ayarları

- SMTP Host
- SMTP Port
- SMTP Kullanıcı Adı
- SMTP Şifre
- Şifreleme (TLS/SSL)
- Gönderen E-posta
- Gönderen Adı

### SEO Ayarları

- Meta başlık
- Meta açıklama
- Meta keywords
- Google Analytics ID
- Google Tag Manager ID
- Facebook Pixel ID

### Sosyal Medya Linkleri

- Facebook URL
- Instagram URL
- Twitter/X URL
- YouTube URL
- LinkedIn URL
- WhatsApp Numarası

### SMS Ayarları

- SMS Bildirimleri Aktif/Pasif
- SMS Gateway (Netgsm, İleti Merkezi)
- Kullanıcı Adı / Usercode
- Şifre / Password
- API Key (İleti Merkezi için)
- Gönderen Adı / Başlık
- API URL (Özel gateway için)

---

## İpuçları

1. **Stok Yönetimi:** Düşük stok uyarılarını dashboard'dan takip edin
2. **Sipariş Takibi:** Sipariş durumlarını düzenli olarak güncelleyin
3. **Kampanyalar:** Kampanyaları önceden planlayın ve tarihlerini doğru ayarlayın
4. **XML Import:** XML import'larını düzenli olarak kontrol edin
5. **Yedekleme:** Düzenli olarak veritabanı yedeği alın

---

**Son Güncelleme:** 2025-11-05

