<x-mail::message>
# Siparişiniz Teslim Edildi!

Merhaba {{ $order->user->name ?? $order->shipping_name }},

Siparişiniz ({{ $order->order_number }}) başarıyla teslim edildi.

## Sipariş Bilgileri

**Sipariş No:** {{ $order->order_number }}  
**Teslimat Tarihi:** {{ $order->updated_at->format('d.m.Y H:i') }}

Ürünlerinizi beğendiyseniz, bizim için değerlendirme yapabilirsiniz.

<x-mail::button :url="route('account.orders.show', $order)">
Sipariş Detaylarını Görüntüle
</x-mail::button>

Tekrar alışveriş yapmak için sitemizi ziyaret edebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
