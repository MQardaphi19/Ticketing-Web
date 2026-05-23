<?php

use App\Http\Controllers\TicketController;
use  App\Http\Controllers\ChatbotController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolePermissionController;

Route::get('/', function () {
    return view('welcome');
});

// // Require auth untuk semua route ticket
Route::middleware(['auth'])->group(function () {
    Route::get('test', function () {
        return "Test";
    });

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Dashboard

    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/my', [TicketController::class, 'my'])->name('my');
        Route::get('/create', [TicketController::class, 'create'])->name('create');
        Route::post('/', [TicketController::class, 'store'])->name('store');
        Route::get('/', [TicketController::class, 'index'])->name('index');
        Route::get('/{ticket}/attachments/{attachment}/download', [TicketController::class, 'downloadAttachment'])->name('attachments.download');
        Route::get('/{ticket}/messages', [TicketController::class, 'messages'])->name('messages.index');
        Route::post('/{ticket}/messages', [TicketController::class, 'storeMessage'])->name('messages.store');
        Route::get('/{ticket}', [TicketController::class, 'show'])->name('show');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('destroy');
        Route::post('/bulk/assign', [TicketController::class, 'bulkAssign'])->name('bulk.assign');
        Route::post('/bulk/status', [TicketController::class, 'bulkStatus'])->name('bulk.status');
    });

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Knowledge Base Controller
    Route::prefix('knowledge')->name('knowledge.')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->name('store');
        Route::put('/{knowledge}', [KnowledgeBaseController::class, 'update'])->name('update');
        Route::delete('/{knowledge}', [KnowledgeBaseController::class, 'destroy'])->name('destroy');
    });

    // Chatbot AI Routes
    Route::prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/logs', [ChatbotController::class, 'logs'])->name('logs');
        Route::post('/predict', [ChatbotController::class, 'predict'])->name('predict');
    });

    // Admin AI Training Routes
    Route::post('/export-dataset', [KnowledgeBaseController::class, 'exportDataset']);
    Route::post('/train-model', [KnowledgeBaseController::class, 'trainModel']);
    Route::post('/chatbot/validate', [ChatbotController::class, 'validatePrediction']);

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::put('/{user}/password', [UserController::class, 'resetPassword'])->name('password.reset');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });

    // Custom routes
    Route::post('/{ticket}/assign', [TicketController::class, 'assign'])
        ->name('assign')
        ->middleware('can:assign,ticket');

    Route::post('/{ticket}/status', [TicketController::class, 'updateStatus'])
        ->name('status.update')
        ->middleware('can:updateStatus,ticket');

    Route::get('/datatable', [TicketController::class, 'datatable'])
        ->name('datatable');

    // // Comments
    // Route::post('/{ticket}/comments', [TicketCommentController::class, 'store'])
    //     ->name('comments.store');

    // Route::put('/comments/{comment}', [TicketCommentController::class, 'update'])
    //     ->name('comments.update');

    // Route::delete('/comments/{comment}', [TicketCommentController::class, 'destroy'])
    //     ->name('comments.destroy');

    // Role and Permission Management
    Route::middleware(['auth'])->group(function () {
        Route::get('/roles', [RolePermissionController::class, 'index'])->name('roles.index');
        Route::post('/roles/{role}', [RolePermissionController::class, 'update'])->name('roles.update');
    });
});




// // API Routes (untuk SPA/Mobile App)
// Route::middleware(['auth:api'])->prefix('api/v1')->group(function () {
//     Route::apiResource('tickets', TicketApiControlller::class);
//     Route::get('/tickets/{ticket}/comments', [CommentApiController::class, 'index']);
//     Route::post('/tickets/{ticket}/comments', [CommentApiController::class, 'store']);
// });

Route::post('/logout', function () {
    Auth::guard('web')->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Authentication Routes
require __DIR__ . '/auth.php';
