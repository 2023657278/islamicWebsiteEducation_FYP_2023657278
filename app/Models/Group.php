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
     * 🟢 FIXED COLUMN: Uses ->year to grab '2025' instead of ->year_name
     * Generates: "4 Amanah (2025)"
     */
    public function getGroupWithYearAttribute()
    {
        if ($this->year) {
            return "{$this->group_name} ({$this->year->year})";
        }

        return $this->group_name;
    }
}