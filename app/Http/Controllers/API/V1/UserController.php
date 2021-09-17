<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
//use App\Models\AllowedUser;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Adldap\Laravel\Facades\Adldap;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Rules\AlphaDashDotRule;

class UserController extends Controller
{
    /*public function __construct(){
        //$this->middleware('permission:edit articles')->only('testmiddleware'); //example
        try {
            $this->middleware(['role_or_permission:user.*'])->only('search');
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Error'], 403);
        }
    }*/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function search(Request $request)
    {
        $request->validate (['page' => 'numeric']);
        if ($request->name != "")
            $request->validate (['name' => new AlphaDashDotRule]);
        try{
            $query = new User();
            if ($request->input('name') !== null)
                $query = $query->where('name', 'like' ,'%'.$request->name.'%')->orWhere('domain','like','%'.$request->name.'%');
            $result = $query->with('roles')->with('permissions')->orderBy('id', 'asc')->paginate(10);
            return UserResource::collection($result);
        }catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            }
            //dd($e);
            return response()->json($data, 500);
        }
    }

    public function getUser(){
        try{
            $user = Auth::user();
            return UserResource::collection([$user]);
            //return response()->json(array("data" => $user, "status" => "200"),200);
        }
        catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            //dd($e);
            return response()->json($data, 501);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'rolemode' => ['required', Rule::in(['new','exist'])],
        ]);

        if ($request->rolemode == 'new'){
            $request->validate([
                'rolename' => ['required', new AlphaDashDotRule],
                'perms' => 'required|array',
                /*'perms.*' => 'numeric'*/
            ]);
        }
        else if ($request->rolemode == 'exist'){
            $request->validate([
                'roles' => 'required|array',
                /*'roles.*' => 'numeric'*/
            ]);
        }
        try{
            //authenticate hiih user table-ruu nemj ogno
            $domainuser = Adldap::search()->where('samaccountname', '=', $request->username)->get(1);
            
            if(count($domainuser) <= 0){
                return response()->json(["message" => "User not found"], 501);
            }
            
            // return $domainuser;
            $oguid =  $this->_to_p_guid($domainuser[0]->objectguid[0]);
            
            $newuser = User::create([
                'name' => $domainuser[0]->cn[0],
                'email' => isset($domainuser[0]->mail) ? $domainuser[0]->mail[0]:"",
                'domain' => $domainuser[0]->samaccountname[0],
                'objectguid' => $oguid,
            ]);

            $rolenames = [];

            if ($request->rolemode == "exist"){
                /*$roles = Role::whereIn('id', $request->roles)->select('name')->get();
                foreach ($roles as $role) {
                    //$newuser->assignRole($role->name);
                    array_push($rolenames, $role->name);
                }*/
                $rolenames = $request->roles;
            }else if ($request->rolemode == "new"){
                $newrole = Role::create(['name' => $request->rolename, 'guard_name' => 'web']);
                // $permissions = Permission::whereIn('id', $request->perms)->get();
                /*foreach($pemissions as $perm){
                    $newrole->givePermissionTo(['name'])    
                }*/
                $newrole->givePermissionTo($request->perms);
                array_push($rolenames, $newrole->name);
            }        

            $newuser->assignRole($rolenames);
            return UserResource::collection([$newuser]);
            // return $newuser;

        } catch (\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            // dd($e);
            return response()->json($data, 501);
        }
        //return "Success";
    }

    protected function _to_p_guid( $guid )
    {
        $hex_guid = unpack( "H*hex", $guid );
        $hex    = $hex_guid["hex"];

        $hex1   = substr( $hex, -26, 2 ) . substr( $hex, -28, 2 ) . substr( $hex, -30, 2 ) . substr( $hex, -32, 2 );
        $hex2   = substr( $hex, -22, 2 ) . substr( $hex, -24, 2 );
        $hex3   = substr( $hex, -18, 2 ) . substr( $hex, -20, 2 );
        $hex4   = substr( $hex, -16, 4 );
        $hex5   = substr( $hex, -12, 12 );

        $guid = $hex1 . "-" . $hex2 . "-" . $hex3 . "-" . $hex4 . "-" . $hex5;

        return $guid;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(["id" => $id],['id' => 'numeric']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $request->validate([
            'roles' => 'array'
        ]);

        try{
            $user = User::find($id);
            $user->syncRoles($request->roles);
        }
        catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            //dd($e);
            return response()->json($data, 500);
        }
        return UserResource::collection([$user]);
    }

    public function saveRole(Request $request){
        $request->validate([
            'rolemode' => ['required', Rule::in(['new','modify'])],
            'perms' => 'required|array',
            'perms.*' => new AlphaDashDotRule
        ]);
        if ($request->rolemode == 'modify')
            $request->validate(['role' => ['required', new AlphaDashDotRule]]);
        else
            $request->validate(['rolename' => ['required', new AlphaDashDotRule]]);
        try{
            if ($request->rolemode == 'modify')
                $role = Role::firstWhere('name', $request->role);
            else
                $role = Role::create(['name' => $request->rolename, 'guard_name' => 'web']);
            
            $role->syncPermissions($request->perms);

            return RoleResource::collection([$role]);
        }
        catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            //dd($e);
            return response()->json($data, 500);
        }
    }

    public function deleteRole($rolename){
        $validation = Validator::make(['rolename' => $rolename], ['rolename' => ['required', new AlphaDashDotRule]]);
        if ($validation->fails())
            return response()->json(array('message' => 'The given data was invalid'), 422);
        try{
            $role = Role::firstWhere('name', $rolename);
        $role->delete();
        return response()->json(array('message' => $rolename.' role has deleted'));
        }
        catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            //dd($e);
            return response()->json($data, 500);
        }
    }

    public function deleteUser($id){
        $validation = Validator::make(['id' => $id], ['id' => 'required|numeric']);
        if ($validation->fails())
            return response()->json(array('message' => 'The given data was invalid'), 422);
        try{
            User::destroy($id);
            return response()->json(array('message' => 'user has deleted'));
        }
        catch(\Throwable $e){
            $errors = [];
            $data = [];
            if (    
                    str_contains($e->getMessage(),'select') ||
                    str_contains($e->getMessage(),'insert') ||
                    str_contains($e->getMessage(),'update') ||
                    str_contains($e->getMessage(),'delete') ||
                    str_contains($e->getMessage(),'sqlstate')
                ){
                $errors = explode(':', $e->getMessage(), 20);
                $data['message'] = $errors[0].': Data base related error';
            } else {
                $data['message'] = $e->getMessage();
            }
            //dd($e);
            return response()->json($data, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
