<?php

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
    return view('welcome');
});

//Auth::routes();
Auth::routes(['register' => false, 'reset' => false]);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
	Route::view('/home', 'home');
});

Route::get('/permission', [App\Http\Controllers\PermissionController::class, 'index'])->name('permission');
Route::post('/permission/store',[App\Http\Controllers\PermissionController::class, 'store'])->name('storepermission');

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles');
Route::post('/roles/store',[App\Http\Controllers\RoleController::class, 'store'])->name('storerole');
Route::post('/roles/assign',[App\Http\Controllers\RoleController::class, 'assign'])->name('permissionstorole');

Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users');
Route::post('/users/store', [App\Http\Controllers\UserController::class, 'store'])->name('storeusers');
Route::post('/users/assign', [App\Http\Controllers\UserController::class, 'assign'])->name('assignroletouser');
Route::post('/users/removerole', [App\Http\Controllers\UserController::class, 'removerole'])->name('removerole');

Route::view('/role2', 'role2')->middleware(['auth','role2']);
Route::view('/vulners', 'vulnerabilities.index')->name('vulners')->middleware('auth');