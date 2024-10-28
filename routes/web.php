<?php

use App\Http\Controllers\AuthController;
use App\Mail\BookingCompletedMailing;
use App\Mail\Welcome;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Http\Controllers\ProfileController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('book',function ()
{
    $email = 'shouter@bk.ru';
    Mail::to($email)->send(new BookingCompletedMailing());
    return response()->json(['status' => 'success']);
});

Route::get('hello',function ()
{
    $user = Auth::user();
    $email = 'shouter@bk.ru';
    Mail::to($email)->send(new Welcome($user));
    return response()->json(['status' => 'success']);
});

Route::get('test-telegram',function ()
{
    Telegram::sendMessage([
        'chat_id' => env('TELEGRAM_CHANNEL_ID'),
        'parse_mode' => 'html',
        'text' => 'произошло тестовое событие'
    ]);
    return response()->json(['status' => 'success']);
});

Route::get('debug-centry', function ()
{
    throw new Exception('Exception for sentry');
});

Route::get('redirect', [AuthController::class, 'redirectToProvider']);

Route::get('callback', [AuthController::class, 'handleProviderCallback']);