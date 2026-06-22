<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type', 
        'is_read', 
        'user_id', 
        'vote_id', 
        'comment_id'
    ];
}
