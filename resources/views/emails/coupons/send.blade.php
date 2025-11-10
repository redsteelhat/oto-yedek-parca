<x-mail::message>
# Özel Kuponunuz Hazır!

Merhaba {{ $user->name }},

Size özel bir kupon hazırladık. Hemen kullanmaya başlayabilirsiniz!

## Kupon Detayları

**Kupon Kodu:** **{{ $coupon->code }}**  
**Kupon Adı:** {{ $coupon->name }}  
@if($coupon->description)
**Açıklama:** {{ $coupon->description }}  
@endif

**İndirim Tipi:** {{ $coupon->type == 'percentage' ? 'Yüzde' : 'Sabit Tutar' }}  
**İndirim Değeri:** {{ $coupon->type == 'percentage' ? $coupon->value . '%' : number_format($coupon->value, 2) . ' ₺' }}  
@if($coupon->min_purchase_amount)
**Minimum Alışveriş Tutarı:** {{ number_format($coupon->min_purchase_amount, 2) }} ₺  
@endif
@if($coupon->max_discount_amount)
**Maksimum İndirim Tutarı:** {{ number_format($coupon->max_discount_amount, 2) }} ₺  
@endif

**Geçerlilik:** {{ $coupon->start_date->format('d.m.Y') }} - {{ $coupon->end_date->format('d.m.Y') }}

<x-mail::button :url="route('products.index')">
Hemen Alışverişe Başla
</x-mail::button>

Kupon kodunu sepetinizde kullanabilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
