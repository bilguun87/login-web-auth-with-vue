<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::middleware('auth:api')->group(function(){
	Route::get('/menu', [App\Http\Controllers\API\V1\MenuController::class, 'index']);
	Route::get('/getgroups', [App\Http\Controllers\API\V1\GroupController::class, 'index']);
	Route::get('/getseasons', [App\Http\Controllers\API\V1\SeasonController::class, 'index']);
	Route::post('/vulnersearch', [App\Http\Controllers\API\V1\VulnerController::class, 'search']);
	Route::post('/vulnerupload', [App\Http\Controllers\API\V1\VulnerController::class, 'upload']);
	Route::get('/checkcompareable', [App\Http\Controllers\API\V1\VulnerController::class, 'compareable']);
	Route::get('/checkfixed', [App\Http\Controllers\API\V1\VulnerController::class, 'checkfixed']);
	Route::post('/createSeason', [App\Http\Controllers\API\V1\SeasonController::class, 'store']);
	Route::delete('/deleteSeason/{id}', [App\Http\Controllers\API\V1\SeasonController::class, 'destroy']);
	Route::post('/createDepartment', [App\Http\Controllers\API\V1\GroupController::class, 'store']);
	Route::delete('/deleteDepartment/{id}', [App\Http\Controllers\API\V1\GroupController::class, 'destroy']);
	Route::put('/updateDepartment/{id}', [App\Http\Controllers\API\V1\GroupController::class, 'update']);
	Route::post('/allotvulners', [App\Http\Controllers\API\V1\VulnerController::class, 'allot']);
	Route::get('/gethosts', [App\Http\Controllers\API\V1\HostController::class, 'index']);
	Route::put('/updatehost/{id}', [App\Http\Controllers\API\V1\HostController::class, 'update']);
	Route::delete('/deletehost/{id}', [App\Http\Controllers\API\V1\HostController::class, 'destroy']);
	Route::post('/createhost', [App\Http\Controllers\API\V1\HostController::class, 'store']);
	Route::post('/genreport', [App\Http\Controllers\API\V1\ReportController::class, 'result']);
	Route::get('/getnfix', [App\Http\Controllers\API\V1\VulnerController::class, 'getlastnfixeds']);
	//Route::post('/searchhosts', [App\Http\Controllers\API\V1\HostController::class, 'search']);
});