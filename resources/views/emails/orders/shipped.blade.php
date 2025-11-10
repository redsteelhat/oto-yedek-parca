<x-mail::message>
# Siparişiniz Kargoya Verildi!

Merhaba {{ $order->user->name ?? $order->shipping_name }},

Siparişiniz ({{ $order->order_number }}) kargoya verildi ve yola çıktı.

## Kargo Bilgileri

**Kargo Firması:** {{ $order->cargo_company ?? 'Belirtilmemiş' }}  
@if($order->tracking_number)
**Takip Numarası:** {{ $order->tracking_number }}  
@endif

@if($order->tracking_number)
<x-mail::button :url="route('account.orders.show', $order)">
Siparişimi Takip Et
</x-mail::button>
@endif

Siparişinizin durumunu hesabınızdan takip edebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
