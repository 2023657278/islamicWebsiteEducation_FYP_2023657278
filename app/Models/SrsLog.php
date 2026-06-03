<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrsLog extends Model
{
    use HasFactory;

    protected $table = 'srs_logs'; // Explicitly link table name

    protected $fillable = [
        'user_id',
        'flashcard_id',
        'box_number',       // 1 to 5
        'ease_factor',      // 2.5 default
        'interval',         // Days until next review
        'repetition_count', // How many times reviewed
        'next_review_date'  // The calculated date
    ];

    
    protected $casts = [
    'next_review_date' => 'datetime', // Changed from 'date' to 'datetime'
    ];

    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}