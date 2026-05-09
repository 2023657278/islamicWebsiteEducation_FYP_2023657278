<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomParticipant extends Model
{
    protected $fillable = ['room_id', 'user_id', 'hp', 'mp', 'last_rank', 'is_ready'];

    public function user() { 
        return $this->belongsTo(User::class); 
    }

    public function room() { 
        return $this->belongsTo(QuizRoom::class, 'room_id'); 
    }
}