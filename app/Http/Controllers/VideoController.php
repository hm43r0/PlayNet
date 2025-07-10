<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class VideoController extends Controller
{
    use AuthorizesRequests;

    // Show all videos for homepage (public)
    public function all()
    {
        $videos = Video::with('user')->latest()->get();
        return view('welcome', compact('videos'));
    }

    public function index()
    {
        $user = Auth::user();
        $videos = Video::with('user')->where('user_id', $user->id)->latest()->get();
        return view('videos', compact('videos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => [
                'required',
                'mimes:jpeg,png,jpg,gif,webp,avif',
                'max:10240', // 10MB max for thumbnail
            ],
            'video' => 'required|mimes:mp4,mov,avi,webm|max:51200', // 50MB max
        ]);

        $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
        $videoPath = $request->file('video')->store('videos', 'public');

        $video = Video::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_path' => $thumbnailPath,
            'video_path' => $videoPath,
        ]);

        return redirect()->route('videos.index')->with('success', 'Video uploaded successfully!');
    }

        // Show a single video player page
    public function show(Video $video)
    {
        $video->load('user');
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Record video history for authenticated users
        if ($user) {
            // Check if this video was viewed recently (within last hour) to prevent spam
            $existingHistory = VideoHistory::where('user_id', $user->id)
                ->where('video_id', $video->id)
                ->where('created_at', '>=', now()->subHour())
                ->first();
            
            // If not viewed recently, create a new history entry
            if (!$existingHistory) {
                VideoHistory::create([
                    'user_id' => $user->id,
                    'video_id' => $video->id,
                ]);
            }
        }
        
        $liked = $user ? $video->likes()->where('user_id', $user->id)->exists() : false;
        $likeCount = $video->likes()->count();
        $disliked = $user ? $video->dislikes()->where('user_id', $user->id)->exists() : false;
        $dislikeCount = $video->dislikes()->count();

        // Subscription logic
        $subscribed = false;
        if ($user && $user->id !== $video->user_id) {
            $subscribed = $user->subscriptions()->where('channel_id', $video->user->id)->exists();
        }
        $subscribersCount = $video->user->subscribers()->count();

        return view('video', compact('video', 'liked', 'likeCount', 'disliked', 'dislikeCount', 'subscribed', 'subscribersCount'));
    }

    public function like(Video $video)
    {
        $user = Auth::user();
        if (!$video->likes()->where('user_id', $user->id)->exists()) {
            $video->likes()->attach($user->id);
        }
        return response()->json(['liked' => true, 'count' => $video->likes()->count()]);
    }

    public function unlike(Video $video)
    {
        $user = Auth::user();
        $video->likes()->detach($user->id);
        return response()->json(['liked' => false, 'count' => $video->likes()->count()]);
    }

    public function dislike(Video $video)
    {
        $user = Auth::user();
        if (!$video->dislikes()->where('user_id', $user->id)->exists()) {
            $video->dislikes()->attach($user->id);
        }
        // Remove like if exists
        $video->likes()->detach($user->id);
        return response()->json([
            'disliked' => true,
            'count' => $video->dislikes()->count(),
            'likeCount' => $video->likes()->count(),
        ]);
    }

    public function undislike(Video $video)
    {
        $user = Auth::user();
        $video->dislikes()->detach($user->id);
        return response()->json([
            'disliked' => false,
            'count' => $video->dislikes()->count(),
            'likeCount' => $video->likes()->count(),
        ]);
    }

    public function edit(Video $video)
    {
        $this->authorize('update', $video); // Only owner can edit
        return view('video-edit', compact('video'));
    }

    public function update(Request $request, Video $video)
    {
        $this->authorize('update', $video);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => [
                'nullable',
                'mimes:jpeg,png,jpg,gif,webp,avif',
                'max:10240', // 10MB max
            ],
        ]);
        $video->title = $request->title;
        $video->description = $request->description;
        if ($request->hasFile('thumbnail')) {
            $video->thumbnail_path = $request->file('thumbnail')->store('thumbnails', 'public');
        }
        $video->save();
        return redirect()->route('videos.index')->with('success', 'Video updated successfully!');
    }

    public function destroy(Video $video)
    {
        $this->authorize('delete', $video);
        $video->delete();
        return redirect()->route('videos.index')->with('success', 'Video deleted successfully!');
    }
}
