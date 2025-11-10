# Veritabanı Kurulum Talimatları

## Sorun
MySQL veritabanına bağlanırken şifre hatası alıyorsunuz.

## Çözüm 1: MySQL Şifresini Ekleyin

1. `.env` dosyasını açın
2. Aşağıdaki satırı bulun:
   ```
   DB_PASSWORD=
   ```
3. MySQL root şifrenizi ekleyin:
   ```
   DB_PASSWORD=şifreniz_buraya
   ```
4. Dosyayı kaydedin

## Çözüm 2: MySQL Veritabanını Oluşturun

Eğer veritabanı henüz oluşturulmamışsa:

### MySQL Komut Satırı ile:
```sql
CREATE DATABASE oto_paca_script CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### veya phpMyAdmin ile:
1. phpMyAdmin'e giriş yapın
2. "Databases" sekmesine gidin
3. "Create database" butonuna tıklayın
4. Database name: `oto_paca_script`
5. Collation: `utf8mb4_unicode_ci`
6. "Create" butonuna tıklayın

## Çözüm 3: SQLite Kullanın (Kolay Başlangıç)

Eğer MySQL ile sorun yaşıyorsanız, SQLite kullanabilirsiniz:

1. `.env` dosyasında şu satırları değiştirin:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=C:\Users\tarik\OneDrive\Masaüstü\scriptler\otoYedekParcaScript\database\database.sqlite
   ```
   
   MySQL ile ilgili satırları yorum satırı yapın veya silin:
   ```
   # DB_HOST=127.0.0.1
   # DB_PORT=3306
   # DB_USERNAME=root
   # DB_PASSWORD=
   ```

2. SQLite veritabanı dosyasını oluşturun:
   ```bash
   New-Item -Path "database\database.sqlite" -ItemType File
   ```

## Migration'ları Çalıştırın

Veritabanı ayarlarını yaptıktan sonra:

```bash
php artisan migrate
```

## Test Etme

Veritabanı bağlantısını test etmek için:

```bash
php artisan tinker
```

Tinker içinde:
```php
DB::connection()->getPdo();
```

Başarılı olursa "PDO Object" görmelisiniz.

