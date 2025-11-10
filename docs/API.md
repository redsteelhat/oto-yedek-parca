# API Dokümantasyonu

## Genel Bilgiler

Bu dokümantasyon, Oto Yedek Parça Script'i için mevcut API endpoint'lerini açıklar.

**Base URL:** `http://your-domain.com/api`

**Format:** Tüm API yanıtları JSON formatındadır.

---

## Rate Limiting

API endpoint'leri rate limiting ile korunmaktadır:
- **Limit:** 60 istek/dakika
- **Middleware:** `throttle:60,1`

Rate limit aşıldığında `429 Too Many Requests` hatası döner.

---

## Endpoint'ler

### 1. Araç Markaları

#### GET `/api/car-brands`

Aktif araç markalarını listeler.

**Request:**
```http
GET /api/car-brands
```

**Response:**
```json
[
  {
    "id": 1,
    "name": "Toyota"
  },
  {
    "id": 2,
    "name": "Volkswagen"
  }
]
```

**Status Codes:**
- `200 OK` - Başarılı

---

### 2. Araç Modelleri

#### GET `/api/car-models/{brandId}`

Belirli bir markaya ait aktif modelleri listeler.

**Request:**
```http
GET /api/car-models/1
```

**Parameters:**
- `brandId` (required, integer) - Marka ID'si

**Response:**
```json
[
  {
    "id": 1,
    "name": "Corolla"
  },
  {
    "id": 2,
    "name": "Camry"
  }
]
```

**Status Codes:**
- `200 OK` - Başarılı
- `404 Not Found` - Marka bulunamadı

---

### 3. Araç Yılları

#### GET `/api/car-years/{modelId}`

Belirli bir modele ait aktif yılları listeler.

**Request:**
```http
GET /api/car-years/1
```

**Parameters:**
- `modelId` (required, integer) - Model ID'si

**Response:**
```json
[
  {
    "id": 1,
    "year": 2022,
    "motor_type": "1.8"
  },
  {
    "id": 2,
    "year": 2021,
    "motor_type": "1.8"
  }
]
```

**Status Codes:**
- `200 OK` - Başarılı
- `404 Not Found` - Model bulunamadı

---

## Hata Yönetimi

### Hata Formatı

Tüm hatalar aşağıdaki formatta döner:

```json
{
  "message": "Hata mesajı",
  "errors": {
    "field": ["Hata detayı"]
  }
}
```

### Status Kodları

- `200 OK` - Başarılı istek
- `400 Bad Request` - Geçersiz istek
- `404 Not Found` - Kaynak bulunamadı
- `429 Too Many Requests` - Rate limit aşıldı
- `500 Internal Server Error` - Sunucu hatası

---

## Örnek Kullanım

### JavaScript (Fetch API)

```javascript
// Araç markalarını getir
fetch('http://your-domain.com/api/car-brands')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

// Markaya ait modelleri getir
fetch('http://your-domain.com/api/car-models/1')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));

// Modele ait yılları getir
fetch('http://your-domain.com/api/car-years/1')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error('Error:', error));
```

### jQuery

```javascript
// Araç markalarını getir
$.ajax({
  url: 'http://your-domain.com/api/car-brands',
  method: 'GET',
  success: function(data) {
    console.log(data);
  },
  error: function(error) {
    console.error('Error:', error);
  }
});
```

### cURL

```bash
# Araç markalarını getir
curl -X GET "http://your-domain.com/api/car-brands"

# Markaya ait modelleri getir
curl -X GET "http://your-domain.com/api/car-models/1"

# Modele ait yılları getir
curl -X GET "http://your-domain.com/api/car-years/1"
```

---

## Notlar

- Tüm endpoint'ler public'tir ve authentication gerektirmez.
- Rate limiting her IP adresi için ayrı ayrı uygulanır.
- Tüm endpoint'ler sadece aktif kayıtları döner (`is_active = true`).

---

**Son Güncelleme:** 2025-11-05

