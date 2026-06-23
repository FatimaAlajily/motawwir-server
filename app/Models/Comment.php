<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'text',
        'type',
        'post_id', 
        'user_id'
        
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function votes() {
    return $this->hasMany(Vote::class);
    }

    public function notifications() {
    return $this->hasMany(Notification::class);
    }
    
}
