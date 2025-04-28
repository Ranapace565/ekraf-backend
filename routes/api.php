<?php

use App\Http\Controllers\Article\AdminArticleController;
use App\Http\Controllers\Event\AdminEventController;
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
use App\Http\Controllers\Event\EnterpreneurEventController;
use App\Http\Controllers\Event\EventController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/redirect/google', [GoogleAuthController::class, 'redirect']);
Route::get('/auth/callback/google', [GoogleAuthController::class, 'callback']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/business', [BusinessController::class, 'index']);
Route::get('/business/{id}', [BusinessController::class, 'show']);
Route::get('/events', [EventController::class, 'index']);
Route::get('/events/{id}', [EventController::class, 'show']);
Route::get('/articles', [AdminArticleController::class, 'index']);
Route::get('/article/{id}', [AdminArticleController::class, 'show']);
Route::get('/sectors', [SectorController::class, 'index']);
Route::get('/sectors/{id}', [SectorController::class, 'show']);
Route::post('/upload-galery', [EntrepreneurBusinessGaleyController::class, 'uploadProof']);

Route::middleware(['web'])->group(function () {
    Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect']);
    Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback']);
});

Route::middleware(['auth:sanctum', 'role:visitor_logged'])->group(function () {
    Route::get('/visitor/business-submission', [BusinessSubmissionController::class, 'index']);
    Route::get('/visitor/business-submission/{id}', [BusinessSubmissionController::class, 'show']);
    Route::post('/visitor/business-submission', [BusinessSubmissionController::class, 'store']);
    Route::put('/visitor/business-submission/{id}', [BusinessSubmissionController::class, 'update']);
    Route::delete('/visitor/business-submission/{id}', [AdminBusinessSubmissionController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:entrepreneur'])->group(function () {
    Route::get('/entrepreneur/business', [EntrepreneurBusinessController::class, 'show']);
    Route::put('/entrepreneur/business/update', [EntrepreneurBusinessController::class, 'update']);
    Route::put('/entrepreneur/business/activate', [EntrepreneurBusinessController::class, 'activate']);
    Route::put('/entrepreneur/business/disable', [EntrepreneurBusinessController::class, 'disable']);
    Route::delete('/entrepreneur/business/delete', [EntrepreneurBusinessController::class, 'destroy']);

    Route::get('/entrepreneur/event', [EnterpreneurEventController::class, 'index']);
    Route::get('/entrepreneur/event/{id}', [EnterpreneurEventController::class, 'show']);
    Route::post('/entrepreneur/event', [EnterpreneurEventController::class, 'store']);
    Route::put('/entrepreneur/event/{id}', [EnterpreneurEventController::class, 'update']);
    Route::delete('/entrepreneur/event/{id}', [EnterpreneurEventController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/business-submission', [AdminBusinessSubmissionController::class, 'index']);
    Route::get('/admin/business-submission/{id}', [BusinessSubmissionController::class, 'show']);
    Route::put('/admin/business-submission/{id}/approve', [AdminBusinessSubmissionController::class, 'approve']);
    Route::put('/admin/business-submission/{id}/reject', [AdminBusinessSubmissionController::class, 'reject']);
    Route::delete('/admin/business-submission/{id}', [AdminBusinessSubmissionController::class, 'destroy']);

    Route::get('/admin/business', [AdminBusinessController::class, 'index']);
    Route::get('/admin/business/{id}', [AdminBusinessController::class, 'show']);
    Route::put('/admin/business/{id}/disable', [AdminBusinessController::class, 'disable']);
    Route::put('/admin/business/{id}/activate', [AdminBusinessController::class, 'activate']);
    Route::delete('/admin/business/{id}', [AdminBusinessController::class, 'destroy']);

    Route::post('/admin/sector', [SectorController::class, 'store']);
    Route::put('/admin/sector/{id}', [SectorController::class, 'update']);
    Route::delete('/admin/sector/{id}', [SectorController::class, 'destroy']);

    Route::get('/admin/event', [AdminEventController::class, 'index']);
    Route::get('/admin/event/{id}', [AdminEventController::class, 'show']);
    Route::put('/admin/event/{id}/approved', [AdminEventController::class, 'approve']);
    Route::put('/admin/event/{id}/reject', [AdminEventController::class, 'reject']);
    Route::delete('/admin/event/{id}', action: [AdminEventController::class, 'destroy']);

    Route::post('/admin/article', [AdminArticleController::class, 'store']);
    Route::put('/admin/article/{id}', [AdminArticleController::class, 'update']);
    Route::delete('/admin/article/{id}', [AdminArticleController::class, 'destroy']);
});
