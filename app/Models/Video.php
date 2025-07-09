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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'video_likes');
    }

    public function dislikes()
    {
        return $this->belongsToMany(User::class, 'video_dislikes');
    }

    // Get video duration in H:i:s format
    public function getDurationAttribute()
    {
        $path = storage_path('app/public/' . $this->video_path);
        if (!file_exists($path)) return null;
        try {
            // Use ffprobe to get duration in seconds
            $duration = null;
            $output = null;
            @exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($path), $output);
            if (isset($output[0])) {
                $seconds = (int) round($output[0]);
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $secs = $seconds % 60;
                if ($hours > 0) {
                    return sprintf('%d:%02d:%02d', $hours, $minutes, $secs);
                } else {
                    return sprintf('%d:%02d', $minutes, $secs);
                }
            }
        } catch (\Exception $e) {}
        return null;
    }
}
