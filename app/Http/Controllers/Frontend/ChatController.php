<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display chat widget or full page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's chat rooms
        if ($user) {
            $chatRooms = ChatRoom::where('user_id', $user->id)
                ->with(['latestMessage', 'assignedAdmin'])
                ->orderBy('last_message_at', 'desc')
                ->get();
        } else {
            $chatRooms = collect();
        }

        return view('frontend.chat.index', compact('chatRooms'));
    }

    /**
     * Show specific chat room.
     */
    public function show(ChatRoom $chatRoom)
    {
        // Check if user has access to this chat room
        if (Auth::check() && $chatRoom->user_id !== Auth::id()) {
            abort(403);
        }

        // Mark messages as read for user
        $chatRoom->markAsReadForUser();

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('frontend.chat.show', compact('chatRoom', 'messages'));
    }

    /**
     * Create new chat room.
     */
    public function create()
    {
        return view('frontend.chat.create');
    }

    /**
     * Store new chat room.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:2000',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        // If user is authenticated, use user data
        $userId = Auth::id();
        if ($userId) {
            $user = Auth::user();
            $name = $user->name;
            $email = $user->email;
            $phone = $user->phone;
        } else {
            // Guest user - name, email, phone are required
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ]);
            $name = $validated['name'];
            $email = $validated['email'];
            $phone = $validated['phone'];
        }

        // Get current tenant ID
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
        
        // Create chat room
        $chatRoom = ChatRoom::create([
            'user_id' => $userId,
            'tenant_id' => $tenantId,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'subject' => $validated['subject'],
            'status' => 'open',
            'priority' => 'normal',
            'last_message_at' => now(),
        ]);

        // Create first message
        ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'sender_type' => 'user',
            'message' => $validated['message'],
        ]);

        // Update unread count for admin
        $chatRoom->update(['unread_count_admin' => 1]);

        return redirect()->route('chat.show', $chatRoom)
            ->with('success', 'Mesajınız gönderildi. En kısa sürede yanıtlanacaktır.');
    }

    /**
     * Send message in chat room.
     */
    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        // Check if user has access
        if (Auth::check() && $chatRoom->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        // Get current tenant ID
        $tenantId = app(\App\Services\TenantService::class)->getCurrentTenantId();
        
        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'sender_type' => Auth::check() ? 'user' : 'user',
            'message' => $validated['message'],
        ]);

        // Update chat room
        $chatRoom->update([
            'last_message_at' => now(),
            'unread_count_admin' => $chatRoom->unread_count_admin + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    /**
     * Get new messages (AJAX polling).
     */
    public function getMessages(ChatRoom $chatRoom, Request $request)
    {
        // Check if user has access
        if (Auth::check() && $chatRoom->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $chatRoom->messages()
            ->where('id', '>', $lastMessageId)
            ->where('sender_type', '!=', 'user') // Only admin messages
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        if ($messages->count() > 0) {
            $chatRoom->markAsReadForUser();
        }

        return response()->json([
            'messages' => $messages,
            'last_message_id' => $chatRoom->messages()->max('id') ?? 0,
        ]);
    }
}

