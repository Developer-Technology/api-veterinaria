<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
 
Route::group([
 
    //'middleware' => 'auth:api',
    'prefix' => 'auth'
 
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});

Route::group([
    'middleware' => 'auth:api',  // Protegido por JWT
], function () {
    // Obtener todos los usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Obtener un usuario por ID
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    
    // Actualizar un usuario por ID
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
});