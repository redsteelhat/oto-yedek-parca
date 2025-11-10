<x-mail::message>
# Siparişiniz Alındı!

Merhaba {{ $order->user->name ?? $order->shipping_name }},

Siparişiniz başarıyla alındı ve işleme alındı. Sipariş detaylarınız aşağıda yer almaktadır.

## Sipariş Bilgileri

**Sipariş No:** {{ $order->order_number }}  
**Sipariş Tarihi:** {{ $order->created_at->format('d.m.Y H:i') }}  
**Ödeme Yöntemi:** {{ $order->payment_method == 'bank_transfer' ? 'Havale/EFT' : ($order->payment_method == 'cash_on_delivery' ? 'Kapıda Ödeme' : ucfirst($order->payment_method)) }}  
**Ödeme Durumu:** {{ $order->payment_status == 'paid' ? 'Ödendi' : ($order->payment_status == 'pending' ? 'Beklemede' : ucfirst($order->payment_status)) }}

## Teslimat Adresi

{{ $order->shipping_name }}  
{{ $order->shipping_address }}  
{{ $order->shipping_district }}, {{ $order->shipping_city }}  
{{ $order->shipping_postal_code }}  
Telefon: {{ $order->shipping_phone }}

## Sipariş Özeti

<x-mail::table>
| Ürün | Adet | Fiyat |
|:-----|:-----|:------|
@foreach($order->items as $item)
| {{ $item->product_name }} | {{ $item->quantity }} | {{ number_format($item->total, 2) }} ₺ |
@endforeach
</x-mail::table>

**Ara Toplam:** {{ number_format($order->subtotal, 2) }} ₺  
@if($order->discount_amount > 0)
**İndirim:** -{{ number_format($order->discount_amount, 2) }} ₺  
@endif
**Kargo:** {{ number_format($order->shipping_cost, 2) }} ₺  
**KDV:** {{ number_format($order->tax_amount, 2) }} ₺  
**TOPLAM:** **{{ number_format($order->total, 2) }} ₺**

<x-mail::button :url="route('account.orders.show', $order)">
Siparişimi Görüntüle
</x-mail::button>

Siparişinizin durumunu takip etmek için hesabınıza giriş yapabilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
