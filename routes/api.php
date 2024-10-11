<?php
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\BreedController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\VaccineController;
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
    //Usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    //Clientes
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.show');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::put('/clients/{id}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy');
    //Proveedores
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/{id}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::put('/suppliers/{id}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    //Especies
    Route::get('/species', [SpecieController::class, 'index'])->name('species.index');
    Route::get('/species/{id}', [SpecieController::class, 'show'])->name('species.show');
    Route::post('/species', [SpecieController::class, 'store'])->name('species.store');
    Route::put('/species/{id}', [SpecieController::class, 'update'])->name('species.update');
    Route::delete('/species/{id}', [SpecieController::class, 'destroy'])->name('species.destroy');
    //Razas
    Route::get('breeds', [BreedController::class, 'index'])->name('breeds.index');
    Route::get('breeds/{id}', [BreedController::class, 'show'])->name('breeds.show');
    Route::post('breeds', [BreedController::class, 'store'])->name('breeds.store');
    Route::put('breeds/{id}', [BreedController::class, 'update'])->name('breeds.update');
    Route::delete('breeds/{id}', [BreedController::class, 'destroy'])->name('breeds.destroy');
    //Vacunas
    Route::get('vaccines', [VaccineController::class, 'index'])->name('vaccines.index');
    Route::get('vaccines/{id}', [VaccineController::class, 'show'])->name('vaccines.show');
    Route::post('vaccines', [VaccineController::class, 'store'])->name('vaccines.store');
    Route::put('vaccines/{id}', [VaccineController::class, 'update'])->name('vaccines.update');
    Route::delete('vaccines/{id}', [VaccineController::class, 'destroy'])->name('vaccines.destroy');
    //Mascotas
    Route::get('pets', [PetController::class, 'index'])->name('pets.index');
    Route::get('pets/{id}', [PetController::class, 'show'])->name('pets.show');
    Route::post('pets', [PetController::class, 'store'])->name('pets.store');
    Route::put('pets/{id}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');
});