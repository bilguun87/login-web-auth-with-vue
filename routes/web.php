<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;

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
    return redirect('login');
});

//Auth::routes();
Auth::routes(['register' => false, 'reset' => false]);

//Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::get('/permission', [App\Http\Controllers\PermissionController::class, 'index'])->name('permission');
Route::post('/permission/store',[App\Http\Controllers\PermissionController::class, 'store'])->name('storepermission');

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'index'])->name('roles');
Route::post('/roles/store',[App\Http\Controllers\RoleController::class, 'store'])->name('storerole');
Route::post('/roles/assign',[App\Http\Controllers\RoleController::class, 'assign'])->name('permissionstorole');

Route::get('/users2', [App\Http\Controllers\UserController::class, 'index'])->name('users');
Route::post('/users2/store', [App\Http\Controllers\UserController::class, 'store'])->name('storeusers');
Route::post('/users2/assign', [App\Http\Controllers\UserController::class, 'assign'])->name('assignroletouser');
Route::post('/users2/removerole', [App\Http\Controllers\UserController::class, 'removerole'])->name('removerole');

Route::view('/role2', 'role2')->middleware(['auth','role2']);

Route::middleware(['auth'])->group(function () {
	Route::view('/home', 'home');

	/*Vulnerabilities*/
	Route::view('/vulners', 'vulnerabilities.index');
	Route::get('/vulners/{subpage}', function($subpage){
		return view('vulnerabilities.index',['subpage' => $subpage]);
	});

	/*Backups*/
	Route::view('/backups', 'backups.index');
	Route::get('/backups/{subpage}', function($subpage){
		return view('backups.index',['subpage' => $subpage]);
	});

	/*User management routes*/
	Route::middleware(['role_or_permission:user.manage'])->group(function(){
		Route::view('/users', 'users.index');
		Route::get('/users/{subpage}', function($subpage){return view('users.index',['subpage' => $subpage]);});

		Route::view('/roles', 'roles.index');
	});
	
	/*Oradb*/
	Route::view('/oradb', 'oradb.index');
	Route::get('/oradb/{subpage}', function($subpage){return view('oradb.index',['subpage' => $subpage]);});

	/*Oradb*/
	Route::view('/addc', 'addc.index');
	Route::get('/addc/{subpage}', function($subpage){return view('addc.index',['subpage' => $subpage]);});
	//Route::view('/test', 'testpermission.index');

	/*Links page*/
	Route::view('/links', 'links.index')->middleware(['permission:link.view|link.manage']);
});

Route::get('/ldap_test', [App\Http\Controllers\Test::class, 'ldap_test']);
Route::get('/testora', [App\Http\Controllers\Test::class, 'OracleDB']);
Route::get('/phpinfo', function(){
	return phpinfo();
});
//Route::resource('groups',App\Http\Controllers\API\V1\GroupController::class);