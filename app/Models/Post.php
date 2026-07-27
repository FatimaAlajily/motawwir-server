<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected $casts = [
        'skill' => 'array',
    ];

      public function getFileUrlAttribute(): ?string
    {
        return $this->file
            ? url(Storage::url($this->file))
            : null;
    }

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
