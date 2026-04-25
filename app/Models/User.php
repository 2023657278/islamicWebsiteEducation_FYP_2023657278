<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * UPDATED: Added new columns for MRSM System
     */
    protected $fillable = [
        'name',
        'email',
        'no_maktab',
        'password',
        'role',              // 'student', 'teacher', 'admin'
        'phone_number',
        'telegram_chat_id',  // For Bot Notifications
        'verification_code', // For Telegram Handshake
        'mrsm_id',           // Optional Student ID
        'group_id',          // Class ID
        'profile_image',
        'last_login_at',     // For Analytics
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
    ];

    // ==========================================
    //  RELATIONSHIPS
    // ==========================================

    // 1. Relationship to Group (Class)
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    // 2. ✅ FIXED: Relationship to QuizAttempts
    // This is what was missing and causing your error!
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class, 'user_id');
    }

    // ==========================================
    //  ACCESSORS (Your Custom Logic)
    // ==========================================

    // Accessor for Profile Image URL
    public function getProfileImageUrlAttribute()
    {
        if ($this->profile_image && Storage::exists('public/profile_images/' . $this->profile_image)) {
            return asset('storage/profile_images/' . $this->profile_image);
        }
        // Return a default placeholder if no image exists
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    // Accessor for Initials
    public function getAvatarInitialsAttribute()
    {
        $nameParts = explode(' ', $this->name);
        $initials = '';
        
        if (isset($nameParts[0])) $initials .= strtoupper(substr($nameParts[0], 0, 1));
        if (isset($nameParts[1])) $initials .= strtoupper(substr($nameParts[1], 0, 1));
        
        return $initials ?: strtoupper(substr($this->name, 0, 2));
    }
}