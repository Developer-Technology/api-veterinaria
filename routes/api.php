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
use App\Http\Controllers\PetNoteController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VaccineHistoryController;
use App\Http\Controllers\PetHistoryController;
use App\Http\Controllers\PetHistoryFileController;
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
    Route::post('/users', [UserController::class, 'store'])->name('store');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
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
    Route::post('pets/{id}/upload', [PetController::class, 'upload'])->name('pets.upload');
    Route::delete('pets/{id}', [PetController::class, 'destroy'])->name('pets.destroy');
    //Notas Mascotas
    Route::get('petnotes', [PetNoteController::class, 'index'])->name('petnotes.index');
    Route::get('petnotes/{id}', [PetNoteController::class, 'show'])->name('petnotes.show');
    Route::post('petnotes', [PetNoteController::class, 'store'])->name('petnotes.store');
    Route::put('petnotes/{id}', [PetNoteController::class, 'update'])->name('petnotes.update');
    Route::delete('petnotes/{id}', [PetNoteController::class, 'destroy'])->name('petnotes.destroy');
    Route::delete('pets/{petId}/notes', [PetNoteController::class, 'destroyByPetId'])->name('petnotes.destroyByPetId');
    //Empresas
    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{id}', [CompanyController::class, 'show'])->name('companies.show');
    Route::post('companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::put('companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::post('companies/{id}/upload', [CompanyController::class, 'upload'])->name('companies.upload');
    Route::delete('companies/{id}', [CompanyController::class, 'destroy'])->name('companies.destroy');
    //Citas
    Route::get('appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('appointments/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::post('appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::put('appointments/{id}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('appointments/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    //Historial Vacunas
    Route::get('vaccineshistory', [VaccineHistoryController::class, 'index'])->name('vaccineshistory.index');
    Route::get('vaccineshistory/{id}', [VaccineHistoryController::class, 'show'])->name('vaccineshistory.show');
    Route::post('vaccineshistory', [VaccineHistoryController::class, 'store'])->name('vaccineshistory.store');
    Route::put('vaccineshistory/{id}', [VaccineHistoryController::class, 'update'])->name('vaccineshistory.update');
    Route::delete('vaccineshistory/{id}', [VaccineHistoryController::class, 'destroy'])->name('vaccineshistory.destroy');
    Route::get('pets/{petId}/vaccine-history', [VaccineHistoryController::class, 'showPet'])->name('vaccineshistory.showPet');
    Route::delete('pets/{petId}/vaccine-history', [VaccineHistoryController::class, 'destroyByPetId'])->name('vaccineshistory.destroyByPetId');
    //Historias Mascotas
    //Route::get('pet-history', [PetHistoryController::class, 'index'])->name('pet-history.index');
    Route::get('pet-histories/{id}', [PetHistoryController::class, 'allHistory'])->name('pet-histories.allHistory');
    Route::get('pet-history/{id}', [PetHistoryController::class, 'show'])->name('pet-history.show');
    Route::post('pet-history', [PetHistoryController::class, 'store'])->name('pet-history.store');
    Route::put('pet-history/{id}', [PetHistoryController::class, 'update'])->name('pet-history.update');
    Route::delete('pet-history/{id}', [PetHistoryController::class, 'destroy'])->name('pet-history.destroy');
    //Adjuntos Historias Mascotas
    Route::get('files-history/{id}', [PetHistoryFileController::class, 'index'])->name('files-history.index');
    Route::post('files-history', [PetHistoryFileController::class, 'store'])->name('files-history.store');
    Route::put('files-history/{id}', [PetHistoryFileController::class, 'update'])->name('files-history.update');
    Route::post('files-history/{id}/upload', [PetHistoryFileController::class, 'upload'])->name('files-history.upload');
    Route::delete('files-history/{id}', [PetHistoryFileController::class, 'destroy'])->name('files-history.destroy');
});