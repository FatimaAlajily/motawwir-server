<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'bio',
        'phone',
        'location',
        'skill',
        'github',
        'gmail',
        'domain',
        'cv',
        'linkedin',
        'user_id',
    ];

    protected $casts = [
        'skill' => 'array',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
