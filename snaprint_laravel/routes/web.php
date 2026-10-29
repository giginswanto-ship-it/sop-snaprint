<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SopController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes - SNAPRINT DIGITAL PRINTING (dutaglobaltech.com)
|--------------------------------------------------------------------------
|
| Database-driven SQL & Laravel Architecture
| Exact Style & Design Preservation
|
*/

// Master SOP Digital Book
Route::get('/', [SopController::class, 'index'])->name('sop.index');
Route::get('/sop', [SopController::class, 'index'])->name('sop.home');
Route::get('/sop/{slug}', [SopController::class, 'show'])->name('sop.show');

// Live Search API
Route::get('/api/search', [SopController::class, 'search'])->name('api.search');

// Operational Control Forms (Dokumen Kontrol)
Route::get('/forms', [FormController::class, 'index'])->name('forms.index');
Route::get('/forms/{slug}', [FormController::class, 'show'])->name('forms.show');
Route::post('/forms/{slug}/submit', [FormController::class, 'submit'])->name('forms.submit');

// Submissions / Logbook Archive
Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');
Route::post('/submissions/{id}/status', [SubmissionController::class, 'updateStatus'])->name('submissions.updateStatus');

// System Dashboard & Database Status
Route::get('/system', [DashboardController::class, 'index'])->name('system.index');
