<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'content',
        'file',
        'skill',
        'primary_link',
        'secondary_link',
        'type',
        'user_id'
    ];

    public function savedByUsers()
    {
        return $this->belongsToMany(User::class, 'save_posts')
            ->withTimestamps();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function work()
    {
        return $this->hasOne(Work::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
