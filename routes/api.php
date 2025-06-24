<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\ChecklistItemController;

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected Routes
Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Checklist Routes
    Route::get('/checklist', [ChecklistController::class, 'index']);
    Route::post('/checklist', [ChecklistController::class, 'store']);
    Route::delete('/checklist/{id}', [ChecklistController::class, 'destroy']);
    
    // Checklist Item Routes
    Route::get('/checklist/{checklistId}/item', [ChecklistItemController::class, 'index']);
    Route::post('/checklist/{checklistId}/item', [ChecklistItemController::class, 'store']);
    Route::get('/checklist/{checklistId}/item/{itemId}', [ChecklistItemController::class, 'show']);
    Route::put('/checklist/{checklistId}/item/{itemId}', [ChecklistItemController::class, 'updateStatus']);
    Route::put('/checklist/{checklistId}/item/rename/{itemId}', [ChecklistItemController::class, 'rename']);
    Route::delete('/checklist/{checklistId}/item/{itemId}', [ChecklistItemController::class, 'destroy']);
});