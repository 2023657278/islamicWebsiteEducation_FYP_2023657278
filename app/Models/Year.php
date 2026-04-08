<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    public $table = 'years';
    
   protected $fillable = ['year'];

public function groups()
{
    return $this->hasMany(Group::class, 'year_id');
}
}
