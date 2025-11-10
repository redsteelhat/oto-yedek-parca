<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseAdminController;
use App\Models\ChatRoom;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends BaseAdminController
{
    /**
     * Display list of chat rooms.
     */
    public function index(Request $request)
    {
        $query = ChatRoom::with(['user', 'assignedAdmin', 'latestMessage']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        // Filter by assigned admin
        if ($request->has('assigned_to')) {
            if ($request->assigned_to === 'me') {
                $query->where('assigned_to', Auth::id());
            } elseif ($request->assigned_to === 'unassigned') {
                $query->whereNull('assigned_to');
            } elseif ($request->assigned_to) {
                $query->where('assigned_to', $request->assigned_to);
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        // Sort
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'latest':
                $query->orderBy('last_message_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('last_message_at', 'asc');
                break;
            case 'unread':
                $query->orderBy('unread_count_admin', 'desc');
                break;
            default:
                $query->latest();
        }

        $chatRooms = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ChatRoom::count(),
            'open' => ChatRoom::where('status', 'open')->count(),
            'pending' => ChatRoom::where('status', 'pending')->count(),
            'closed' => ChatRoom::where('status', 'closed')->count(),
            'unread' => ChatRoom::where('unread_count_admin', '>', 0)->count(),
            'assigned_to_me' => ChatRoom::where('assigned_to', Auth::id())->count(),
        ];

        return view('admin.chat.index', compact('chatRooms', 'stats'));
    }

    /**
     * Show chat room details.
     */
    public function show(ChatRoom $chatRoom)
    {
        // Mark messages as read for admin
        $chatRoom->markAsReadForAdmin();

        // Assign to current admin if not assigned
        if (!$chatRoom->assigned_to) {
            $chatRoom->update(['assigned_to' => Auth::id()]);
        }

        $messages = $chatRoom->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $chatRoom->load(['user', 'assignedAdmin']);

        return view('admin.chat.show', compact('chatRoom', 'messages'));
    }

    /**
     * Send message as admin.
     */
    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $validated = $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        // Get current tenant ID
        $tenantId = $this->getCurrentTenantId();
        
        $message = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'tenant_id' => $tenantId,
            'user_id' => Auth::id(),
            'sender_type' => 'admin',
            'message' => $validated['message'],
        ]);

        // Update chat room
        $chatRoom->update([
            'last_message_at' => now(),
            'unread_count_user' => $chatRoom->unread_count_user + 1,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        }

        return redirect()->back()->with('success', 'Mesaj gönderildi.');
    }

    /**
     * Update chat room status.
     */
    public function updateStatus(Request $request, ChatRoom $chatRoom)
    {
        $validated = $request->validate([
            'status' => 'required|in:open,closed,pending',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $chatRoom->update($validated);

        return redirect()->back()->with('success', 'Chat durumu güncellendi.');
    }

    /**
     * Assign chat room to admin.
     */
    public function assign(Request $request, ChatRoom $chatRoom)
    {
        $validated = $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $chatRoom->update(['assigned_to' => $validated['assigned_to']]);

        return redirect()->back()->with('success', 'Chat atandı.');
    }

    /**
     * Get new messages (AJAX polling).
     */
    public function getMessages(ChatRoom $chatRoom, Request $request)
    {
        $lastMessageId = $request->get('last_message_id', 0);

        $messages = $chatRoom->messages()
            ->where('id', '>', $lastMessageId)
            ->where('sender_type', 'user') // Only user messages
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark as read
        if ($messages->count() > 0) {
            $chatRoom->markAsReadForAdmin();
        }

        return response()->json([
            'messages' => $messages,
            'last_message_id' => $chatRoom->messages()->max('id') ?? 0,
        ]);
    }

    /**
     * Close chat room.
     */
    public function close(ChatRoom $chatRoom)
    {
        $chatRoom->update(['status' => 'closed']);

        return redirect()->route('admin.chat.index')
            ->with('success', 'Chat kapatıldı.');
    }
}

