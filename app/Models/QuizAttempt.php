<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    // 1. Link to your database table
    protected $table = 'quiz_attempts';

    // 2. Allow these columns to be saved
    protected $fillable = [
        'user_id',
        'quiz_id',
        'score',
        'total_questions'
    ];

    // 3. Relationships (Optional but good to have)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }
}