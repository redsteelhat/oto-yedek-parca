@extends('layouts.admin')

@section('title', 'Canlı Destek')
@section('page-title', 'Canlı Destek')

@section('content')
<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Toplam Chat</div>
        <div class="text-2xl font-bold text-gray-800">{{ number_format($stats['total']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Açık Chat</div>
        <div class="text-2xl font-bold text-green-600">{{ number_format($stats['open']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Okunmamış</div>
        <div class="text-2xl font-bold text-red-600">{{ number_format($stats['unread']) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-4">
        <div class="text-sm text-gray-600 mb-1">Bana Atanan</div>
        <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['assigned_to_me']) }}</div>
    </div>
</div>

<!-- Filters -->
<div class="mb-6 bg-white rounded-lg shadow p-4">
    <form method="GET" action="{{ route('admin.chat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Durum</label>
            <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Tüm Durumlar</option>
                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Açık</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Beklemede</option>
                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Kapalı</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Öncelik</label>
            <select name="priority" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Tüm Öncelikler</option>
                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Düşük</option>
                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>Yüksek</option>
                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Acil</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Atanan</label>
            <select name="assigned_to" class="w-full border rounded px-3 py-2 text-sm">
                <option value="">Tümü</option>
                <option value="me" {{ request('assigned_to') == 'me' ? 'selected' : '' }}>Bana Atanan</option>
                <option value="unassigned" {{ request('assigned_to') == 'unassigned' ? 'selected' : '' }}>Atanmamış</option>
                @foreach(\App\Models\User::where('user_type', 'admin')->get() as $admin)
                    <option value="{{ $admin->id }}" {{ request('assigned_to') == $admin->id ? 'selected' : '' }}>
                        {{ $admin->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Arama</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Konu, müşteri ara..." class="w-full border rounded px-3 py-2 text-sm">
        </div>
        <div class="md:col-span-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                Filtrele
            </button>
            <a href="{{ route('admin.chat.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 text-sm ml-2">
                Temizle
            </a>
        </div>
    </form>
</div>

<!-- Chat Rooms Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold">Chat Listesi</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Konu</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Müşteri</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Durum</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Öncelik</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Atanan</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Okunmamış</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Son Mesaj</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">İşlemler</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($chatRooms as $chatRoom)
                <tr class="{{ $chatRoom->unread_count_admin > 0 ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.chat.show', $chatRoom) }}" class="font-medium text-blue-600 hover:text-blue-800">
                            {{ Str::limit($chatRoom->subject, 40) }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <div>
                            <div class="font-medium">{{ $chatRoom->name ?? $chatRoom->user->name ?? 'Misafir' }}</div>
                            @if($chatRoom->email)
                                <div class="text-gray-500 text-xs">{{ $chatRoom->email }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($chatRoom->status == 'open') bg-green-100 text-green-800
                            @elseif($chatRoom->status == 'closed') bg-gray-100 text-gray-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($chatRoom->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full 
                            @if($chatRoom->priority == 'urgent') bg-red-100 text-red-800
                            @elseif($chatRoom->priority == 'high') bg-orange-100 text-orange-800
                            @elseif($chatRoom->priority == 'low') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($chatRoom->priority) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $chatRoom->assignedAdmin ? $chatRoom->assignedAdmin->name : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($chatRoom->unread_count_admin > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $chatRoom->unread_count_admin }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $chatRoom->last_message_at ? $chatRoom->last_message_at->diffForHumans() : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('admin.chat.show', $chatRoom) }}" class="text-blue-600 hover:text-blue-900">Görüntüle</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                        Chat bulunamadı.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $chatRooms->links() }}
</div>
@endsection

