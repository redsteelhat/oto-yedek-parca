<x-mail::message>
# Hoş Geldiniz!

Merhaba {{ $user->name }},

{{ config('app.name') }}'a kaydınız başarıyla tamamlandı. Hesabınızı kullanmaya başlayabilirsiniz.

## Hesap Bilgileriniz

**E-posta:** {{ $user->email }}  
**Kayıt Tarihi:** {{ $user->created_at->format('d.m.Y H:i') }}

<x-mail::button :url="route('home')">
Alışverişe Başla
</x-mail::button>

Hesabınızdan siparişlerinizi takip edebilir, adreslerinizi yönetebilir ve daha fazlasını yapabilirsiniz.

Sorularınız için bizimle iletişime geçebilirsiniz.

Teşekkürler,<br>
{{ config('app.name') }}
</x-mail::message>
