<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - {{ $tenantBranding['name'] ?? 'Yedek Parça' }}</title>
    @if(!empty($tenantBranding['favicon_url']))
        <link rel="icon" type="image/png" href="{{ $tenantBranding['favicon_url'] }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        :root {
            --brand-primary: {{ $tenantBranding['primary_color'] ?? '#2563EB' }};
            --brand-secondary: {{ $tenantBranding['secondary_color'] ?? '#1E40AF' }};
        }

        .admin-sidebar {
            background-color: var(--brand-primary);
        }

        .admin-sidebar a {
            color: rgba(255, 255, 255, 0.85);
            transition: background-color 0.2s ease;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active-link {
            background-color: rgba(255, 255, 255, 0.15);
        }

        .admin-sidebar h1 {
            color: #ffffff;
        }

        .admin-topbar {
            border-bottom: 1px solid rgba(148, 163, 184, 0.3);
        }

        .admin-btn {
            background-color: var(--brand-primary);
            color: #ffffff;
        }

        .admin-btn:hover {
            background-color: var(--brand-secondary);
        }

        .brand-focus:focus {
            --tw-ring-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <aside class="w-64 admin-sidebar text-white">
            <div class="p-4 flex items-center space-x-3">
                @if(!empty($tenantBranding['logo_url']))
                    <img src="{{ $tenantBranding['logo_url'] }}" alt="{{ $tenantBranding['name'] ?? 'Logo' }}" class="h-10 w-auto">
                @endif
                <div>
                    <h1 class="text-xl font-semibold">{{ $tenantBranding['name'] ?? 'Admin Panel' }}</h1>
                    <p class="text-xs text-white/80">Yönetim Paneli</p>
                </div>
            </div>
            <nav class="mt-8">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 {{ request()->routeIs('admin.dashboard') ? 'active-link' : '' }}">
                    Dashboard
                </a>
                <a href="{{ route('admin.products.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.products.*') ? 'active-link' : '' }}">
                    Ürünler
                </a>
                <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.orders.*') ? 'active-link' : '' }}">
                    Siparişler
                </a>
                <a href="{{ route('admin.customers.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.customers.*') ? 'active-link' : '' }}">
                    Müşteriler
                </a>
                <a href="{{ route('admin.suppliers.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.suppliers.*') ? 'active-link' : '' }}">
                    Tedarikçiler
                </a>
                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.categories.*') ? 'active-link' : '' }}">
                    Kategoriler
                </a>
                <a href="{{ route('admin.car-brands.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.car-brands.*') ? 'active-link' : '' }}">
                    Araç Markaları
                </a>
                <a href="{{ route('admin.coupons.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.coupons.*') ? 'active-link' : '' }}">
                    Kuponlar
                </a>
                <a href="{{ route('admin.campaigns.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.campaigns.*') ? 'active-link' : '' }}">
                    Kampanyalar
                </a>
                <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.settings.*') ? 'active-link' : '' }}">
                    Ayarlar
                </a>
                <a href="{{ route('admin.pages.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.pages.*') ? 'active-link' : '' }}">
                    Sayfalar (CMS)
                </a>
                <a href="{{ route('admin.shipping-companies.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.shipping-companies.*') ? 'active-link' : '' }}">
                    Kargo Firmaları
                </a>
                <a href="{{ route('admin.bank-transfers.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.bank-transfers.*') ? 'active-link' : '' }}">
                    Havale/EFT Onayları
                </a>
                <a href="{{ route('admin.reviews.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.reviews.*') ? 'active-link' : '' }}">
                    Ürün Yorumları
                </a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.reports.*') ? 'active-link' : '' }}">
                    Raporlar
                </a>
                <a href="{{ route('admin.chat.index') }}" class="block px-4 py-2 {{ request()->routeIs('admin.chat.*') ? 'active-link' : '' }}">
                    Canlı Destek
                    @php
                        $unreadChats = \App\Models\ChatRoom::where('unread_count_admin', '>', 0)->count();
                    @endphp
                    @if($unreadChats > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-600 text-white">
                            {{ $unreadChats }}
                        </span>
                    @endif
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white shadow admin-topbar">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold">@yield('page-title', 'Dashboard')</h2>
                    <div class="flex items-center space-x-4">
                        <!-- Language Selector -->
                        <div class="relative">
                            <select onchange="changeLanguage(this.value)" class="bg-white text-gray-700 px-3 py-1 rounded text-sm border border-gray-300 focus:outline-none focus:ring-2 brand-focus">
                                <option value="tr" {{ app()->getLocale() == 'tr' ? 'selected' : '' }}>TR</option>
                                <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                            </select>
                        </div>
                        <span class="text-gray-600">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">{{ __('common.logout') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mx-6 mt-4 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mx-6 mt-4 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        function changeLanguage(locale) {
            window.location.href = '{{ url('/') }}/dil/' + locale;
        }
    </script>
</body>
</html>

