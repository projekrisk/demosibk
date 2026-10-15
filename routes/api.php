<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\StudentAuthController;
use App\Http\Controllers\Api\StudentAssessmentController;
use App\Http\Controllers\Api\StudentCurhatController;

// Public Routes
Route::post('/student/login', [StudentAuthController::class, 'login']);
Route::post('/student/activate', [StudentAuthController::class, 'activate']);

// Protected Routes (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    // Profil & Auth
    Route::get('/student/me', [StudentAuthController::class, 'me']);
    Route::post('/student/logout', [StudentAuthController::class, 'logout']);
    Route::post('/student/avatar', [StudentAuthController::class, 'updateAvatar']);
    
    // Fitur Catatan Kedisiplinan (Prestasi/Pelanggaran)
    Route::get('/student/records', [StudentAuthController::class, 'records']);

    // Fitur Asesmen Kebutuhan (AKPD/DCM)
    Route::get('/assessments/packages', [StudentAssessmentController::class, 'getAvailablePackages']);
    Route::get('/assessments/packages/{id}', [StudentAssessmentController::class, 'getPackageQuestions']);
    Route::post('/assessments/progress', [StudentAssessmentController::class, 'submitProgress']);
    Route::post('/assessments/finalize', [StudentAssessmentController::class, 'finalize']);
    
    // Fitur Konseling (Chat/Curhat)
    Route::get('/curhat/counselors', [StudentCurhatController::class, 'getCounselors']);
    Route::get('/curhat', [StudentCurhatController::class, 'index']);
    Route::post('/curhat', [StudentCurhatController::class, 'store']);
});