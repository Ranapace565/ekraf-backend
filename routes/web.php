<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');

Route::get('/auth/redirect/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/callback/google', [GoogleAuthController::class, 'callback']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/', function () {
    return view('testlogin');
});
Route::get('/login', function () {
    return view('welcome');
});
