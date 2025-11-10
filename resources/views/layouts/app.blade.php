<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $tenantBranding['name'] ?? 'Otomobil Yedek Parça')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(!empty($tenantBranding['favicon_url']))
        <link rel="icon" type="image/png" href="{{ $tenantBranding['favicon_url'] }}">
    @endif
    @stack('styles')
    <style>
        :root {
            --brand-primary: {{ $tenantBranding['primary_color'] ?? '#2563EB' }};
            --brand-secondary: {{ $tenantBranding['secondary_color'] ?? '#1E40AF' }};
        }

        .brand-text {
            color: var(--brand-primary) !important;
        }

        .brand-bg {
            background-color: var(--brand-primary) !important;
            color: #ffffff !important;
        }

        .brand-bg:hover,
        .brand-bg:focus {
            background-color: var(--brand-secondary) !important;
        }

        .brand-border {
            border-color: var(--brand-primary) !important;
        }

        .brand-link {
            color: #374151;
            border-bottom: 2px solid transparent;
            transition: color 0.2s ease, border-color 0.2s ease;
        }

        .brand-link:hover {
            color: var(--brand-primary);
        }

        .brand-link-active {
            color: var(--brand-primary) !important;
            border-color: var(--brand-primary) !important;
        }

        .brand-focus:focus {
            --tw-ring-color: var(--brand-primary);
            border-color: var(--brand-primary);
        }

        .brand-mobile-active {
            background-color: var(--brand-primary) !important;
            color: #ffffff !important;
        }

        .brand-outline {
            border: 1px solid var(--brand-primary);
            color: var(--brand-primary);
        }

        .brand-outline:hover {
            background-color: var(--brand-primary);
            color: #ffffff;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Top Bar -->
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        @if(!empty($tenantBranding['logo_url']))
                            <img src="{{ $tenantBranding['logo_url'] }}" alt="{{ $tenantBranding['name'] ?? 'Logo' }}" class="h-10 w-auto">
                        @else
                            <span class="text-xl sm:text-2xl font-bold brand-text">
                                {{ $tenantBranding['name'] ?? 'Yedek Parça' }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Desktop Search Bar -->
                <div class="hidden lg:flex flex-1 max-w-lg mx-8">
                    <form action="{{ route('products.index') }}" method="GET" class="w-full">
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Parça adı, OEM, ürün kodu ile arama..." 
                                   class="w-full border rounded-l-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 brand-focus">
                            <button type="submit" class="absolute right-0 top-0 h-full px-4 rounded-r-lg brand-bg transition">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right Menu -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <!-- Language Selector -->
                    <div class="relative">
                        <select onchange="changeLanguage(this.value)" class="bg-white text-gray-700 px-2 py-1 rounded text-sm border border-gray-300 focus:outline-none focus:ring-2 brand-focus">
                            <option value="tr" {{ app()->getLocale() == 'tr' ? 'selected' : '' }}>TR</option>
                            <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>EN</option>
                        </select>
                    </div>
                    
                    <!-- Mobile Search Button -->
                    <button onclick="toggleMobileSearch()" class="lg:hidden text-gray-500 hover:text-gray-700 p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>

                    <!-- Cart -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gray-700 p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @php
                            $cartCount = count(session('cart', []));
                        @endphp
                        @if($cartCount > 0)
                            <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $cartCount }}</span>
                        @endif
                    </a>

                    <!-- Mobile Menu Button -->
                    <button onclick="toggleMobileMenu()" class="lg:hidden text-gray-500 hover:text-gray-700 p-2">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="menuIcon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" id="closeIcon">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <!-- Desktop User Menu -->
                    @auth
                        <div class="hidden lg:block relative group">
                            <button class="flex items-center text-gray-500 hover:text-gray-700 px-3 py-2">
                                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="hidden xl:inline">{{ auth()->user()->name }}</span>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                                <a href="{{ route('account.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Hesabım</a>
                                <a href="{{ route('account.orders') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Siparişlerim</a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Admin Panel</a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Çıkış</button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="hidden lg:flex items-center space-x-2">
                            <a href="{{ route('login') }}" class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm">Giriş</a>
                            <a href="{{ route('register') }}" class="brand-bg px-3 py-2 rounded text-sm">Kayıt Ol</a>
                        </div>
                    @endauth
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div id="mobileSearch" class="hidden lg:hidden pb-4">
                <form action="{{ route('products.index') }}" method="GET" class="w-full">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Ara..." 
                               class="w-full border rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 brand-focus">
                        <button type="submit" class="absolute right-2 top-2 px-3 py-1 rounded text-sm brand-bg">
                            Ara
                        </button>
                    </div>
                </form>
            </div>

            <!-- Main Navigation -->
            <nav class="border-t border-gray-200">
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex space-x-8 h-12 items-center overflow-x-auto">
                    <a href="{{ route('home') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('home') ? 'brand-link-active' : '' }}">
                        Ana Sayfa
                    </a>
                    <a href="{{ route('products.index') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('products.*') ? 'brand-link-active' : '' }}">
                        Kategoriler
                    </a>
                    <a href="{{ route('products.find-by-car') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap {{ request()->routeIs('products.find-by-car') ? 'brand-link-active' : '' }}">
                        Araçla Parça Bul
                    </a>
                    <a href="{{ route('campaigns.index') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap">
                        Kampanyalar
                    </a>
                    <a href="{{ route('about') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap">
                        Hakkımızda
                    </a>
                    <a href="{{ route('contact') }}" class="brand-link px-3 py-2 text-sm font-medium whitespace-nowrap">
                        İletişim
                    </a>
                </div>

                <!-- Mobile Navigation -->
                <div id="mobileMenu" class="hidden lg:hidden bg-white border-t">
                    <div class="px-4 py-2 space-y-1">
                        <a href="{{ route('home') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium {{ request()->routeIs('home') ? 'brand-mobile-active' : '' }}">
                            Ana Sayfa
                        </a>
                        <a href="{{ route('products.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium {{ request()->routeIs('products.*') ? 'brand-mobile-active' : '' }}">
                            Kategoriler
                        </a>
                        <a href="{{ route('products.find-by-car') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium {{ request()->routeIs('products.find-by-car') ? 'brand-mobile-active' : '' }}">
                            Araçla Parça Bul
                        </a>
                        <a href="{{ route('campaigns.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                            Kampanyalar
                        </a>
                        <a href="{{ route('about') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                            Hakkımızda
                        </a>
                        <a href="{{ route('contact') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                            İletişim
                        </a>
                        @auth
                            <div class="border-t pt-2 mt-2">
                                <a href="{{ route('account.index') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                                    Hesabım
                                </a>
                                <a href="{{ route('account.orders') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                                    Siparişlerim
                                </a>
                                @if(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                                        Admin Panel
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium">
                                        Çıkış
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="border-t pt-2 mt-2 space-y-2">
                                <a href="{{ route('login') }}" class="block px-3 py-2 text-gray-700 hover:bg-gray-100 rounded text-sm font-medium text-center">
                                    Giriş
                                </a>
                                <a href="{{ route('register') }}" class="block px-3 py-2 rounded text-sm font-medium text-center brand-bg">
                                    Kayıt Ol
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mx-4 sm:mx-0" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mx-4 sm:mx-0" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-12">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">Hakkımızda</h3>
                    <p class="text-gray-400 text-sm">Otomobil yedek parça satışı için güvenilir çözüm.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Hızlı Linkler</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-white transition">Ana Sayfa</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-white transition">Ürünler</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">Hakkımızda</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">İletişim</a></li>
                        <li><a href="{{ route('privacy') }}" class="hover:text-white transition">KVKK</a></li>
                        <li><a href="{{ route('return-policy') }}" class="hover:text-white transition">İade Koşulları</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">İletişim</h3>
                    <p class="text-gray-400 text-sm">Email: info@yedekparca.com</p>
                    <p class="text-gray-400 text-sm">Tel: 0850 XXX XX XX</p>
                    <p class="text-gray-400 text-sm">Adres: [Firma Adresi]</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Sosyal Medya</h3>
                    <div class="flex space-x-4 mb-6">
                        <!-- Social media links -->
                    </div>
                    <h3 class="text-lg font-semibold mb-4">Ödeme Yöntemleri</h3>
                    <div class="flex flex-wrap gap-2">
                        <!-- Payment method icons -->
                    </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-gray-700 text-center text-gray-400 text-sm">
                <p>&copy; {{ date('Y') }} {{ $tenantBranding['name'] ?? config('app.name', 'Yedek Parça') }}. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- Chat Widget -->
    <div id="chatWidget" class="fixed bottom-4 right-4 z-50">
        <button onclick="toggleChatWidget()" class="brand-bg rounded-full p-4 shadow-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            @php
                $unreadCount = 0;
                if (auth()->check()) {
                    $unreadCount = \App\Models\ChatRoom::where('user_id', auth()->id())
                        ->where('unread_count_user', '>', 0)
                        ->sum('unread_count_user');
                }
            @endphp
            @if($unreadCount > 0)
                <span class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">{{ $unreadCount }}</span>
            @endif
        </button>
    </div>

    <!-- Chat Popup -->
    <div id="chatPopup" class="fixed bottom-20 right-4 w-96 h-96 bg-white rounded-lg shadow-2xl z-50 hidden flex flex-col">
        <div class="brand-bg p-4 rounded-t-lg flex justify-between items-center">
            <h3 class="font-semibold">Canlı Destek</h3>
            <button onclick="toggleChatWidget()" class="text-white hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-4" id="chatMessages">
            <div class="text-center text-gray-500 text-sm">
                <p>Canlı destek başlatmak için yeni bir mesaj oluşturun.</p>
                <a href="{{ route('chat.create') }}" class="mt-2 inline-block brand-bg px-4 py-2 rounded text-sm">
                    Yeni Mesaj
                </a>
            </div>
        </div>
    </div>

    @stack('scripts')
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            const menuIcon = document.getElementById('menuIcon');
            const closeIcon = document.getElementById('closeIcon');
            
            menu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        }

        function toggleMobileSearch() {
            const search = document.getElementById('mobileSearch');
            search.classList.toggle('hidden');
        }

        function changeLanguage(locale) {
            window.location.href = '{{ url('/') }}/dil/' + locale;
        }

        function toggleChatWidget() {
            const popup = document.getElementById('chatPopup');
            popup.classList.toggle('hidden');
        }
    </script>
</body>
</html>
