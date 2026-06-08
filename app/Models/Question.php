<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id', // Kept for safety or fallback if your migration still has it
        'subject_id', 
        'difficulty', 
        'question_text', 
        'points', 
        'question_type',
        'correct_answer_text'
    ];

    // 🟢 The true Many-to-Many pivot table relationship
    public function quizzes() { 
        return $this->belongsToMany(Quiz::class, 'quiz_question'); 
    }

    // 🟢 HELPER RELATIONSHIP: Fetches the first associated quiz so your templates don't break!
    public function quiz()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question')->latest()->limit(1);
    }

    public function options() { 
        return $this->hasMany(Option::class); 
    }
}