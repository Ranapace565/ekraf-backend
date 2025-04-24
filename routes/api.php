<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\AdminBusinessController;
use App\Http\Controllers\Business\BusinessSubmissionController;
use App\Http\Controllers\Business\EntrepreneurBusinessController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/auth/redirect/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/callback/google', [GoogleAuthController::class, 'callback']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware(['web'])->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::get('/usaha', [BusinessController::class, 'index']);

Route::middleware(['auth:sanctum', 'role:visitor_logged'])->group(function () {
    Route::post('/business-submission', [BusinessSubmissionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/pengajuan-usaha/{id}/approve', [AdminBusinessController::class, 'approve']);
    Route::post('/pengajuan-usaha/{id}/reject', [AdminBusinessController::class, 'reject']);
    Route::get('/pengajuan-usaha', [BusinessSubmissionController::class, 'index']);
    Route::delete('/usaha/{id}', [AdminBusinessController::class, 'destroy']);
    Route::post('/sectors', [SectorController::class, 'store']);
    Route::put('/sectors/{id}', [SectorController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'role:entrepreneur'])->group(function () {
    Route::middleware('auth:sanctum')->put('/usaha/{id}', [EntrepreneurBusinessController::class, 'update']);
});
