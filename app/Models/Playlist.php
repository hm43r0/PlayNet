<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Playlist extends Model
{
    protected $fillable = [
        'name',
        'description',
        'user_id',
        'visibility',
        'is_watch_later'
    ];

    protected $casts = [
        'is_watch_later' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'playlist_videos')
                    ->withPivot('order_position')
                    ->withTimestamps()
                    ->orderBy('playlist_videos.order_position');
    }

    public function getVideoCountAttribute()
    {
        return $this->videos()->count();
    }

    public function getFirstVideoThumbnailAttribute()
    {
        $firstVideo = $this->videos()->first();
        return $firstVideo ? $firstVideo->thumbnail_path : null;
    }

    // Scope for watch later playlist
    public function scopeWatchLater($query)
    {
        return $query->where('is_watch_later', true);
    }

    // Scope for regular playlists (non-watch later)
    public function scopeRegular($query)
    {
        return $query->where('is_watch_later', false);
    }
}
