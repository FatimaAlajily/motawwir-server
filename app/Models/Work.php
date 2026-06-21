<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    protected $fillable = [
        'location',
        'salary_range',
        'work_place',
        'contact',
        'hours',
    ];

    public function post(){
        return $this->belongsTo(Post::class);
    }

}
