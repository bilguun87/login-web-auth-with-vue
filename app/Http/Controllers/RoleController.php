<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    //
    public function index()
    {
    	$roles = Role::all();
    	//dd($roles[0]->permissions()->pluck('name'));
    	//dd($roles);
    	//$role_has_permission = Role::all()->pluck('name');
    	//dd($role_has_permission);
    	$permissions = Permission::all();
    	return view('role', compact('roles'/*,'role_has_permission'*/, 'permissions'));
    }

    public function store(Request $request)
    {
    	Role::create(['name' => $request->rolename]);
    	return redirect()->back();
    }

    public function assign(Request $request)
    {
    	$permission = Permission::find($request->permission);
    	$role = Role::find($request->role);
    	//return $role->permissions()->name();
    	$permission->assignRole($role);
    	return redirect()->back();
    }
}
