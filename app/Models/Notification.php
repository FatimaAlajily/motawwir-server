<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'is_read',
        'comment_id',
        'from_user_id',
        'vote_id',
        'user_id',


    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class , 'from_user_id');
    }

    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
