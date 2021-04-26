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
	/*Vulnerabilities section*/
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
	/*Backup section*/
	Route::get('/backup/search', [App\Http\Controllers\API\V1\RecordController::class, 'search']);
	Route::get('/backup/recbyname', [App\Http\Controllers\API\V1\RecordController::class, 'searchbyname']);
	Route::put('/backup/{id}', [App\Http\Controllers\API\V1\RecordController::class, 'update']);
	Route::put('/backup/delete/{id}', [App\Http\Controllers\API\V1\RecordController::class, 'markAsDelete']);
	Route::post('/backup/add', [App\Http\Controllers\API\V1\RecordController::class, 'store']);

	Route::get('/backup/types', [App\Http\Controllers\API\V1\TypeController::class, 'index']);
	Route::get('/backup/types/list', [App\Http\Controllers\API\V1\TypeController::class, 'search']);
	Route::put('/backup/types/update/{id}', [App\Http\Controllers\API\V1\TypeController::class, 'update']);
	Route::post('/backup/types/add', [App\Http\Controllers\API\V1\TypeController::class, 'store']);
	Route::delete('/backup/types/delete/{id}', [App\Http\Controllers\API\V1\TypeController::class, 'destroy']);

	Route::get('/backup/places', [App\Http\Controllers\API\V1\PlaceController::class, 'index']);
	Route::get('/backup/places/list', [App\Http\Controllers\API\V1\PlaceController::class, 'search']);
	Route::put('/backup/places/update/{id}', [App\Http\Controllers\API\V1\PlaceController::class, 'update']);
	Route::post('/backup/places/add', [App\Http\Controllers\API\V1\PlaceController::class, 'store']);
	Route::delete('/backup/places/delete/{id}', [App\Http\Controllers\API\V1\PlaceController::class, 'destroy']);

	Route::get('/backup/moves', [App\Http\Controllers\API\V1\MoveController::class, 'index']);
	Route::get('/backup/moves/list', [App\Http\Controllers\API\V1\MoveController::class, 'search']);
	//Route::get('/backup/moves/search', [App\Http\Controllers\API\V1\MoveController::class, 'search']);
	Route::put('/backup/moves/update/{id}', [App\Http\Controllers\API\V1\MoveController::class, 'update']);
	Route::post('/backup/moves/add', [App\Http\Controllers\API\V1\MoveController::class, 'store']);
});