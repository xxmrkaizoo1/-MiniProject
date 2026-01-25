<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';
    protected $fillable = [
        'subject',
        'rating',
        'mood_rating',
        'comments',
        'is_anonymous',
        'user_id',
    ];
}
