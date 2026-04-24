<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FriendshipController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\SpotController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
*/

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// Authenticated Routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth & User Profile
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/me', [UserController::class, 'update']);

    // Spots
    Route::get('/spots/nearby', [SpotController::class, 'nearby']);
    Route::apiResource('spots', SpotController::class);

    // Questions
    Route::apiResource('questions', QuestionController::class);

    // Events
    Route::apiResource('events', EventController::class);

    // Comments & Reactions
    Route::post('/comments', [CommentController::class, 'store']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::post('/reactions', [ReactionController::class, 'store']);

    // Friendships
    Route::apiResource('friendships', FriendshipController::class)->except(['create', 'edit', 'show']);

    // Chat
    Route::apiResource('conversations', ConversationController::class)->only(['index', 'store', 'show']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::patch('/messages/{message}/read', [MessageController::class, 'markAsRead']);

    Route::middleware([\App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->group(function () {
        Route::get('/stats', [AdminController::class, 'getStats']);
        Route::get('/spots/pending', [AdminController::class, 'getPendingSpots']);
        Route::get('/users', [AdminController::class, 'getUsers']);
        Route::patch('/users/{user}/ban', [AdminController::class, 'banUser']);
        Route::patch('/users/{user}/unban', [AdminController::class, 'unbanUser']);
        Route::patch('/spots/{spot}/approve', [AdminController::class, 'approveSpot']);
        Route::patch('/spots/{spot}/reject', [AdminController::class, 'rejectSpot']);
    });

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index']);
    Route::patch('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy']);

    // File Uploads
    Route::post('/upload', [FileController::class, 'upload']);
    Route::delete('/files', [FileController::class, 'destroy']);
});
