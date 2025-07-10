<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get or create watch later playlist
        $watchLater = $user->watchLaterPlaylist();
        if (!$watchLater) {
            $watchLater = Playlist::create([
                'name' => 'Watch Later',
                'user_id' => $user->id,
                'is_watch_later' => true,
                'visibility' => 'private'
            ]);
        }
        
        // Get regular playlists
        $playlists = $user->regularPlaylists()->latest()->get();
        
        return view('playlists', compact('watchLater', 'playlists'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:public,unlisted,private'
        ]);

        $playlist = Playlist::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
            'visibility' => $request->visibility,
            'is_watch_later' => false
        ]);

        return redirect()->route('playlists')->with('success', 'Playlist created successfully!');
    }

    public function show(Playlist $playlist)
    {
        // Check if user can view this playlist
        if ($playlist->visibility === 'private' && $playlist->user_id !== Auth::id()) {
            abort(403);
        }

        $playlist->load(['videos.user', 'user']);
        
        return view('playlist-show', compact('playlist'));
    }

    public function edit(Playlist $playlist)
    {
        // Check if user owns the playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        // Don't allow editing watch later playlist name
        if ($playlist->is_watch_later) {
            return redirect()->route('playlists')->with('error', 'Cannot edit Watch Later playlist');
        }

        return view('playlist-edit', compact('playlist'));
    }

    public function update(Request $request, Playlist $playlist)
    {
        // Check if user owns the playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        // Don't allow editing watch later playlist
        if ($playlist->is_watch_later) {
            return redirect()->route('playlists')->with('error', 'Cannot edit Watch Later playlist');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:public,unlisted,private'
        ]);

        $playlist->update([
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility
        ]);

        return redirect()->route('playlist.show', $playlist)->with('success', 'Playlist updated successfully!');
    }

    public function addVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'playlist_id' => 'required|exists:playlists,id'
        ]);

        $playlist = Playlist::findOrFail($request->playlist_id);
        $video = Video::findOrFail($request->video_id);

        // Check if user owns the playlist
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check if video is already in playlist
        if ($playlist->videos()->where('video_id', $video->id)->exists()) {
            return response()->json(['error' => 'Video already in playlist'], 400);
        }

        // Add video to playlist
        $nextOrder = $playlist->videos()->max('playlist_videos.order_position') + 1;
        $playlist->videos()->attach($video->id, [
            'order_position' => $nextOrder,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Added to {$playlist->name}"
        ]);
    }

    public function addToWatchLater(Video $video)
    {
        $user = Auth::user();
        
        // Get or create watch later playlist
        $watchLater = $user->watchLaterPlaylist();
        if (!$watchLater) {
            $watchLater = Playlist::create([
                'name' => 'Watch Later',
                'user_id' => $user->id,
                'is_watch_later' => true,
                'visibility' => 'private'
            ]);
        }

        // Check if video is already in watch later
        if ($watchLater->videos()->where('video_id', $video->id)->exists()) {
            return response()->json(['error' => 'Video already in Watch Later'], 400);
        }

        // Add video to watch later
        $nextOrder = $watchLater->videos()->max('playlist_videos.order_position') + 1;
        $watchLater->videos()->attach($video->id, [
            'order_position' => $nextOrder,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to Watch Later'
        ]);
    }

    public function removeVideo(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:videos,id',
            'playlist_id' => 'required|exists:playlists,id'
        ]);

        $playlist = Playlist::findOrFail($request->playlist_id);

        // Check if user owns the playlist
        if ($playlist->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $playlist->videos()->detach($request->video_id);

        return response()->json([
            'success' => true,
            'message' => 'Removed from playlist'
        ]);
    }

    public function getUserPlaylists()
    {
        $user = Auth::user();
        $playlists = $user->regularPlaylists()->select('id', 'name')->get();
        
        return response()->json($playlists);
    }

    public function getVideoPlaylists($videoId)
    {
        $user = Auth::user();
        $video = Video::findOrFail($videoId);
        
        // Get playlist IDs that this video is in for this user
        $playlistIds = $user->playlists()
            ->whereHas('videos', function($query) use ($videoId) {
                $query->where('video_id', $videoId);
            })
            ->pluck('id')
            ->toArray();
        
        // Also get the watch later playlist ID for reference
        $watchLaterPlaylist = $user->watchLaterPlaylist();
        $watchLaterPlaylistId = $watchLaterPlaylist ? $watchLaterPlaylist->id : null;
        
        return response()->json([
            'playlist_ids' => $playlistIds,
            'watch_later_id' => $watchLaterPlaylistId
        ]);
    }

    public function destroy(Playlist $playlist)
    {
        // Check if user owns the playlist
        if ($playlist->user_id !== Auth::id()) {
            abort(403);
        }

        // Don't allow deleting watch later playlist
        if ($playlist->is_watch_later) {
            return redirect()->route('playlists')->with('error', 'Cannot delete Watch Later playlist');
        }

        $playlist->delete();

        return redirect()->route('playlists')->with('success', 'Playlist deleted successfully!');
    }
}
