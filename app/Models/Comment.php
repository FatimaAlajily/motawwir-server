<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'text',
        'type',
        'user_id',
        'post_id',
        'profile_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function profileUser()
    {
    return $this->belongsTo(User::class, 'profile_user_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }
}
