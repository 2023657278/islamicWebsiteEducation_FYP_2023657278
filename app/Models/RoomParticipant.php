<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomParticipant extends Model
{
    // 🟢 Added status, shielded, frozen, boost, and frozen_until to fillable
    protected $fillable = [
    'room_id', 'user_id', 'hp', 'mp', 'last_rank', 'is_ready', 
    'status', 'is_shielded', 'is_frozen', 'active_boost', 'frozen_until',
    'skills_locked_turns', 'strike_locked_until' // 🟢 Added these two
    ];

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function room() { 
        return $this->belongsTo(QuizRoom::class, 'room_id'); 
    }
}