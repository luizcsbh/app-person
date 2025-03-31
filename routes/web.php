<?php

use App\Http\Controllers\AuthController;
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

// Rota para a página de login
Route::get('/login-page', function () {
    return view('auth.login');
})->name('login.page');

// Rota para o formulário de login real (API)
Route::post('/login', [AuthController::class, 'login'])->name('login');