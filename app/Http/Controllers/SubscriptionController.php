<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $channels = $user->subscriptions()->withCount('subscribers')->get();
        return view('subscriptions', compact('channels'));
    }

    public function subscribe(User $user)
    {
        $subscriber = Auth::user();
        if ($subscriber->id === $user->id) {
            return response()->json(['subscribed' => false, 'count' => $user->subscribers()->count(), 'error' => 'Cannot subscribe to yourself.']);
        }
        if (!$subscriber->subscriptions()->where('channel_id', $user->id)->exists()) {
            $subscriber->subscriptions()->attach($user->id);
        }
        return response()->json(['subscribed' => true, 'count' => $user->subscribers()->count()]);
    }

    public function unsubscribe(User $user)
    {
        $subscriber = Auth::user();
        $subscriber->subscriptions()->detach($user->id);
        return response()->json(['subscribed' => false, 'count' => $user->subscribers()->count()]);
    }
}
