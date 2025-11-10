@extends('layouts.admin')

@section('title', 'Ayarlar')
@section('page-title', 'Ayarlar')

@section('content')
<div class="bg-white rounded-lg shadow-lg p-6">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex space-x-8 overflow-x-auto">
                <button type="button" onclick="showTab('general')" id="tab-general" class="tab-button border-b-2 border-primary-500 text-primary-600 py-4 px-1 text-sm font-medium whitespace-nowrap">
                    Genel Ayarlar
                </button>
                <button type="button" onclick="showTab('payment')" id="tab-payment" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                    Ödeme Ayarları
                </button>
                <button type="button" onclick="showTab('shipping')" id="tab-shipping" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                    Kargo Ayarları
                </button>
                <button type="button" onclick="showTab('email')" id="tab-email" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                    E-posta Ayarları
                </button>
                <button type="button" onclick="showTab('seo')" id="tab-seo" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                    SEO Ayarları
                </button>
                    <button type="button" onclick="showTab('social')" id="tab-social" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                        Sosyal Medya
                    </button>
                    <button type="button" onclick="showTab('sms')" id="tab-sms" class="tab-button border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-4 px-1 text-sm font-medium whitespace-nowrap">
                        SMS Ayarları
                    </button>
                </nav>
            </div>

        <!-- General Settings -->
        <div id="content-general" class="tab-content">
            <h3 class="text-lg font-bold mb-4">Genel Ayarlar</h3>
            <div class="space-y-4">
                @php
                    $generalSettings = $settings->get('general', collect());
                @endphp
                
                @if($generalSettings->isEmpty())
                    @php
                        $defaultGeneralSettings = [
                            ['key' => 'site_name', 'label' => 'Site Adı', 'type' => 'text', 'value' => ''],
                            ['key' => 'site_description', 'label' => 'Site Açıklaması', 'type' => 'textarea', 'value' => ''],
                            ['key' => 'site_logo', 'label' => 'Logo', 'type' => 'file', 'value' => ''],
                            ['key' => 'site_favicon', 'label' => 'Favicon', 'type' => 'file', 'value' => ''],
                            ['key' => 'contact_email', 'label' => 'İletişim E-postası', 'type' => 'text', 'value' => ''],
                            ['key' => 'contact_phone', 'label' => 'İletişim Telefonu', 'type' => 'text', 'value' => ''],
                            ['key' => 'contact_address', 'label' => 'Adres', 'type' => 'textarea', 'value' => ''],
                        ];
                    @endphp
                    @foreach($defaultGeneralSettings as $setting)
                        @php
                            $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                            $settingValue = $existing ? $existing->value : $setting['value'];
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                            @if($setting['type'] === 'textarea')
                                <textarea name="settings[{{ $setting['key'] }}]" rows="3"
                                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $settingValue }}</textarea>
                            @elseif($setting['type'] === 'file')
                                @if($settingValue && str_starts_with($settingValue, 'settings/'))
                                    <div class="mb-2">
                                        @if($setting['key'] === 'site_logo')
                                            <img src="{{ asset('storage/' . $settingValue) }}" alt="Logo" class="h-20 object-contain">
                                        @else
                                            <img src="{{ asset('storage/' . $settingValue) }}" alt="Favicon" class="h-10 w-10 object-contain">
                                        @endif
                                    </div>
                                @endif
                                <input type="file" name="{{ $setting['key'] }}" accept="{{ $setting['key'] === 'site_favicon' ? 'image/x-icon,image/png' : 'image/*' }}"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @if($settingValue && !str_starts_with($settingValue, 'settings/'))
                                    <input type="hidden" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}">
                                    <p class="text-xs text-gray-500 mt-1">Mevcut: {{ $settingValue }}</p>
                                @endif
                            @else
                                <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @endif
                        </div>
                    @endforeach
                @else
                    @foreach($generalSettings as $setting)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting->label }}</label>
                            @if($setting->type === 'textarea')
                                <textarea name="settings[{{ $setting->key }}]" rows="3"
                                          class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $setting->value }}</textarea>
                            @elseif($setting->type === 'boolean')
                                <label class="flex items-center">
                                    <input type="checkbox" name="settings[{{ $setting->key }}]" value="1" {{ $setting->value ? 'checked' : '' }} class="mr-2">
                                    <span class="text-sm text-gray-700">{{ $setting->label }}</span>
                                </label>
                            @else
                                <input type="{{ $setting->type === 'number' ? 'number' : 'text' }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}"
                                       class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                            @endif
                            @if($setting->description)
                                <p class="text-xs text-gray-500 mt-1">{{ $setting->description }}</p>
                            @endif
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- Payment Settings -->
        <div id="content-payment" class="tab-content hidden">
            <h3 class="text-lg font-bold mb-4">Ödeme Ayarları</h3>
            <div class="space-y-6">
                <!-- İyzico -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold mb-3">İyzico</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $iyzicoSettings = [
                                ['key' => 'payment_iyzico_enabled', 'label' => 'İyzico Aktif', 'type' => 'boolean'],
                                ['key' => 'payment_iyzico_api_key', 'label' => 'API Key', 'type' => 'text'],
                                ['key' => 'payment_iyzico_secret_key', 'label' => 'Secret Key', 'type' => 'text'],
                                ['key' => 'payment_iyzico_base_url', 'label' => 'Base URL', 'type' => 'text', 'default' => 'https://api.iyzipay.com'],
                            ];
                        @endphp
                        @foreach($iyzicoSettings as $setting)
                        @php
                            $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                            $settingValue = $existing ? $existing->value : ($setting['default'] ?? '');
                        @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                                @if($setting['type'] === 'boolean')
                                    <label class="flex items-center">
                                        <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" {{ $settingValue ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm text-gray-700">Aktif</span>
                                    </label>
                                @else
                                    <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- PayTR -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold mb-3">PayTR</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @php
                            $paytrSettings = [
                                ['key' => 'payment_paytr_enabled', 'label' => 'PayTR Aktif', 'type' => 'boolean'],
                                ['key' => 'payment_paytr_merchant_id', 'label' => 'Merchant ID', 'type' => 'text'],
                                ['key' => 'payment_paytr_merchant_key', 'label' => 'Merchant Key', 'type' => 'text'],
                                ['key' => 'payment_paytr_merchant_salt', 'label' => 'Merchant Salt', 'type' => 'text'],
                            ];
                        @endphp
                        @foreach($paytrSettings as $setting)
                            @php
                                $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                                $settingValue = $existing ? $existing->value : '';
                            @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                                @if($setting['type'] === 'boolean')
                                    <label class="flex items-center">
                                        <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" {{ $settingValue ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm text-gray-700">Aktif</span>
                                    </label>
                                @else
                                    <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Havale/EFT -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold mb-3">Havale/EFT</h4>
                    <div class="space-y-4">
                        @php
                            $bankSettings = [
                                ['key' => 'payment_bank_transfer_enabled', 'label' => 'Havale/EFT Aktif', 'type' => 'boolean'],
                                ['key' => 'payment_bank_name', 'label' => 'Banka Adı', 'type' => 'text'],
                                ['key' => 'payment_bank_account_name', 'label' => 'Hesap Adı', 'type' => 'text'],
                                ['key' => 'payment_bank_account_number', 'label' => 'Hesap No', 'type' => 'text'],
                                ['key' => 'payment_bank_iban', 'label' => 'IBAN', 'type' => 'text'],
                                ['key' => 'payment_bank_branch', 'label' => 'Şube', 'type' => 'text'],
                            ];
                        @endphp
                        @foreach($bankSettings as $setting)
                            @php
                                $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                                $settingValue = $existing ? $existing->value : '';
                            @endphp
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                                @if($setting['type'] === 'boolean')
                                    <label class="flex items-center">
                                        <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" {{ $settingValue ? 'checked' : '' }} class="mr-2">
                                        <span class="text-sm text-gray-700">Aktif</span>
                                    </label>
                                @else
                                    <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                                           class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kapıda Ödeme -->
                <div class="border rounded-lg p-4">
                    <h4 class="font-semibold mb-3">Kapıda Ödeme</h4>
                    <div>
                        @php
                            $existing = \App\Models\Setting::where('key', 'payment_cash_on_delivery_enabled')->first();
                            $settingValue = $existing ? $existing->value : false;
                        @endphp
                        <label class="flex items-center">
                            <input type="checkbox" name="settings[payment_cash_on_delivery_enabled]" value="1" {{ $settingValue ? 'checked' : '' }} class="mr-2">
                            <span class="text-sm text-gray-700">Kapıda Ödeme Aktif</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Shipping Settings -->
        <div id="content-shipping" class="tab-content hidden">
            <div class="mb-4 flex justify-between items-center">
                <h3 class="text-lg font-bold">Kargo Ayarları</h3>
                <a href="{{ route('admin.shipping-companies.index') }}" class="bg-primary-600 text-white px-4 py-2 rounded hover:bg-primary-700">
                    Kargo Firmaları Yönetimi
                </a>
            </div>
            <div class="space-y-4">
                @php
                    $shippingSettings = [
                        ['key' => 'shipping_free_shipping_threshold', 'label' => 'Ücretsiz Kargo Limiti (₺)', 'type' => 'number', 'default' => ''],
                        ['key' => 'shipping_default_company', 'label' => 'Varsayılan Kargo Firması', 'type' => 'text', 'default' => ''],
                    ];
                @endphp
                @foreach($shippingSettings as $setting)
                        @php
                            $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                            $settingValue = $existing ? $existing->value : ($setting['default'] ?? '');
                        @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                        <input type="{{ $setting['type'] }}" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Email Settings -->
        <div id="content-email" class="tab-content hidden">
            <h3 class="text-lg font-bold mb-4">E-posta Ayarları</h3>
            <div class="space-y-4">
                @php
                    $emailSettings = [
                        ['key' => 'email_driver', 'label' => 'E-posta Driver', 'type' => 'text', 'default' => 'smtp'],
                        ['key' => 'email_host', 'label' => 'SMTP Host', 'type' => 'text'],
                        ['key' => 'email_port', 'label' => 'SMTP Port', 'type' => 'number', 'default' => '587'],
                        ['key' => 'email_username', 'label' => 'SMTP Kullanıcı Adı', 'type' => 'text'],
                        ['key' => 'email_password', 'label' => 'SMTP Şifre', 'type' => 'text'],
                        ['key' => 'email_encryption', 'label' => 'Şifreleme (tls/ssl)', 'type' => 'text', 'default' => 'tls'],
                        ['key' => 'email_from_address', 'label' => 'Gönderen E-posta', 'type' => 'text'],
                        ['key' => 'email_from_name', 'label' => 'Gönderen Adı', 'type' => 'text'],
                    ];
                @endphp
                @foreach($emailSettings as $setting)
                        @php
                            $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                            $settingValue = $existing ? $existing->value : ($setting['default'] ?? '');
                        @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                        <input type="{{ $setting['type'] === 'number' ? 'number' : 'text' }}" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SEO Settings -->
        <div id="content-seo" class="tab-content hidden">
            <h3 class="text-lg font-bold mb-4">SEO Ayarları</h3>
            <div class="space-y-4">
                @php
                    $seoSettings = [
                        ['key' => 'seo_meta_title', 'label' => 'Meta Başlık', 'type' => 'text'],
                        ['key' => 'seo_meta_description', 'label' => 'Meta Açıklama', 'type' => 'textarea'],
                        ['key' => 'seo_meta_keywords', 'label' => 'Meta Keywords', 'type' => 'text'],
                        ['key' => 'seo_google_analytics', 'label' => 'Google Analytics ID', 'type' => 'text'],
                        ['key' => 'seo_google_tag_manager', 'label' => 'Google Tag Manager ID', 'type' => 'text'],
                        ['key' => 'seo_facebook_pixel', 'label' => 'Facebook Pixel ID', 'type' => 'text'],
                    ];
                @endphp
                @foreach($seoSettings as $setting)
                    @php
                        $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                        $settingValue = $existing ? $existing->value : '';
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                        @if($setting['type'] === 'textarea')
                            <textarea name="settings[{{ $setting['key'] }}]" rows="3"
                                      class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">{{ $settingValue }}</textarea>
                        @else
                            <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Social Media Settings -->
        <div id="content-social" class="tab-content hidden">
            <h3 class="text-lg font-bold mb-4">Sosyal Medya Linkleri</h3>
            <div class="space-y-4">
                @php
                    $socialSettings = [
                        ['key' => 'social_facebook', 'label' => 'Facebook URL', 'type' => 'text'],
                        ['key' => 'social_instagram', 'label' => 'Instagram URL', 'type' => 'text'],
                        ['key' => 'social_twitter', 'label' => 'Twitter/X URL', 'type' => 'text'],
                        ['key' => 'social_youtube', 'label' => 'YouTube URL', 'type' => 'text'],
                        ['key' => 'social_linkedin', 'label' => 'LinkedIn URL', 'type' => 'text'],
                        ['key' => 'social_whatsapp', 'label' => 'WhatsApp Numarası', 'type' => 'text'],
                    ];
                @endphp
                @foreach($socialSettings as $setting)
                    @php
                        $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                        $settingValue = $existing ? $existing->value : '';
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                        <input type="text" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}"
                               class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                               placeholder="https://...">
                    </div>
                @endforeach
            </div>
        </div>

        <!-- SMS Settings -->
        <div id="content-sms" class="tab-content hidden">
            <h3 class="text-lg font-bold mb-4">SMS Ayarları</h3>
            <div class="space-y-4">
                @php
                    $smsSettings = [
                        ['key' => 'sms_enabled', 'label' => 'SMS Bildirimleri Aktif', 'type' => 'boolean'],
                        ['key' => 'sms_gateway', 'label' => 'SMS Gateway', 'type' => 'select', 'options' => ['netgsm' => 'Netgsm', 'iletimerkezi' => 'İleti Merkezi'], 'default' => 'netgsm'],
                        ['key' => 'sms_username', 'label' => 'Kullanıcı Adı / Usercode', 'type' => 'text'],
                        ['key' => 'sms_password', 'label' => 'Şifre / Password', 'type' => 'text'],
                        ['key' => 'sms_api_key', 'label' => 'API Key (İleti Merkezi için)', 'type' => 'text'],
                        ['key' => 'sms_sender', 'label' => 'Gönderen Adı / Başlık', 'type' => 'text'],
                        ['key' => 'sms_api_url', 'label' => 'API URL (Özel gateway için)', 'type' => 'text'],
                    ];
                @endphp
                @foreach($smsSettings as $setting)
                    @php
                        $existing = \App\Models\Setting::where('key', $setting['key'])->first();
                        $settingValue = $existing ? $existing->value : ($setting['default'] ?? '');
                    @endphp
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $setting['label'] }}</label>
                        @if($setting['type'] === 'boolean')
                            <label class="flex items-center">
                                <input type="checkbox" name="settings[{{ $setting['key'] }}]" value="1" {{ $settingValue ? 'checked' : '' }} class="mr-2">
                                <span class="text-sm text-gray-700">Aktif</span>
                            </label>
                        @elseif($setting['type'] === 'select')
                            <select name="settings[{{ $setting['key'] }}]" class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                @foreach($setting['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ $settingValue == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="{{ $setting['type'] }}" name="settings[{{ $setting['key'] }}]" value="{{ $settingValue }}" 
                                   class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500"
                                   @if($setting['key'] === 'sms_password') placeholder="Güvenlik için şifre gizli tutulur" @endif>
                        @endif
                        @if($setting['key'] === 'sms_sender')
                            <p class="text-xs text-gray-500 mt-1">SMS başlığı (Netgsm için kayıtlı başlık, İleti Merkezi için gönderen adı)</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">
                Ayarları Kaydet
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('border-primary-500', 'text-primary-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab
    document.getElementById('content-' + tabName).classList.remove('hidden');
    document.getElementById('tab-' + tabName).classList.remove('border-transparent', 'text-gray-500');
    document.getElementById('tab-' + tabName).classList.add('border-primary-500', 'text-primary-600');
}
</script>
@endpush
@endsection

