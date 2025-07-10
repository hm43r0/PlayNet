<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;
use App\Models\Video;
use App\Models\VideoHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// Public homepage route (shows all videos, does not require auth)
Route::get('/', [VideoController::class, 'all'])->name('home');

Route::middleware('auth')->group(function () {

    // Sidebar navigation routes
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'index'])->name('subscriptions');

    Route::get('/playlists', [\App\Http\Controllers\PlaylistController::class, 'index'])->name('playlists');
    Route::post('/playlists', [\App\Http\Controllers\PlaylistController::class, 'store'])->name('playlists.store');
    Route::get('/playlist/{playlist}', [\App\Http\Controllers\PlaylistController::class, 'show'])->name('playlist.show');
    Route::get('/playlist/{playlist}/edit', [\App\Http\Controllers\PlaylistController::class, 'edit'])->name('playlist.edit');
    Route::put('/playlist/{playlist}', [\App\Http\Controllers\PlaylistController::class, 'update'])->name('playlist.update');
    Route::delete('/playlist/{playlist}', [\App\Http\Controllers\PlaylistController::class, 'destroy'])->name('playlist.destroy');
    
    // Playlist video management
    Route::post('/playlist/add-video', [\App\Http\Controllers\PlaylistController::class, 'addVideo'])->name('playlist.add-video');
    Route::post('/playlist/remove-video', [\App\Http\Controllers\PlaylistController::class, 'removeVideo'])->name('playlist.remove-video');
    Route::post('/watch-later/{video}', [\App\Http\Controllers\PlaylistController::class, 'addToWatchLater'])->name('watch-later.add');
    Route::get('/api/user-playlists', [\App\Http\Controllers\PlaylistController::class, 'getUserPlaylists'])->name('api.user-playlists');
    Route::get('/api/video-playlists/{video}', [\App\Http\Controllers\PlaylistController::class, 'getVideoPlaylists'])->name('api.video-playlists');

    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/video/{video}', [VideoController::class, 'show'])->name('video.show');
    Route::get('/watchlater', function () {
        $user = Auth::user();
        $watchLater = $user->watchLaterPlaylist();
        
        if (!$watchLater) {
            $watchLater = \App\Models\Playlist::create([
                'name' => 'Watch Later',
                'user_id' => $user->id,
                'is_watch_later' => true,
                'visibility' => 'private'
            ]);
        }
        
        return redirect()->route('playlist.show', $watchLater);
    })->name('watchlater');
    Route::get('/liked', function () {
        $user = Auth::user();
        $videos = $user->likedVideos()->with('user')->orderByDesc('video_likes.created_at')->get();
        $grouped = $videos->groupBy(function($video) {
            $date = $video->pivot->created_at;
            if (!$date) return 'Unknown'; // Handle null dates
            if ($date->isToday()) return 'Today';
            if ($date->isYesterday()) return 'Yesterday';
            if ($date->greaterThanOrEqualTo(now()->startOfWeek())) return 'This Week';
            if ($date->greaterThanOrEqualTo(now()->startOfMonth())) return 'This Month';
            if ($date->greaterThanOrEqualTo(now()->startOfYear())) return 'This Year';
            return $date->format('Y');
        });
        return view('liked', compact('grouped'));
    })->name('liked');
    Route::get('/history', function () {
        $user = Auth::user();
        $history = VideoHistory::with('video.user')
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();
        $grouped = $history->groupBy(function($item) {
            $date = $item->created_at;
            if ($date->isToday()) return 'Today';
            if ($date->isYesterday()) return 'Yesterday';
            if ($date->greaterThanOrEqualTo(now()->startOfWeek())) return 'This Week';
            if ($date->greaterThanOrEqualTo(now()->startOfMonth())) return 'This Month';
            if ($date->greaterThanOrEqualTo(now()->startOfYear())) return 'This Year';
            return $date->format('Y');
        });
        return view('history', compact('grouped'));
    })->name('history');
    Route::post('/videos/{video}/like', [\App\Http\Controllers\VideoController::class, 'like'])->name('videos.like');
    Route::post('/videos/{video}/unlike', [\App\Http\Controllers\VideoController::class, 'unlike'])->name('videos.unlike');
    Route::post('/videos/{video}/dislike', [\App\Http\Controllers\VideoController::class, 'dislike'])->name('videos.dislike');
    Route::post('/videos/{video}/undislike', [\App\Http\Controllers\VideoController::class, 'undislike'])->name('videos.undislike');

    Route::post('/channels/{user}/subscribe', [\App\Http\Controllers\SubscriptionController::class, 'subscribe'])->name('channels.subscribe');
    Route::post('/channels/{user}/unsubscribe', [\App\Http\Controllers\SubscriptionController::class, 'unsubscribe'])->name('channels.unsubscribe');

    // Account switching routes
    Route::post('/switch-account/{user}', [\App\Http\Controllers\AccountSwitchController::class, 'switch'])->name('account.switch');
    Route::get('/link-account', [\App\Http\Controllers\AccountSwitchController::class, 'showLinkForm'])->name('account.link.form');
    Route::post('/link-account', [\App\Http\Controllers\AccountSwitchController::class, 'linkAccount'])->name('account.link');
    Route::post('/unlink-account/{user}', [\App\Http\Controllers\AccountSwitchController::class, 'unlinkAccount'])->name('account.unlink');
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Video edit, update, and delete routes
Route::get('/videos/{video}/edit', [\App\Http\Controllers\VideoController::class, 'edit'])->name('videos.edit');
Route::put('/videos/{video}', [\App\Http\Controllers\VideoController::class, 'update'])->name('videos.update');
Route::delete('/videos/{video}', [\App\Http\Controllers\VideoController::class, 'destroy'])->name('videos.destroy');
