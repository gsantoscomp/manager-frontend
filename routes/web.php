<?php

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

Route::get('/login', function () {return view('auth.login');})->name('admin.login');
Route::get('/', function () {return view('admin.index');})->name('admin.dashboard');
Route::get('/userType', function () {return view('admin.user-type.list');})->name('admin.userType.index');
