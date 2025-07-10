<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoHistory extends Model
{
    protected $table = 'video_history';
    protected $fillable = ['user_id', 'video_id'];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
