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
    ];

    public function savedByUsers(){
        return $this->belongsToMany(User::class , 'save_posts')
        ->withTimestamps();
    }


    public function user(){
        return $this->belongsTo(User::class);
    }


    public function work(){
        return $this->hasOne(Work::class);
    }
           
}
