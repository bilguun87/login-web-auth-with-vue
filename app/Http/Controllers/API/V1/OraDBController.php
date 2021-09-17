<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Oradb;
use App\Models\Oradb_pol;
use App\Http\Resources\OraDBResource;
use App\Rules\AlphaDashDotRule;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

// use Illuminate\Pagination\Paginator;
// use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class OraDBController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        try{
            $cons = Oradb::all();
            return OraDBResource::collection($cons);
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
            }
            //dd($e);
            return response()->json($data, 500);
        }
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
            'name' => ['required', new AlphaDashDotRule],
            'desc' => ['required', new AlphaDashDotRule],
            'user' => ['required', new AlphaDashDotRule],
            'constr' => ['required', new AlphaDashDotRule],
        ]);
        try{
            $oracon = Oradb::create([
                'name' => $request->name,
                'description' => $request->desc,
                'user' => $request->user,
                'constr' => Crypt::encryptString($request->constr)
            ]);
            return OraDBResource::collection([$oracon]);
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
            }
            //dd($e);
            return response()->json($data, 500);
        }
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $valdate = Validator::make(['id' => $id], ['id' => 'required|numeric']);
        if($valdate->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $request->validate([
            'name' => ['required', new AlphaDashDotRule],
            'desc' => ['required', new AlphaDashDotRule],
            'user' => ['required', new AlphaDashDotRule],
            'constr' => [new AlphaDashDotRule],
        ]);
        try{
            $con = Oradb::find($id);
            $con->name = $request->name;
            $con->user = $request->user;
            $con->description = $request->desc;
            if ($request->input('constr'))
                $con->constr = Crypt::encryptString($request->constr);
            $con->save();
            return OraDBResource::collection([$con]);
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
    public function delete($id)
    {
        //
        $valdate = Validator::make(['id' => $id], ['id' => 'required|numeric']);
        if($valdate->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        try{
            Oradb::destroy($id);
            return response()->json(array('message' => 'deleted'));
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
            }
            //dd($e);
            return response()->json($data, 500);
        }
    }

    protected function odbconnex($request, $qry, $param = []){
        try{
            $odbcon = Oradb::find($request->id);
            $conn = oci_connect($odbcon->user, $request->password, Crypt::decryptString($odbcon->constr));
            $allrows = 0;
            $response = [];
            
            if (!$conn) {
                $e = oci_error();
                return "In connection: ".$e;
            }
           
            //dd($allrows);
            $stid = oci_parse($conn, $qry);
            if (count($param) > 0)
                foreach ($param as $key => $value)
                    oci_bind_by_name($stid, ":".$key, $param[$key]);
            
            oci_execute($stid);

            $res = [];

            while ($row = oci_fetch_assoc($stid)) {
                array_push($res, $row);
            }
            oci_close($conn);

            return $res;
        }
        catch(\Throwable $e){
            // return response()->json(array('message' => explode(':',$e->getMessage(), 3)[2]), 501);
            return $e;
        }
    }

    public function profiles(Request $request){
        $request->validate([
            'id' => 'required|numeric',
            'password' => 'required'
        ]);
        try{
            $qry = "SELECT PROFILE FROM SYS.DBA_PROFILES GROUP BY PROFILE";
            $result = $this->odbconnex($request, $qry);
            //return gettype($result);
            // return OraDBProfileResource::collection($result);
            // return response()->json(array('data' => $result));
            if (gettype($result) != 'array')
                return response()->json(['message' => $result->getMessage()], 501);
            return response()->json(array('data' => $result));
        }
        catch(\Throwable $e){
            return response()->json( array('message' => $e->getMessage()), 501);
        }

    }

    public function openusers(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric', // connection string-iin ID
            'password' => 'required',
            'profile' => ['required', new AlphaDashDotRule],
            'page' => 'required|numeric'
        ]);
        try{
            $data = Cache::remember('openusers-'.$request->id.$request->profile, now()->addMinutes(30), function() use ($request){
                $qry = "SELECT USERNAME, USER_ID, ACCOUNT_STATUS, EXPIRY_DATE, CREATED, PROFILE FROM DBA_USERS WHERE ACCOUNT_STATUS='OPEN' AND PROFILE=:profile ORDER BY USERNAME";
                return $this->odbconnex($request, $qry, array("profile" => $request->profile));
            });
            
            if (gettype($data) != 'array')
                return response()->json(['message' => $data->getMessage()], 501);

            return $this->convertPagination($data, $request->page);
        }
        catch(\Throwable $e){
            return response()->json( array('message' => $e->getMessage()), 501);
        }
    }

    public function permissions(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric', // connection string-iin ID
            'password' => 'required',
            'profile' => ['required', new AlphaDashDotRule],
            'page' => 'required|numeric'
        ]);
        try{            
            $data = Cache::remember('perms-'.$request->id.$request->profile, now()->addMinutes(30), function() use ($request){
                $qry = "SELECT * FROM DBA_TAB_PRIVS WHERE GRANTEE IN (SELECT USERNAME FROM DBA_USERS WHERE ACCOUNT_STATUS='OPEN' AND PROFILE = :profile) 
                        AND PRIVILEGE <> 'SELECT' ORDER BY GRANTEE,PRIVILEGE";
                return $this->odbconnex($request, $qry, array("profile" => $request->profile));
            });
            // dd($data['data']);
            if (gettype($data) != 'array')
                return response()->json(['message' => $data->getMessage()], 501);
            
            return  $this->convertPagination($data, $request->page);
        }
        catch(\Throwable $e){
            return response()->json( array('message' => $e->getMessage()), 501);
            // return $e;
        }
    }

    protected function convertPagination($data, $page){
        $paginator = new LengthAwarePaginator($data, count($data), 10, $page);
        $from = ($page-1)*10+1;
        $to = ($page*10 > $paginator->total())? $paginator->total(): $page*10;
        $items = array_slice($data, $from - 1, 10);
        if ($paginator->total() % 10 > 0)
            $allpage = floor($paginator->total() / 10) + 1;
        else
            $allpage = $paginator->total() / 10;
        $response = [
                    "links" => [
                        "first" => '/?page=1',
                        "last" => '/?page='.$paginator->lastPage(),
                        "next" => $paginator->nextPageUrl(),
                        "prev" => $paginator->previousPageUrl()
                    ],
                     "meta" => [
                        'current_page' => $page,
                        'from' => $from,
                        'last_page' => $allpage,
                        /*'links' => $links,*/
                        'path' => $paginator->path(),
                        'per_page' => 10,
                        'to' => $to,
                        'total' => $paginator->total(),
                        
                    ],
                    "data" => $items,
                ];
        return $response;
    }

    protected function useraudset(Request $request, string $username){
        $qry = "SELECT * FROM DBA_STMT_AUDIT_OPTS WHERE USER_NAME = :username";
        $res = $this->odbconnex($request, $qry, array("username"=>$username));
        return $res;
    }

    protected function getpol(string $polname){
        $settings = Oradb_pol::where('polname','=', $polname)->select('polvalue')->get();
        return $settings;
    }

    public function audits(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric', // connection string-iin ID
            'password' => 'required',
            'profile' => ['required', new AlphaDashDotRule],
            'page' => 'required|numeric'
        ]);
        try{
            $audconfs = Cache::remember('audconfs', now()->addMinutes(30), function() {return $this->getpol('audit_option');});
            $response = [];
            /*Users without pagination*/
            /*$allusersrequest = $request;
            unset($allusersrequest['page']);*/

            $data = Cache::remember('userauds-'.$request->id.$request->profile, now()->addMinutes(30), function() use ($request, $audconfs){
                $qry = "SELECT USERNAME, USER_ID FROM DBA_USERS WHERE ACCOUNT_STATUS='OPEN' AND PROFILE=:profile";
                $users = $this->odbconnex($request, $qry, array("profile" => $request->profile));
                // dd($users);
                if (gettype($users) != 'array')
                    return $users;
                //dd($users['data']);
                for ($i=0; $i < count($users); $i++){
                    $audset = $this->useraudset($request, $users[$i]['USERNAME']);
                    if (gettype($audset) != 'array')
                        return $audset;
                    // dd($audset);
                    foreach ($audconfs as $conf){
                        if (in_array(strtoupper($conf->polvalue), array_column($audset,"AUDIT_OPTION")))
                            $users[$i][$conf->polvalue] = "Good";
                        else
                            $users[$i][$conf->polvalue] = "Bad";

                    }
                    // dd($users[$i]);
                    // array_push($response['data'], $users[$i]);
                }
                return $users;
            });

            if (gettype($data) != 'array')
                return response()->json(['message' => $data->getMessage()], 501);
            
            $paginated = $this->convertPagination($data, $request->page);
            $paginated['audconfs'] = $audconfs;

            return $paginated;
        }
        catch(\Throwable $e){
            // return response()->json( array('message' => $e->getMessage()), 501);
            return $e;
        }
    }
}
