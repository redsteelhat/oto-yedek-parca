# SQLite'a Geçiş Scripti
# Bu script MySQL yerine SQLite kullanmanıza yardımcı olur

Write-Host "SQLite'a geçiş yapılıyor..." -ForegroundColor Green

# .env dosyasını oku
$envContent = Get-Content .env -Raw

# MySQL ayarlarını yorum satırı yap
$envContent = $envContent -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite'
$envContent = $envContent -replace 'DB_HOST=127\.0\.0\.1', '# DB_HOST=127.0.0.1'
$envContent = $envContent -replace 'DB_PORT=3306', '# DB_PORT=3306'
$envContent = $envContent -replace 'DB_DATABASE=oto_paca_script', "DB_DATABASE=$PWD\database\database.sqlite"
$envContent = $envContent -replace 'DB_USERNAME=root', '# DB_USERNAME=root'
$envContent = $envContent -replace 'DB_PASSWORD=', '# DB_PASSWORD='

# .env dosyasını güncelle
$envContent | Set-Content .env -Encoding UTF8

# SQLite veritabanı dosyasını oluştur
$dbPath = "database\database.sqlite"
if (-not (Test-Path $dbPath)) {
    New-Item -Path $dbPath -ItemType File -Force | Out-Null
    Write-Host "SQLite veritabanı dosyası oluşturuldu: $dbPath" -ForegroundColor Green
} else {
    Write-Host "SQLite veritabanı dosyası zaten mevcut: $dbPath" -ForegroundColor Yellow
}

# Config cache'i temizle
Write-Host "Config cache temizleniyor..." -ForegroundColor Green
php artisan config:clear

Write-Host "`nSQLite'a geçiş tamamlandı!" -ForegroundColor Green
Write-Host "Şimdi migration'ları çalıştırabilirsiniz:" -ForegroundColor Cyan
Write-Host "  php artisan migrate" -ForegroundColor White

