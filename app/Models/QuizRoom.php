<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizRoom extends Model
{
    protected $fillable = [
        'quiz_id', 
        'host_id', // 🟢 MAKE SURE THIS IS HERE
        'room_code', 
        'is_public',
        'status', 
        'current_question_index', 
        'question_expires_at'];

    public function quiz() { 
        return $this->belongsTo(Quiz::class); 
    }

    public function participants() { 
        return $this->hasMany(RoomParticipant::class, 'room_id'); 
    }

    public function host(){
    // Tells Laravel that host_id points to a User
    return $this->belongsTo(User::class, 'host_id');
    }
}