<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SrsLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'srs_logs'; // Explicitly link table name

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'flashcard_id',
        'box_number',       // 1 to 5
        'ease_factor',      // 2.5 default
        'interval',         // Days or minutes until next review
        'repetition_count', // How many times reviewed
        'next_review_date'  // The calculated precise timestamp
    ];

    /**
     * The attributes that should be cast.
     * Ensuring this is 'datetime' is critical for the 1-minute timer logic.
     *
     * @var array
     */
    protected $casts = [
        'next_review_date' => 'datetime', // Critical for minute-level accuracy
        'ease_factor' => 'float',
        'interval' => 'integer',
        'repetition_count' => 'integer',
    ];

    /**
     * Relationship: A log belongs to a specific flashcard.
     */
    public function flashcard()
    {
        return $this->belongsTo(Flashcard::class); //
    }

    /**
     * Relationship: A log belongs to a specific user.
     */
    public function user()
    {
        return $this->belongsTo(User::class); //
    }
}