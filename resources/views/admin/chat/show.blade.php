@extends('layouts.admin')

@section('title', $chatRoom->subject)
@section('page-title', 'Chat: ' . Str::limit($chatRoom->subject, 50))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chat Messages -->
    <div class="lg:col-span-2 bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-primary-600 text-white p-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-semibold">{{ $chatRoom->subject }}</h2>
                <a href="{{ route('admin.chat.index') }}" class="text-white hover:text-gray-200 text-sm">
                    ← Geri Dön
                </a>
            </div>
            <div class="flex items-center space-x-4 text-sm">
                <span>Müşteri: {{ $chatRoom->name ?? $chatRoom->user->name ?? 'Misafir' }}</span>
                @if($chatRoom->email)
                    <span>{{ $chatRoom->email }}</span>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="p-4 sm:p-6 h-96 overflow-y-auto bg-gray-50" id="chatMessages">
            @foreach($messages as $message)
                <div class="mb-4 flex {{ $message->sender_type == 'admin' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                        <div class="rounded-lg p-3 sm:p-4 {{ $message->sender_type == 'admin' ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                            <p class="text-sm sm:text-base whitespace-pre-wrap">{{ $message->message }}</p>
                            <p class="text-xs mt-2 {{ $message->sender_type == 'admin' ? 'text-white/70' : 'text-gray-500' }}">
                                {{ $message->created_at->format('d.m.Y H:i') }}
                                @if($message->sender_type == 'admin' && $message->user)
                                    - {{ $message->user->name }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Message Form -->
        @if($chatRoom->status == 'open')
            <div class="p-4 sm:p-6 border-t border-gray-200 bg-white">
                <form id="messageForm" class="flex space-x-2">
                    @csrf
                    <input type="text" name="message" id="messageInput" placeholder="Mesajınızı yazın..." 
                           class="flex-1 border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500" 
                           required maxlength="2000">
                    <button type="submit" class="bg-primary-600 text-white px-4 sm:px-6 py-2 rounded hover:bg-primary-700 font-medium">
                        Gönder
                    </button>
                </form>
            </div>
        @else
            <div class="p-4 sm:p-6 border-t border-gray-200 bg-gray-50 text-center text-gray-600">
                <p>Bu chat kapatılmış.</p>
            </div>
        @endif
    </div>

    <!-- Chat Info Sidebar -->
    <div class="space-y-6">
        <!-- Status & Priority -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Chat Bilgileri</h3>
            
            <form method="POST" action="{{ route('admin.chat.update-status', $chatRoom) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Durum</label>
                    <select name="status" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="open" {{ $chatRoom->status == 'open' ? 'selected' : '' }}>Açık</option>
                        <option value="pending" {{ $chatRoom->status == 'pending' ? 'selected' : '' }}>Beklemede</option>
                        <option value="closed" {{ $chatRoom->status == 'closed' ? 'selected' : '' }}>Kapalı</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Öncelik</label>
                    <select name="priority" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="low" {{ $chatRoom->priority == 'low' ? 'selected' : '' }}>Düşük</option>
                        <option value="normal" {{ $chatRoom->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ $chatRoom->priority == 'high' ? 'selected' : '' }}>Yüksek</option>
                        <option value="urgent" {{ $chatRoom->priority == 'urgent' ? 'selected' : '' }}>Acil</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Güncelle
                </button>
            </form>
        </div>

        <!-- Assign -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Atama</h3>
            <form method="POST" action="{{ route('admin.chat.assign', $chatRoom) }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Atanan Admin</label>
                    <select name="assigned_to" class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Atanmamış</option>
                        @foreach(\App\Models\User::where('user_type', 'admin')->get() as $admin)
                            <option value="{{ $admin->id }}" {{ $chatRoom->assigned_to == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Ata
                </button>
            </form>
        </div>

        <!-- Customer Info -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Müşteri Bilgileri</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <span class="font-medium">Ad:</span> {{ $chatRoom->name ?? $chatRoom->user->name ?? 'Misafir' }}
                </div>
                @if($chatRoom->email)
                    <div>
                        <span class="font-medium">E-posta:</span> {{ $chatRoom->email }}
                    </div>
                @endif
                @if($chatRoom->phone)
                    <div>
                        <span class="font-medium">Telefon:</span> {{ $chatRoom->phone }}
                    </div>
                @endif
                @if($chatRoom->user)
                    <div>
                        <a href="{{ route('admin.customers.show', $chatRoom->user) }}" class="text-blue-600 hover:text-blue-800">
                            Müşteri Detayı →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">İşlemler</h3>
            <form method="POST" action="{{ route('admin.chat.close', $chatRoom) }}" onsubmit="return confirm('Bu chat\'i kapatmak istediğinize emin misiniz?')">
                @csrf
                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 text-sm">
                    Chat'i Kapat
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messageInput = document.getElementById('messageInput');
    const chatMessages = document.getElementById('chatMessages');
    
    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = messageInput.value.trim();
            if (!message) return;
            
            // Add message to UI immediately
            addMessageToUI(message, 'admin');
            messageInput.value = '';
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Send message to server
            fetch('{{ route('admin.chat.send-message', $chatRoom) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Message sent successfully
                } else {
                    console.error('Error:', data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    }
    
    // Poll for new messages
    let lastMessageId = {{ $messages->max('id') ?? 0 }};
    
    setInterval(function() {
        fetch('{{ route('admin.chat.get-messages', $chatRoom) }}?last_message_id=' + lastMessageId)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(function(msg) {
                        addMessageToUI(msg.message, 'user', msg.created_at);
                        lastMessageId = Math.max(lastMessageId, msg.id);
                    });
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
                if (data.last_message_id) {
                    lastMessageId = data.last_message_id;
                }
            })
            .catch(error => console.error('Error:', error));
    }, 3000); // Poll every 3 seconds
    
    function addMessageToUI(message, senderType, createdAt) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'mb-4 flex ' + (senderType == 'admin' ? 'justify-end' : 'justify-start');
        
        const now = new Date();
        const timeStr = createdAt ? new Date(createdAt).toLocaleString('tr-TR') : now.toLocaleString('tr-TR');
        
        messageDiv.innerHTML = `
            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                <div class="rounded-lg p-3 sm:p-4 ${senderType == 'admin' ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200'}">
                    <p class="text-sm sm:text-base whitespace-pre-wrap">${message}</p>
                    <p class="text-xs mt-2 ${senderType == 'admin' ? 'text-white/70' : 'text-gray-500'}">
                        ${timeStr}
                    </p>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageDiv);
    }
    
    // Scroll to bottom on load
    chatMessages.scrollTop = chatMessages.scrollHeight;
});
</script>
@endsection

