<?php

use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CreatorProfileController;
use App\Http\Controllers\GeneratedEmailController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\LeadFinderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return auth()->check() ? redirect('/businesses') : redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return redirect('/businesses');
    })->name('dashboard');

    Route::resource('businesses', BusinessController::class);
    Route::get('/businesses/{business}/generate-email', [BusinessController::class, 'generateEmail']);
    Route::get('/businesses/{business}/generate-dm', [BusinessController::class, 'generateDm']);
    Route::get('/businesses/{business}/generate-follow-up', [BusinessController::class, 'generateFollowUp']);
    Route::get('/businesses/{business}/generate-pr-outreach', [BusinessController::class, 'generatePrOutreach']);
    Route::get('/businesses/{business}/generate-pr-follow-up', [BusinessController::class, 'generatePrFollowUp']);

    Route::get('/generated-emails/{generatedEmail}', [GeneratedEmailController::class, 'show']);
    Route::post('/generated-emails/{generatedEmail}/create-gmail-draft', [GeneratedEmailController::class, 'createGmailDraft']);
    Route::delete('/generated-emails/{generatedEmail}', [GeneratedEmailController::class, 'destroy']);

    Route::get('/creator-profile', [CreatorProfileController::class, 'show']);
    Route::post('/creator-profile', [CreatorProfileController::class, 'store']);
    Route::put('/creator-profile', [CreatorProfileController::class, 'update']);

    Route::post('/businesses/{business}/interactions', [InteractionController::class, 'store']);

    Route::get('/lead-finder', [LeadFinderController::class, 'index']);
    Route::post('/lead-finder/add', [LeadFinderController::class, 'addToCrm']);
});

require __DIR__.'/auth.php';
