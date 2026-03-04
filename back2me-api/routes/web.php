<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Dashboard;
use App\Livewire\Notifications\Index as NotificationsIndex;
use App\Livewire\Objets\Index as ObjetsIndex;
use App\Livewire\Objets\Show as ObjetShow;
use App\Livewire\Claims\Pending as ClaimsPending;
use App\Livewire\Users\Index as UsersIndex;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/login', Login::class)->name('login')->middleware('guest');
Route::get('/register', Register::class)->name('register')->middleware('guest');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/objets', ObjetsIndex::class)->name('objets.index');
    Route::get('/objets/{objet}', ObjetShow::class)->name('objets.show');
    Route::get('/notifications', NotificationsIndex::class)->name('notifications.index');
    Route::get('/claims/pending', ClaimsPending::class)->name('claims.pending')->middleware('can:viewPending,App\Models\Claim');
    Route::get('/users', UsersIndex::class)->name('users.index')->middleware('can:viewAny,App\Models\User');
});
