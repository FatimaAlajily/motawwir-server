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
    ];
}
