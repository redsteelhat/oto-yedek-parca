@extends('layouts.app')

@section('title', 'Araçlarım')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar -->
        <aside class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                <h3 class="font-bold text-lg mb-4">Hesabım</h3>
                <nav class="space-y-2">
                    <a href="{{ route('account.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Ana Sayfa</a>
                    <a href="{{ route('account.profile') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Profil Bilgilerim</a>
                    <a href="{{ route('account.orders') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Siparişlerim</a>
                    <a href="{{ route('account.addresses') }}" class="block px-4 py-2 rounded hover:bg-gray-100">Adreslerim</a>
                    <a href="{{ route('account.cars') }}" class="block px-4 py-2 rounded bg-primary-50 text-primary-600">Araçlarım</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Araçlarım</h1>
            
            <div class="bg-white rounded-lg shadow-lg p-12 text-center">
                <p class="text-gray-600 text-lg mb-4">Bu özellik yakında eklenecektir.</p>
                <p class="text-gray-500 text-sm">Araçlarınızı kaydederek, aracınıza uygun parçaları daha kolay bulabileceksiniz.</p>
            </div>
        </div>
    </div>
</div>
@endsection

