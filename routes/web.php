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

    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('permission:view dashboard')->name('dashboard');

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

    Route::middleware('permission:view kategori')->prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->middleware('permission:create kategori')->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->middleware('permission:edit kategori')->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->middleware('permission:delete kategori')->name('destroy');
    });

    // Knowledge Base Controller
    Route::middleware('permission:view knowledge base')->prefix('knowledge')->name('knowledge.')->group(function () {
        Route::get('/', [KnowledgeBaseController::class, 'index'])->name('index');
        Route::post('/', [KnowledgeBaseController::class, 'store'])->middleware('permission:create knowledge base')->name('store');
        Route::put('/{knowledge}', [KnowledgeBaseController::class, 'update'])->middleware('permission:edit knowledge base')->name('update');
        Route::delete('/{knowledge}', [KnowledgeBaseController::class, 'destroy'])->middleware('permission:delete knowledge base')->name('destroy');
    });

    // Chatbot AI Routes
    // Predict endpoint - accessible to all authenticated users
    Route::post('/chatbot/predict', [ChatbotController::class, 'predict'])->name('chatbot.predict');

    // Logs and validation - only for admin
    Route::middleware('permission:view log chatbot')->prefix('chatbot')->name('chatbot.')->group(function () {
        Route::get('/logs', [ChatbotController::class, 'logs'])->name('logs');
        Route::post('/validate', [ChatbotController::class, 'validatePrediction'])->middleware('permission:validate log chatbot')->name('validate');
    });

    // Admin AI Training Routes
    Route::post('/export-dataset', [KnowledgeBaseController::class, 'exportDataset'])
        ->middleware('permission:train')
        ->name('knowledge.export-dataset');
    Route::post('/train-model', [KnowledgeBaseController::class, 'trainModel'])
        ->middleware('permission:train')
        ->name('knowledge.train-model');

    Route::middleware('permission:view pengguna')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:create pengguna')->name('store');
        Route::put('/{user}/password', [UserController::class, 'resetPassword'])->middleware('permission:edit pengguna')->name('password.reset');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:edit pengguna')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:delete pengguna')->name('destroy');
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

    // Role and Permission Management - Only Admin
    Route::middleware('permission:view role permission')->group(function () {
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
