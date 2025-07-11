<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Video $video)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);
        $comment = $video->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        $comment->load('user');
        // Notify video owner
        if ($video->user_id !== Auth::id()) {
            \App\Models\Notification::create([
                'user_id' => $video->user_id,
                'data' => [
                    'type' => 'comment',
                    'video_id' => $video->id,
                    'commenter_id' => Auth::id(),
                    'commenter_name' => Auth::user()->name,
                    'body' => $request->body,
                ],
            ]);
        }
        if ($request->wantsJson()) {
            return response()->json(['comment' => $comment]);
        }
        return back();
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        $comment->delete();
        return response()->json(['success' => true]);
    }
}
