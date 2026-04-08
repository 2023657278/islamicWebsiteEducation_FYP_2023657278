<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    public $table = 'subjects';
    
    protected $fillable = [
        'subject_code',
        'subject_name',
        
    ];

    // ✅ ADD THIS FUNCTION
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
