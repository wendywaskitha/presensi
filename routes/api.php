<?php

use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/get-attendance-today', [AttendanceController::class, 'getAttendanceToday'])->name('get_attandence_today');
    Route::get('/get-schedule', [AttendanceController::class, 'getSchedule'])->name('get_schedule');
    Route::post('/store-attendance', [AttendanceController::class, 'store']);
    Route::get('/get-attendance-by-month-year/{month}/{year}', [AttendanceController::class, 'getAttendanceByMonthYear']);
    Route::post('/banned', [AttendanceController::class, 'banned']);
    Route::get('/get-photo', [AttendanceController::class, 'getImage']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
