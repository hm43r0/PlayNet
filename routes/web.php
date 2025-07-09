<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// A protected route for authenticated users to land on after login/registration.
// This also provides a simple way to test the logout functionality.
Route::get('/dashboard', function () {
    return '<h1>Dashboard</h1><p>Welcome, ' . auth()->user()->name . '!</p><form method="POST" action="' . route('logout') . '">@csrf<button type="submit" class="btn btn-danger">Logout</button></form>';
})->middleware('auth')->name('dashboard');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login']);
    Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [AuthController::class, 'register']);
});

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
