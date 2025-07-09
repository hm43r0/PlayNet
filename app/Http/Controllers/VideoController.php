<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
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
}
