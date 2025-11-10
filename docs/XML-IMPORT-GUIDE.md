# XML Import Kılavuzu

## Genel Bakış

Bu sistem, tedarikçilerden XML formatında ürün verilerini otomatik olarak içe aktarmanıza olanak tanır.

**Özellikler:**
- Otomatik ürün import
- Kategori eşleştirme
- Görsel indirme
- Hata yönetimi ve loglama
- Zamanlanmış import'lar

---

## Tedarikçi Kurulumu

### 1. Tedarikçi Ekleme

1. Admin panelinde `/admin/suppliers` sayfasına gidin
2. "Yeni Tedarikçi Ekle" butonuna tıklayın
3. Aşağıdaki bilgileri doldurun:

**Temel Bilgiler:**
- **Tedarikçi Adı:** Tedarikçi adı (örn: "Örnek Tedarikçi")
- **Kod:** Tedarikçi kodu (benzersiz, örn: "SUP001")
- **Aktif:** Tedarikçi aktif mi?

**XML Ayarları:**
- **XML URL:** XML dosyasının tam URL'i (örn: `https://example.com/products.xml`)
- **XML Kullanıcı Adı:** XML erişimi için kullanıcı adı (gerekirse)
- **XML Şifre:** XML erişimi için şifre (gerekirse)
- **XML Tipi:** XML formatı (standard, custom)
- **Güncelleme Sıklığı:** Otomatik güncelleme sıklığı (saatlik, günlük, haftalık, manuel)

**Notlar:**
- Tedarikçi hakkında notlar

4. "Kaydet" butonuna tıklayın

---

## XML Mapping (Alan Eşleştirme)

### 1. XML Mapping Sayfasına Erişim

1. Tedarikçi detay sayfasında "XML Mapping" butonuna tıklayın
2. Veya `/admin/suppliers/{supplier}/xml-mapping` URL'ine gidin

### 2. Alan Eşleştirme

XML'deki alanları sistem alanlarına eşleştirmeniz gerekir:

**Standart Alanlar:**
- `SKU` → `sku`
- `OEM Code` → `oem_code`
- `Name` → `name`
- `Description` → `description`
- `Price` → `price`
- `Sale Price` → `sale_price`
- `Stock` → `stock`
- `Category` → `category` (kategori eşleştirme için)
- `Image` → `image` (görsel URL'i)

**Transform Rules (Dönüşüm Kuralları):**
- **None:** Değişiklik yapma
- **Uppercase:** Büyük harfe çevir
- **Lowercase:** Küçük harfe çevir
- **Slug:** URL-friendly formatına çevir
- **Number:** Sayıya çevir
- **Decimal:** Ondalıklı sayıya çevir

### 3. Mapping Ekleme

1. "Yeni Eşleştirme Ekle" butonuna tıklayın
2. Alan bilgilerini doldurun:
   - **XML Alan Adı:** XML'deki alan adı (örn: `product_code`)
   - **Sistem Alanı:** Sistemdeki alan adı (örn: `sku`)
   - **Dönüşüm Kuralı:** Transform rule (örn: `uppercase`)
   - **Zorunlu:** Bu alan zorunlu mu?
3. "Kaydet" butonuna tıklayın

### 4. Mapping Test Etme

1. "Test Et" butonuna tıklayın
2. Sistem XML'i okur ve mapping'i test eder
3. Test sonuçlarını görüntüleyebilirsiniz

---

## Manuel XML Import

### 1. Import Başlatma

1. Tedarikçi detay sayfasında "XML Import" butonuna tıklayın
2. Veya Artisan komutunu kullanın:

```bash
php artisan xml:import {supplier_code}
```

**Örnek:**
```bash
php artisan xml:import SUP001
```

### 2. Import İlerlemesini Takip Etme

- Import işlemi sırasında ilerleme durumu gösterilir
- Hata varsa loglarda görüntülenir

### 3. Import Sonuçları

Import tamamlandıktan sonra:
- **Toplam İşlenen:** Toplam işlenen ürün sayısı
- **Başarılı:** Başarıyla import edilen ürün sayısı
- **Güncellenen:** Güncellenen ürün sayısı
- **Başarısız:** Başarısız olan ürün sayısı
- **Hata Detayları:** Hata logları

---

## Otomatik (Zamanlanmış) Import

### 1. Cron Job Kurulumu

`crontab` dosyasına aşağıdaki satırı ekleyin:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 2. Scheduled Import Ayarları

Tedarikçi ayarlarında "Güncelleme Sıklığı" seçeneğini ayarlayın:
- **Saatlik:** Her saat başı import
- **Günlük:** Her gün belirli saatte import
- **Haftalık:** Haftada bir import
- **Manuel:** Sadece manuel import

### 3. Scheduled Import Komutu

Sistem otomatik olarak aktif tedarikçiler için scheduled import çalıştırır:

```bash
php artisan xml:import:scheduled
```

Bu komut Laravel'in scheduled task sistemi tarafından otomatik çalıştırılır.

---

## Kategori Eşleştirme

### 1. Otomatik Eşleştirme

Sistem, XML'deki kategori adlarını sistem kategorilerine otomatik olarak eşleştirmeye çalışır:
- Kategori adı tam eşleşirse → Otomatik eşleştir
- Kategori adı benzer ise → En yakın kategoriyi öner
- Eşleşme bulunamazsa → Ürün kategorisiz olarak import edilir

### 2. Manuel Eşleştirme

XML mapping'de kategori alanını manuel olarak eşleştirebilirsiniz.

---

## Görsel İndirme

### 1. Otomatik Görsel İndirme

Sistem, XML'deki görsel URL'lerinden görselleri otomatik olarak indirir:
- Görseller `storage/app/public/products/` klasörüne kaydedilir
- Her ürün için birincil görsel belirlenir
- Birden fazla görsel varsa tüm görseller import edilir

### 2. Görsel Formatı

Desteklenen formatlar:
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)

---

## Hata Yönetimi

### 1. Import Logları

Tüm import işlemleri loglanır:
- **Başarılı Import:** Yeşil durum
- **Kısmi Başarı:** Sarı durum (bazı ürünler başarısız)
- **Başarısız Import:** Kırmızı durum

### 2. Hata Detayları

Import loglarında hata detaylarını görebilirsiniz:
- Hangi ürünlerde hata oluştu
- Hata mesajları
- Hata sebepleri

### 3. Hata Türleri

**Yaygın Hatalar:**
- **XML Parse Hatası:** XML formatı geçersiz
- **Alan Eksikliği:** Zorunlu alan bulunamadı
- **Kategori Bulunamadı:** Kategori eşleştirilemedi
- **Görsel İndirme Hatası:** Görsel indirilemedi
- **SKU Çakışması:** Aynı SKU'ya sahip ürün zaten var

### 4. Retry Mekanizması

Başarısız import'lar otomatik olarak tekrar denenir:
- Scheduled import sırasında otomatik retry
- Manuel retry için import logundan "Yeniden Dene" butonuna tıklayın

---

## XML Format Örnekleri

### Standart Format

```xml
<?xml version="1.0" encoding="UTF-8"?>
<products>
    <product>
        <sku>MYF-001</sku>
        <oem_code>OE-12345</oem_code>
        <name>Motor Yağ Filtresi</name>
        <description>Yüksek kaliteli motor yağ filtresi</description>
        <price>150.00</price>
        <sale_price>120.00</sale_price>
        <stock>50</stock>
        <category>Filtreler</category>
        <image>https://example.com/images/product1.jpg</image>
    </product>
    <product>
        <sku>HF-001</sku>
        <oem_code>OE-12346</oem_code>
        <name>Hava Filtresi</name>
        <description>Standart hava filtresi</description>
        <price>80.00</price>
        <stock>100</stock>
        <category>Filtreler</category>
        <image>https://example.com/images/product2.jpg</image>
    </product>
</products>
```

### Özel Format

Özel XML formatları için custom mapping kullanabilirsiniz.

---

## İpuçları ve En İyi Uygulamalar

### 1. XML Mapping

- **Alan Adlarını Doğru Eşleştirin:** XML'deki alan adlarını sistem alanlarına doğru eşleştirin
- **Transform Rules Kullanın:** Gerekirse transform rules kullanarak veriyi dönüştürün
- **Test Edin:** Mapping'i kaydetmeden önce mutlaka test edin

### 2. Kategori Eşleştirme

- **Kategori Adlarını Standartlaştırın:** XML'deki kategori adlarını sistem kategorileriyle eşleştirilebilir hale getirin
- **Manuel Kontrol:** Otomatik eşleştirme sonrası manuel kontrol yapın

### 3. Görsel Yönetimi

- **Görsel URL'lerini Kontrol Edin:** XML'deki görsel URL'lerinin erişilebilir olduğundan emin olun
- **Görsel Boyutları:** Büyük görseller performansı etkileyebilir, optimize edin

### 4. Performans

- **Büyük XML Dosyaları:** Büyük XML dosyaları için import işlemi uzun sürebilir
- **Queue Kullanımı:** Büyük import'lar için queue kullanımı önerilir
- **Zamanlanmış Import:** Yoğun saatlerde import yapmayın

### 5. Hata Yönetimi

- **Logları Düzenli Kontrol Edin:** Import loglarını düzenli olarak kontrol edin
- **Hataları Düzeltin:** Hataları tespit edip düzeltin
- **Retry Mekanizması:** Başarısız import'ları retry edin

---

## Sorun Giderme

### XML Okunamıyor

**Sorun:** XML dosyası okunamıyor
**Çözüm:**
- XML URL'inin erişilebilir olduğundan emin olun
- XML formatının geçerli olduğunu kontrol edin
- XML erişimi için gerekli kimlik bilgilerini doğru girdiğinizden emin olun

### Kategori Eşleştirilemiyor

**Sorun:** Kategoriler otomatik eşleştirilemiyor
**Çözüm:**
- Kategori adlarını kontrol edin
- Manuel kategori eşleştirmesi yapın
- Kategori mapping'ini gözden geçirin

### Görseller İndirilemiyor

**Sorun:** Görseller indirilemiyor
**Çözüm:**
- Görsel URL'lerinin erişilebilir olduğundan emin olun
- Görsel formatının desteklendiğini kontrol edin
- Storage klasörünün yazılabilir olduğundan emin olun

### Import Çok Yavaş

**Sorun:** Import işlemi çok yavaş
**Çözüm:**
- Queue kullanımını etkinleştirin
- Büyük XML dosyalarını parçalara bölün
- Sunucu kaynaklarını kontrol edin

---

**Son Güncelleme:** 2025-11-05

