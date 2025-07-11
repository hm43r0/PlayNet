<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $history_paused
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany likedVideos()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany dislikedVideos()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany subscribers()
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany subscriptions()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany playlists()
 * @method \App\Models\Playlist watchLaterPlaylist()
 * @method \Illuminate\Database\Eloquent\Relations\HasMany regularPlaylists()
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'history_paused',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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
            'history_paused' => 'boolean',
        ];
    }

    /**
     * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany likedVideos()
     */
    public function likedVideos()
    {
        return $this->belongsToMany(Video::class, 'video_likes')->withTimestamps();
    }

    public function dislikedVideos()
    {
        return $this->belongsToMany(Video::class, 'video_dislikes')->withTimestamps();
    }
    /**
     * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany subscribers()
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'channel_id', 'user_id');
    }
    /**
     * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany subscriptions()
     */
    public function subscriptions()
    {
        return $this->belongsToMany(User::class, 'subscriptions', 'user_id', 'channel_id');
    }

    // Linked accounts relationships
    public function linkedAccounts()
    {
        return $this->belongsToMany(User::class, 'linked_accounts', 'primary_user_id', 'linked_user_id');
    }

    public function linkedByAccounts()
    {
        return $this->belongsToMany(User::class, 'linked_accounts', 'linked_user_id', 'primary_user_id');
    }

    // Get all accounts that this user can switch to
    public function getAllLinkedAccounts()
    {
        $linked = $this->linkedAccounts;
        $linkedBy = $this->linkedByAccounts;
        
        return $linked->merge($linkedBy)->unique('id');
    }

    // Playlist relationships
    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }

    public function watchLaterPlaylist()
    {
        return $this->playlists()->watchLater()->first();
    }

    public function regularPlaylists()
    {
        return $this->playlists()->regular();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }
}
