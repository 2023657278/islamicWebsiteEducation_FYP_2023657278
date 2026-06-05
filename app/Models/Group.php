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

    public function timetables()
    {
        return $this->hasMany(Timetable::class, 'group_id');
    }

    /**
     * 🟢 ACCESSED VIA: $group->group_with_year
     * Generates "4Amanah (2025)" safely without changing any database column structures.
     */
    public function getGroupWithYearAttribute()
    {
        if ($this->year) {
            return "{$this->group_name} ({$this->year->year_name})";
        }

        return $this->group_name;
    }
}