@extends('layouts.app')

@section('title', 'Canlı Destek')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Canlı Destek</h1>
        <p class="text-gray-600">Sorularınız için bizimle iletişime geçebilirsiniz.</p>
    </div>

    <div class="mb-6">
        <a href="{{ route('chat.create') }}" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium inline-block">
            Yeni Mesaj Oluştur
        </a>
    </div>

    @if($chatRooms->count() > 0)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold">Mesajlarım</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($chatRooms as $chatRoom)
                    <a href="{{ route('chat.show', $chatRoom) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <h3 class="font-semibold text-gray-900">{{ $chatRoom->subject }}</h3>
                                    @if($chatRoom->unread_count_user > 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                            {{ $chatRoom->unread_count_user }} yeni
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        @if($chatRoom->status == 'open') bg-green-100 text-green-800
                                        @elseif($chatRoom->status == 'closed') bg-gray-100 text-gray-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ ucfirst($chatRoom->status) }}
                                    </span>
                                </div>
                                @if($chatRoom->latestMessage)
                                    <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($chatRoom->latestMessage->message, 100) }}</p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ $chatRoom->last_message_at ? $chatRoom->last_message_at->diffForHumans() : $chatRoom->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="ml-4">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
            </svg>
            <p class="text-gray-600 mb-4">Henüz mesajınız yok.</p>
            <a href="{{ route('chat.create') }}" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium inline-block">
                İlk Mesajınızı Oluşturun
            </a>
        </div>
    @endif
</div>
@endsection

