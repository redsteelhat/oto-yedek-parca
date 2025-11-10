<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatRoom extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'subject',
        'status',
        'priority',
        'assigned_to',
        'last_message_at',
        'unread_count_user',
        'unread_count_admin',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count_user' => 'integer',
        'unread_count_admin' => 'integer',
    ];

    /**
     * Get the user that owns the chat room.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin assigned to this chat room.
     */
    public function assignedAdmin()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the messages for the chat room.
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get the latest message.
     */
    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * Scope to get open chat rooms.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get closed chat rooms.
     */
    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    /**
     * Scope to get pending chat rooms.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get unread messages count for user.
     */
    public function getUnreadCountForUser()
    {
        if (!$this->user_id) {
            return 0;
        }
        return $this->messages()
            ->where('sender_type', '!=', 'user')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get unread messages count for admin.
     */
    public function getUnreadCountForAdmin()
    {
        return $this->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->count();
    }

    /**
     * Mark messages as read for user.
     */
    public function markAsReadForUser()
    {
        $this->messages()
            ->where('sender_type', '!=', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        $this->update(['unread_count_user' => 0]);
    }

    /**
     * Mark messages as read for admin.
     */
    public function markAsReadForAdmin()
    {
        $this->messages()
            ->where('sender_type', 'user')
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        
        $this->update(['unread_count_admin' => 0]);
    }
}

