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

    protected static function booted()
    {
        static::addGlobalScope('hideReservoirFromStudents', function (Builder $builder) {
            // 🟢 SECURITY BOUNDARY: If the user is a student (or unauthenticated guest portal browsing),
            // completely hide the master data row block from all collection loops!
            if (!Auth::check() || Auth::user()->role !== 'admin') { 
                $builder->where('subject_code', '!=', 'GLOBAL_RESERVOIR');
            }
        });
    }

    // ✅ ADD THIS FUNCTION
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}
