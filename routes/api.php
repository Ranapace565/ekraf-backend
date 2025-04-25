<?php

use App\Http\Controllers\Galery\EntrepreneurBusinessGaleyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Sector\SectorController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Business\BusinessController;
use App\Http\Controllers\Business\AdminBusinessController;
use App\Http\Controllers\Business\BusinessSubmissionController;
use App\Http\Controllers\Business\EntrepreneurBusinessController;
use App\Http\Controllers\Business\AdminBusinessSubmissionController;
use App\Http\Controllers\BusinessGalleryController;

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
Route::get('/sectors', [SectorController::class, 'index']);
Route::post('/upload-galery', [EntrepreneurBusinessGaleyController::class, 'uploadProof']);


Route::middleware(['auth:sanctum', 'role:visitor_logged'])->group(function () {
    Route::post('/business-submission', [BusinessSubmissionController::class, 'store']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/pengajuan-usaha/{id}/approve', [AdminBusinessSubmissionController::class, 'approve']);
    Route::post('/pengajuan-usaha/{id}/reject', [AdminBusinessSubmissionController::class, 'reject']);
    Route::delete('/pengajuan-usaha/{id}', [AdminBusinessSubmissionController::class, 'destroy']);
    Route::get('/pengajuan-usaha', [AdminBusinessSubmissionController::class, 'index']);


    Route::post('/usaha/{id}/disable', [AdminBusinessController::class, 'disable']);
    Route::post('/usaha/{id}/activate', [AdminBusinessController::class, 'activate']);
    Route::delete('/usaha/{id}', [AdminBusinessController::class, 'destroy']);

    Route::post('/sectors', [SectorController::class, 'store']);
    Route::put('/sectors/{id}', [SectorController::class, 'update']);
});

Route::middleware(['auth:sanctum', 'role:entrepreneur'])->group(function () {
    Route::put('/usaha/{id}', [EntrepreneurBusinessController::class, 'update']);
});
