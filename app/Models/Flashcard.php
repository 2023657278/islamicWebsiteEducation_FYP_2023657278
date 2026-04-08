<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flashcard extends Model
{
    use HasFactory;

    protected $fillable = [
    'teacher_id',
    'subject_id',
    'question', // Matches your DB
    'answer',   // Matches your DB
    'topic',
    'quiz_id'   // <--- Added this
];

// Add this relationship so we can access Quiz details
public function quiz()
{
    return $this->belongsTo(Quiz::class);
}

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function srsLogs()
    {
        return $this->hasMany(SrsLog::class);
    }
}