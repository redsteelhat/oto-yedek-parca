@extends('layouts.app')

@section('title', $chatRoom->subject)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 sm:py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Chat Header -->
        <div class="bg-primary-600 text-white p-4 sm:p-6">
            <div class="flex items-center justify-between mb-2">
                <h1 class="text-xl sm:text-2xl font-bold">{{ $chatRoom->subject }}</h1>
                <a href="{{ route('chat.index') }}" class="text-white hover:text-gray-200 text-sm">
                    ← Geri Dön
                </a>
            </div>
            <div class="flex items-center space-x-4 text-sm">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-white/20">
                    @if($chatRoom->status == 'open')
                        Açık
                    @elseif($chatRoom->status == 'closed')
                        Kapalı
                    @else
                        Beklemede
                    @endif
                </span>
                @if($chatRoom->assignedAdmin)
                    <span>Atanan: {{ $chatRoom->assignedAdmin->name }}</span>
                @endif
            </div>
        </div>

        <!-- Messages -->
        <div class="p-4 sm:p-6 h-96 overflow-y-auto bg-gray-50" id="chatMessages">
            @foreach($messages as $message)
                <div class="mb-4 flex {{ $message->sender_type == 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                        <div class="rounded-lg p-3 sm:p-4 {{ $message->sender_type == 'user' ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                            <p class="text-sm sm:text-base whitespace-pre-wrap">{{ $message->message }}</p>
                            <p class="text-xs mt-2 {{ $message->sender_type == 'user' ? 'text-white/70' : 'text-gray-500' }}">
                                {{ $message->created_at->format('d.m.Y H:i') }}
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
                <p>Bu chat kapatılmış. Yeni bir mesaj oluşturmak için <a href="{{ route('chat.create') }}" class="text-primary-600 hover:underline">buraya tıklayın</a>.</p>
            </div>
        @endif
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
            addMessageToUI(message, 'user');
            messageInput.value = '';
            
            // Scroll to bottom
            chatMessages.scrollTop = chatMessages.scrollHeight;
            
            // Send message to server
            fetch('{{ route('chat.send-message', $chatRoom) }}', {
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
        fetch('{{ route('chat.get-messages', $chatRoom) }}?last_message_id=' + lastMessageId)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(function(msg) {
                        addMessageToUI(msg.message, 'admin', msg.created_at);
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
        messageDiv.className = 'mb-4 flex ' + (senderType == 'user' ? 'justify-end' : 'justify-start');
        
        const now = new Date();
        const timeStr = createdAt ? new Date(createdAt).toLocaleString('tr-TR') : now.toLocaleString('tr-TR');
        
        messageDiv.innerHTML = `
            <div class="max-w-xs sm:max-w-md lg:max-w-lg">
                <div class="rounded-lg p-3 sm:p-4 ${senderType == 'user' ? 'bg-primary-600 text-white' : 'bg-white text-gray-900 border border-gray-200'}">
                    <p class="text-sm sm:text-base whitespace-pre-wrap">${message}</p>
                    <p class="text-xs mt-2 ${senderType == 'user' ? 'text-white/70' : 'text-gray-500'}">
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

