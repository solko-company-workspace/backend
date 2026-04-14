<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyMemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/password/forgot', [AuthController::class, 'forgotPassword']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/users', [Admin\UserController::class, 'index']);
        Route::post('/users/{id}/approve', [Admin\UserController::class, 'approve']);
        Route::post('/users/{id}/reject', [Admin\UserController::class, 'reject']);

        Route::get('/codes/groups', [Admin\CodeController::class, 'groups']);
        Route::get('/codes/{groupKey}', [Admin\CodeController::class, 'index']);
        Route::post('/codes', [Admin\CodeController::class, 'store']);
        Route::patch('/codes/{id}', [Admin\CodeController::class, 'update']);
        Route::patch('/codes/{id}/toggle', [Admin\CodeController::class, 'toggle']);
    });

    Route::get('/companies', [CompanyController::class, 'companies']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::patch('/companies/{id}', [CompanyController::class, 'update']);
    Route::patch('/companies/{id}/toggle', [CompanyController::class, 'toggle']);

    Route::get('/company-members', [CompanyMemberController::class, 'index']);
    Route::post('/company-members', [CompanyMemberController::class, 'store']);
    Route::patch('/company-members/{id}', [CompanyMemberController::class, 'update']);
    Route::delete('/company-members/{id}', [CompanyMemberController::class, 'delete']);
});
