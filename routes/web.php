<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CctController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\DeliveryValidationController;
use App\Http\Controllers\Admin\DirectionController;
use App\Http\Controllers\Admin\PlantController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
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
    return auth()->check() ? redirect()->route('admin.dashboard') : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::put('users/{user}', [UserController::class, 'update']);
    Route::resource('users', UserController::class);


    Route::resource('roles', RoleController::class);
    Route::resource('ccts', CctController::class);
    Route::resource('plants', PlantController::class);
    Route::resource('directions', DirectionController::class);
    Route::resource('deliveries', DeliveryController::class);
    Route::resource('delivery-validations', DeliveryValidationController::class)->parameters(['delivery-validations' => 'deliveryValidation']);
    Route::post('delivery-validations/{deliveryValidation}/approve', [DeliveryValidationController::class, 'approve'])->name('delivery-validations.approve');
    Route::get('/logs', [ActivityLogController::class, 'index'])->name('logs.index');
});
