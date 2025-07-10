<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AccountSwitchController extends Controller
{
    public function switch(User $user)
    {
        $currentUser = Auth::user();
        
        // Check if the target user is linked to the current user
        $canSwitch = $currentUser->getAllLinkedAccounts()->contains('id', $user->id);
        
        if (!$canSwitch && $currentUser->id !== $user->id) {
            return redirect()->back()->with('error', 'You cannot switch to this account.');
        }
        
        // Store the original user in session for potential switching back
        if (!session('original_user_id')) {
            session(['original_user_id' => $currentUser->id]);
        }
        
        // Switch to the new user
        Auth::login($user);
        
        return redirect()->route('home')->with('success', "Switched to {$user->name}'s account.");
    }
    
    public function linkAccount(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required'
        ]);
        
        $currentUser = Auth::user();
        $targetUser = User::where('email', $request->email)->first();
        
        // Verify the password for the target account
        if (!Hash::check($request->password, $targetUser->password)) {
            return back()->withErrors(['password' => 'Invalid password for this account.']);
        }
        
        // Check if already linked
        $alreadyLinked = $currentUser->getAllLinkedAccounts()->contains('id', $targetUser->id);
        if ($alreadyLinked || $currentUser->id === $targetUser->id) {
            return back()->withErrors(['email' => 'This account is already linked or is the same account.']);
        }
        
        // Create bidirectional link using the attach method with current timestamp
        $currentUser->linkedAccounts()->attach($targetUser->id, [
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return back()->with('success', "Successfully linked {$targetUser->name}'s account.");
    }
    
    public function unlinkAccount(User $user)
    {
        $currentUser = Auth::user();
        
        // Remove bidirectional links
        $currentUser->linkedAccounts()->detach($user->id);
        $currentUser->linkedByAccounts()->detach($user->id);
        
        return back()->with('success', "Unlinked {$user->name}'s account.");
    }
    
    public function showLinkForm()
    {
        return view('auth.link-account');
    }
}
