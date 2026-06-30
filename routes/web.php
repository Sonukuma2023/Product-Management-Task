<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserControlller;
use App\Http\Controllers\ProductController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;


Route::get('/', function () {
    
    if (!Auth::id()) {
        return Inertia::render('Auth/LoginComponent');
    }

    return redirect()->route('User/UserDashboard');
});


Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/login');
})->middleware('auth')->name('logout');

Route::get('register',[AuthController::class ,'index'])->name('register');
Route::post('/register',[AuthController::class ,'post'])->name('register');

Route::get('login',[AuthController::class ,'login'])->name('login');
Route::post('/login',[AuthController::class ,'loginStore'])->name('login');

Route::middleware(['auth', 'users'])->group(function () {

    Route::get('user/dashboard', [UserControlller::class, 'index'])
        ->name('user.dashboard');

    // Categories
    Route::get('/categories', [UserControlller::class, 'create'])
        ->name('categories');

    Route::post('/categories', [UserControlller::class, 'store'])
        ->name('categories.store');

    Route::put('/categories/{id}', [UserControlller::class, 'update'])
        ->name('categories.update');

    Route::delete('/categories/{id}', [UserControlller::class, 'destroy'])
        ->name('categories.destroy');

    // Products
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');

    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');

    Route::put('/products/{id}', [ProductController::class, 'update'])
        ->name('products.update');

    Route::delete('/products/{id}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
});

Route::get('/test-email', function () {
    Mail::to('rahulch82667@gmail.com')->send(new TestMail());

    return 'Email sent successfully!';
});