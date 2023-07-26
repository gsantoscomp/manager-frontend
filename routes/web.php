<?php

use App\Http\Controllers\AuthController;

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\UserTypeController;
use App\Http\Controllers\UserController;

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

Route::prefix('admin')->group(function () {
    Route::get('/userType', [UserTypeController::class, 'index'])->name('admin.userType.index')->middleware('permissions:userType.index');
    Route::get('/animal', [AnimalController::class, 'index'])->name('admin.animal.index')->middleware('permissions:animals.index');
    Route::get('/appointment', [AppointmentController::class, 'index'])->name('admin.appointment.index')->middleware('permissions:appointment.index');
    Route::get('/medicine', [MedicineController::class, 'index'])->name('admin.medicine.index')->middleware('permissions:medicine.index');
    Route::get('/client', [ClientController::class, 'index'])->name('admin.client.index')->middleware('permissions:client.index');
    Route::get('/procedure', [ProcedureController::class, 'index'])->name('admin.procedure.index')->middleware('permissions:procedure.index');
    Route::get('/permission', [PermissionController::class, 'index'])->name('admin.permission.index')->middleware('permissions:permission.index');
    Route::get('/user', [UserController::class, 'index'])->name('admin.user.index')->middleware('permissions:user.index');
});