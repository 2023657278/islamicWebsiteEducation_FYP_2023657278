<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'group_id', 
        'subject_id', 
        'day_id', 
        'year_id', 
        // 'user_id' removed here
        'time_from', 
        'time_to'
    ];

    // Relationship to get the Teacher's name
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function day()
    {
        return $this->belongsTo(Day::class, 'day_id');
    }

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }
}