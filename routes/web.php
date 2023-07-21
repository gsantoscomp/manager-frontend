<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserTypeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', [AuthController::class, 'index'])->name('admin.login')->withoutMiddleware('authenticated');
Route::post('/login', [AuthController::class, 'login'])->name('admin.authenticate')->withoutMiddleware('authenticated');
Route::get('/logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::get('/', function () {return view('admin.index');})->name('admin.dashboard');

Route::prefix('userType')->group(function () {
    Route::get('/', [UserTypeController::class, 'index'])->name('admin.userType.index');
});
