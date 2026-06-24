<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'duration_minutes', 'teacher_id', 'subject_id', 'quiz_id', 'topic', 'difficulty',
    ];

    protected static function booted()
    {
        static::addGlobalScope('hideGlobalBankFromStudents', function (Builder $builder) {
            // 🟢 SECURITY BOUNDARY: Strip the backend container out of student views automatically
            if (!Auth::check() || Auth::user()->role !== 'admin') {
                $builder->where('topic', '!=', 'GLOBAL_BANK');
            }
        });
    }

    public function teacher() { return $this->belongsTo(User::class, 'teacher_id'); }
    public function subject() { return $this->belongsTo(Subject::class); }
    public function results() { return $this->hasMany(Result::class); }

    // 🟢 CHANGE THIS: From hasMany to belongsToMany
    // app/Models/Quiz.php

public function questions() 
{ 
    // 🟢 CHANGE THIS from hasMany to belongsToMany
    // This tells Laravel to use the 'quiz_question' pivot table
    return $this->belongsToMany(Question::class, 'quiz_question'); 
}
}