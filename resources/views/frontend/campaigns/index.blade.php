@extends('layouts.app')

@section('title', 'Kampanyalar')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Kampanyalar</h1>

    @if($campaigns->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($campaigns as $campaign)
                @if($campaign->isActive())
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition border-2 border-red-500">
                        @if($campaign->image)
                            <img src="{{ asset('storage/' . $campaign->image) }}" alt="{{ $campaign->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gradient-to-r from-red-500 to-red-600 flex items-center justify-center text-white text-2xl font-bold">
                                %{{ number_format($campaign->discount_value, 0) }} İndirim
                            </div>
                        @endif
                        <div class="p-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $campaign->name }}</h2>
                            @if($campaign->description)
                                <p class="text-gray-600 mb-4">{{ Str::limit($campaign->description, 150) }}</p>
                            @endif
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-semibold text-red-600">
                                    %{{ number_format($campaign->discount_value, 0) }} İndirim
                                </span>
                                <span class="text-sm text-gray-500">
                                    {{ $campaign->start_date->format('d.m.Y') }} - {{ $campaign->end_date->format('d.m.Y') }}
                                </span>
                            </div>
                            <a href="{{ route('campaigns.show', $campaign->slug) }}" 
                               class="block w-full bg-primary-600 text-white text-center px-4 py-2 rounded-lg hover:bg-primary-700 transition">
                                Kampanyaya Git
                            </a>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <div class="mt-8">
            {{ $campaigns->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <p class="text-gray-600 text-lg">Şu anda aktif kampanya bulunmuyor.</p>
        </div>
    @endif
</div>
@endsection

