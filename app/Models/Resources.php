<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resources extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'file_url',        // ✅ This is the correct column now
        'type',            // 'note' or 'video' or 'textbook'
        'teacher_id',
        'group_id',
        'subject_id',
        'is_public',
        'youtube_video_id', // Optional backup
        'description'
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    // 1. Link to the Group (Class)
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    // 2. Link to the Subject (PAI)
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // 3. Link to the Teacher (Uploader)
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}