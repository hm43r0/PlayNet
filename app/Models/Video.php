<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'thumbnail_path',
        'video_path',
        'views',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'video_likes')->withTimestamps();
    }

    public function dislikes()
    {
        return $this->belongsToMany(User::class, 'video_dislikes')->withTimestamps();
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_videos')
                    ->withPivot('order_position')
                    ->withTimestamps();
    }

    public function history()
    {
        return $this->hasMany(\App\Models\VideoHistory::class);
    }

    public function getFormattedViewsAttribute()
    {
        return number_format($this->views);
    }
}
