<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    public $table = 'groups';
    
    protected $fillable = ['group_name', 'year_id'];

    public function year()
    {
        return $this->belongsTo(Year::class, 'year_id');
    }

    public function students()
    {
        return $this->hasMany(User::class, 'group_id')->where('role', 'student');
    }

    // ✅ ADD THIS MISSING RELATIONSHIP
    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'group_id');
    }
}