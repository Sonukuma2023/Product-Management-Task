<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserControlller;

Route::get('/', function () {
    return view('welcome');
});

Route::get('register',[AuthController::class ,'index'])->name('register');
Route::post('/register',[AuthController::class ,'post'])->name('register');

Route::get('login',[AuthController::class ,'login'])->name('login');
Route::post('/login',[AuthController::class ,'loginStore'])->name('login');

Route::get('user/dashboard',[UserControlller::class ,'index'])->name('user.dashboard');
