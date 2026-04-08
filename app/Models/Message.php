<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    // ✅ Match your existing table columns
    protected $fillable = [
        'sender_id', 
        'target_id', 
        'type',      // Your table has this column
        'subject',   // Your table has this column
        'message', 
        'is_read'    // Assuming you have this or similar
    ];

    // Link to Sender
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Link to Target (Receiver)
    public function target()
    {
        return $this->belongsTo(User::class, 'target_id');
    }
}