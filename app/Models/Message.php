<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    /**
     * Explicitly map your existing database table fields.
     */
    protected $fillable = [
        'sender_id', 
        'target_id', 
        'type',      // 'private', 'group', or 'global'
        'subject',   
        'message', 
        'is_read'    
    ];

    /**
     * Relationship: Link to the User who sent the message.
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relationship: Link to the User target (For private 1-to-1 messages).
     */
    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    /**
     * 🟢 ADDED: Link to the Group target (For class announcements/chat).
     * This links the message target_id to the groups table id.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'target_id');
    }
}