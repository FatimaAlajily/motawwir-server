<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'user_name',
    'email',
    'password',
    'avatar',
    'role',
    'votra',
    'is_banned',
    'ban_reason',
    'banned_at',

])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_banned' => 'boolean',
            'banned_at' => 'datetime',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar
            ? Storage::url($this->avatar)
            : asset('images/default-avatar.png');
    }


    public function savedPosts()
    {
        return $this->belongsToMany(Post::class, 'save_posts')
            ->withTimestamps();
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function profileComments()
    {
        return $this->hasMany(Comment::class, 'profile_user_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
