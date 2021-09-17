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

	Route::group(['middleware' => ['permission:vulner.view|vulner.manage|vulner.report']], function(){
		Route::get('/getgroups', [App\Http\Controllers\API\V1\GroupController::class, 'index']);
		Route::get('/getseasons', [App\Http\Controllers\API\V1\SeasonController::class, 'index']);
		Route::post('/vulnersearch', [App\Http\Controllers\API\V1\VulnerController::class, 'search']);
		Route::get('/checkcompareable', [App\Http\Controllers\API\V1\VulnerController::class, 'compareable']);
		Route::get('/checkfixed', [App\Http\Controllers\API\V1\VulnerController::class, 'checkfixed']);		
		/*vulner.manage erhtei linkuud*/
		Route::group(['middleware' => ['permission:vulner.manage']], function(){
			Route::post('/vulnerupload', [App\Http\Controllers\API\V1\VulnerController::class, 'upload']);
			Route::post('/createSeason', [App\Http\Controllers\API\V1\SeasonController::class, 'store']);
			Route::delete('/deleteSeason/{id}', [App\Http\Controllers\API\V1\SeasonController::class, 'destroy']);
			Route::post('/createDepartment', [App\Http\Controllers\API\V1\GroupController::class, 'store']);
			Route::delete('/deleteDepartment/{id}', [App\Http\Controllers\API\V1\GroupController::class, 'destroy']);
			Route::put('/updateDepartment/{id}', [App\Http\Controllers\API\V1\GroupController::class, 'update']);
			Route::post('/allotvulners', [App\Http\Controllers\API\V1\VulnerController::class, 'allot']);
		});
		Route::get('/getnfix', [App\Http\Controllers\API\V1\VulnerController::class, 'getlastnfixeds']);
		
		Route::post('/genreport', [App\Http\Controllers\API\V1\ReportController::class, 'result'])->middleware(['permission:vulner.report|vulner.manage']);
		/*hosts-uudiin heseg*/
		Route::get('/gethosts', [App\Http\Controllers\API\V1\HostController::class, 'index'])->middleware(['permission:vulner.hosts.view|vulner.manage']);
		Route::group(['middleware' => ['permission:vulner.hosts.manage']], function(){
			Route::post('/createhost', [App\Http\Controllers\API\V1\HostController::class, 'store']);
			Route::put('/updatehost/{id}', [App\Http\Controllers\API\V1\HostController::class, 'update']);
			Route::delete('/deletehost/{id}', [App\Http\Controllers\API\V1\HostController::class, 'destroy']);
		});
	});
	
	/*Vulnerabilities section*/
	
	//Route::post('/searchhosts', [App\Http\Controllers\API\V1\HostController::class, 'search']);
	
	/*Backup section*/
	Route::group(['middleware' => ['permission:backup.view|backup.manage']], function () {
		Route::get('/backup/search', [App\Http\Controllers\API\V1\RecordController::class, 'search']);
		Route::get('/backup/recbyname', [App\Http\Controllers\API\V1\RecordController::class, 'searchbyname']);
		
		Route::get('/backup/types', [App\Http\Controllers\API\V1\TypeController::class, 'index']);
		Route::get('/backup/types/list', [App\Http\Controllers\API\V1\TypeController::class, 'search']);
		
		Route::get('/backup/places', [App\Http\Controllers\API\V1\PlaceController::class, 'index']);
		Route::get('/backup/places/list', [App\Http\Controllers\API\V1\PlaceController::class, 'search']);
		
		Route::get('/backup/moves', [App\Http\Controllers\API\V1\MoveController::class, 'index']);
		Route::get('/backup/moves/list', [App\Http\Controllers\API\V1\MoveController::class, 'search']);
		Route::group(['middleware' => ['permission:backup.manage']], function () {
			Route::put('/backup/{id}', [App\Http\Controllers\API\V1\RecordController::class, 'update']);
			Route::put('/backup/delete/{id}', [App\Http\Controllers\API\V1\RecordController::class, 'markAsDelete']);
			Route::post('/backup/add', [App\Http\Controllers\API\V1\RecordController::class, 'store']);
			Route::post('/backup/import', [App\Http\Controllers\API\V1\RecordController::class, 'import']);

			Route::put('/backup/types/update/{id}', [App\Http\Controllers\API\V1\TypeController::class, 'update']);
			Route::post('/backup/types/add', [App\Http\Controllers\API\V1\TypeController::class, 'store']);
			Route::delete('/backup/types/delete/{id}', [App\Http\Controllers\API\V1\TypeController::class, 'destroy']);

			Route::put('/backup/places/update/{id}', [App\Http\Controllers\API\V1\PlaceController::class, 'update']);
			Route::post('/backup/places/add', [App\Http\Controllers\API\V1\PlaceController::class, 'store']);
			Route::delete('/backup/places/delete/{id}', [App\Http\Controllers\API\V1\PlaceController::class, 'destroy']);

			Route::put('/backup/moves/update/{id}', [App\Http\Controllers\API\V1\MoveController::class, 'update']);
			Route::post('/backup/moves/add', [App\Http\Controllers\API\V1\MoveController::class, 'store']);
		});
	});
	//users
	Route::group(['middleware' => ['role_or_permission:user.manage']], function()
	{
		Route::get('/users/search', [App\Http\Controllers\API\V1\UserController::class, 'search']);
		Route::get('/users/roles', [App\Http\Controllers\API\V1\RoleController::class, 'index']);
		Route::get('/users/permissions', [App\Http\Controllers\API\V1\PermissionController::class, 'index']);
		Route::put('/users/edit/{id}', [App\Http\Controllers\API\V1\UserController::class, 'update']);
		Route::post('/users/add', [App\Http\Controllers\API\V1\UserController::class, 'store']);
		Route::post('/roles/save', [App\Http\Controllers\API\V1\UserController::class, 'saveRole']);
		Route::delete('/roles/delete/{rolename}', [App\Http\Controllers\API\V1\UserController::class, 'deleteRole']);
		Route::delete('/users/delete/{id}', [App\Http\Controllers\API\V1\UserController::class, 'deleteUser']);
	});
	
	Route::group(['middleware' => ['permission:oradb.view|oradb.manage']], function(){
		Route::get('/oradb/all',[App\Http\Controllers\API\V1\OraDBController::class, 'index']);
		Route::post('/oradb/profiles',[App\Http\Controllers\API\V1\OraDBController::class, 'profiles']);
		Route::post('/oradb/openusers',[App\Http\Controllers\API\V1\OraDBController::class, 'openusers']);
		Route::post('/oradb/permissions',[App\Http\Controllers\API\V1\OraDBController::class, 'permissions']);
		Route::post('/oradb/audconfs',[App\Http\Controllers\API\V1\OraDBController::class, 'audits']);
		Route::group(['middleware' => ['permission:oradb.manage']], function(){
			Route::post('/oradb/new',[App\Http\Controllers\API\V1\OraDBController::class, 'store']);
			Route::put('/oradb/edit/{id}',[App\Http\Controllers\API\V1\OraDBController::class, 'update']);
			Route::delete('/oradb/delete/{id}',[App\Http\Controllers\API\V1\OraDBController::class, 'delete']);
		});
	});
	// Route::get('/getldap', [App\Http\Controllers\API\V1\ADDCController::class, 'index']);
	Route::group(['middleware' => ['permission:addc.view|addc.manage']], function(){
		Route::get('/getusers', [App\Http\Controllers\API\V1\ADDCController::class, 'checkUserGroups']);
		Route::get('/getbranches', [App\Http\Controllers\API\V1\ADDCController::class, 'getBranches']);
		Route::get('/getpositions', [App\Http\Controllers\API\V1\ADDCController::class, 'getPositions']);
		Route::get('/getadgroups', [App\Http\Controllers\API\V1\ADDCController::class, 'getADGroups']);
		Route::get('/getmatrix', [App\Http\Controllers\API\V1\ADDCController::class, 'getGroupMatrix']);
		Route::get('/getActiveSyncOWAEnabledUsers', [App\Http\Controllers\API\V1\ExchangeEWSController::class, 'getActiveSyncOWAEnabledUsers']);
		Route::group(['middleware' => ['permission:addc.manage']], function(){
			Route::post('/addmatrix', [App\Http\Controllers\API\V1\ADDCController::class, 'addGroupMatrix']);
			Route::put('/savematrix/{id}', [App\Http\Controllers\API\V1\ADDCController::class, 'saveGroupMatrix']);
			Route::delete('/deletematrix/{id}', [App\Http\Controllers\API\V1\ADDCController::class, 'deleteGroupMatrix']);
		});
	});

	Route::group(['middleware' => ['permission:link.view|link.manage']], function(){
		Route::get('/getlinks', [App\Http\Controllers\API\V1\LinksController::class, 'index']);
		Route::group(['middleware' => ['permission:link.manage']], function(){
			Route::post('/linkadd', [App\Http\Controllers\API\V1\LinksController::class, 'addLink']);
			Route::post('/linkedit/{id}', [App\Http\Controllers\API\V1\LinksController::class, 'editLink']);
			Route::delete('/linkdelete/{id}', [App\Http\Controllers\API\V1\LinksController::class, 'deleteLink']);
		});
	});	

	//test
	Route::get('/getpermissions', [App\Http\Controllers\API\V1\UserController::class, 'getUser']);
});