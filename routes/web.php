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

    Route::get('/playlists', function () {
        return view('playlists');
    })->name('playlists');

    Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
    Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
    Route::get('/video/{video}', [VideoController::class, 'show'])->name('video.show');
    Route::get('/watchlater', function () {
        return view('watchlater');
    })->name('watchlater');
    Route::get('/liked', function () {
        $user = Auth::user();
        $videos = $user->likedVideos()->with('user')->orderByDesc('video_likes.created_at')->get();
        $grouped = $videos->groupBy(function($video) {
            $date = $video->pivot->created_at;
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
});

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
