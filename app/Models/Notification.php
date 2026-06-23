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

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function vote() {
    return $this->belongsTo(Vote::class);
    }

    public function comment() {
    return $this->belongsTo(Comment::class);
    }
    
}
