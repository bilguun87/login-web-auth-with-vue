<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Resources\ADUsersResource;
use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use App\Models\GroupMatrix;
use App\Rules\AlphaDashDotRule;
use App\Http\Resources\GroupMatrixResource;
use Illuminate\Support\Facades\Validator;
use DB;

class ADDCController extends Controller
{
    // protected $branchesfromora = 'SELECT BR_ID,BR_NAME FROM CADR.VW_ST_STAFFS WHERE GESTATION <> 1 AND STATUS=0 GROUP BY BR_ID,BR_NAME';
    // protected $positionfromora = 'SELECT POS_ID,POS_NAME FROM CADR.VW_ST_STAFFS WHERE GESTATION <> 1 AND STATUS=0 GROUP BY POS_ID,POS_NAME';
    // protected $staffsfromora = 'SELECT st_id,br_id,br_name,lname,mname,pos_id,pos_name,domain_user FROM CADR.VW_ST_STAFFS WHERE GESTATION <> 1 AND STATUS=0';

    public function ldapconnect(){
        $ldapserver = 'statedc1.statebank.mn';
        $ldapuser   = 'CN=test,OU=Test,OU=Statebank,DC=statebank,DC=mn'; 
        $ldappass   = 'Asdf##123';
        $ldaptree   = "OU=statebank,DC=statebank,DC=mn";
        // $filter     = "(&(objectClass=user)(department=*))";
        $filter     = "(member=CN=Билгүүн.Ө,OU=Мэдээллийн аюулгүй байдлын алба,OU=Head_Office,OU=New User,OU=Statebank,DC=statebank,DC=mn)";
        $jsutthese  = ['samaccountname','displayname','distinguishedname','memberOf'];
        $data = [];
        $cookie = '';
        // connect
        $ldapconn = ldap_connect($ldapserver) or die("Could not connect to LDAP server.");

        if($ldapconn) {/*
            // binding to ldap server
            $ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass) or die ("Error trying to bind: ".ldap_error($ldapconn));
            // verify binding
            if ($ldapbind) {
                // echo "LDAP bind successful...<br /><br />";
               
                $cookie = '';
                // $result = ldap_search($ldapconn,$ldaptree, $filter, $jsutthese, 0, 0, 0,  LDAP_DEREF_NEVER, [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 10000, 'cookie' => $cookie]]]) or die ("Error in search query: ".ldap_error($ldapconn));
                $result = ldap_search($ldapconn,$ldaptree, $filter, $jsutthese, 0, 0, 30,  LDAP_DEREF_NEVER, [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 10, 'cookie' => $cookie]]]) or die ("Error in search query: ".ldap_error($ldapconn));
                $data = ldap_get_entries($ldapconn, $result);
               
                // SHOW ALL DATA
                echo '<h1>Dump all data</h1><pre>';
                print_r($data);   
                echo '</pre>';
               
               
                // iterate over array and print data for each entry
                echo '<h1>Show me the users</h1>';
                for ($i=0; $i<$data["count"]; $i++) {
                    //echo "dn is: ". $data[$i]["dn"] ."<br />";
                    echo "User: ". $data[$i]["cn"][0] ."<br />";
                    if(isset($data[$i]["mail"][0])) {
                        echo "Email: ". $data[$i]["mail"][0] ."<br /><br />";
                    } else {
                        echo "Email: None<br /><br />";
                    }
                }
                // print number of entries found
                // echo "Number of entries found: " . ldap_count_entries($ldapconn, $result);
            } else {
                echo "LDAP bind failed...";
            }*/
            $ldapbind = ldap_bind($ldapconn, $ldapuser, $ldappass) or die ("Error trying to bind: ".ldap_error($ldapconn));

            if($ldapbind){
                $errcode = $dn = $errmsg = $refs =  $controls = null;
                // $controls = [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 10, 'cookie' => $cookie]]];
                // echo "<br>";
                // print_r($controls);
                // echo "<br>";
                do {
                    // $controls = [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 10, 'cookie' => $cookie]]];
                    $result = ldap_search($ldapconn,$ldaptree, $filter, $jsutthese, 0, 0, 30,  LDAP_DEREF_NEVER, [['oid' => LDAP_CONTROL_PAGEDRESULTS, 'value' => ['size' => 10, 'cookie' => $cookie]]]) or die ("Error in search query: ".ldap_error($ldapconn));
                    
                    ldap_parse_result($ldapconn, $result, $errcode , $matcheddn , $errmsg , $referrals, $controls);
                    // echo $cookie;
                    $entries = ldap_get_entries($ldapconn, $result);
                    foreach ($entries as $entry) {
                        // echo "cn: ".$entry['cn'][0]."\n";
                        array_push($data, $entry);
                    }
                    // echo "<br>";
                    // print_r($controls);
                    // echo "<br>";
                    if (isset($controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'])) {
                        // You need to pass the cookie from the last call to the next one
                        $cookie = $controls[LDAP_CONTROL_PAGEDRESULTS]['value']['cookie'];
                    } else {
                        $cookie = '';
                    }
                    // echo "<br>";
                    // print_r($cookie);
                    // echo "<br>";
                } while (!empty($cookie));
            }
        }

        // all done? clean up
        ldap_close($ldapconn);
        return $data;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return $this->ldapconnect();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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

    // public function getadusers(){
    //     $recordsPerPage = 100;
    //     $currentPage = 1;
    //     $allpage = 0;
    //     $users = [];
        
    //     // return $this->getBranches();
    //     // $dbusers = $this->getStaffs();
    //     // dd($dbusers[0]);
       
    //     try{
    //         $search = Adldap::search()->users()->where('description', '*')
    //             ->andFilter(function($q) {
    //                 $q->where('useraccountcontrol','!','514')
    //                     ->orWhere('useraccountcontrol','!','546');
    //                 })
    //             ->whereIn('samaccountname',['bayasgalan.z','gandulam.j'])
    //             ->select(['samaccountname','name','displayname','department','memberOf', 'description', 'useraccountcontrol'])
    //             ->sortBy('samaccountname', 'asc')->paginate($recordsPerPage, $currentPage);
    //         // dd($search->getResults());
    //         $datas = ($search->count() > 100)?$search:$search->getResults();
    //         foreach ($datas as $user) {
    //             // dd($user);
    //             $groups = $user->getGroups(['cn','objectguid'], true);
    //             $user['groups'] = $groups;
    //             array_push($users, $user);
    //         }
    //         return ['data' => ADUsersResource::collection($users), 'total' =>  $search->count(), 'pages' => $search->getPages()];
    //         // return response()->json(['data' => $users]);
    //         // return $search;
    //     }
    //     catch(\Throwable $e){
    //         return $e;
    //     }
    // }

    public function getBranches (){
        // return DB::connection('oracle')->table('CADR.VW_ST_STAFFS')->where('STATUS','0')->where('GESTATION','!=','1')->groupBy('br_id','br_name')->select('br_id','br_name')->get();
        try {
            // $branches = Cache::remember('branches', now()->addMinutes(30), function () {
            // }
            // return response()->json(['data' => DB::connection('oracle')->select($this->branchesfromora)]);
            return response()->json(['data' => DB::connection('oracle')->table('CADR.VW_ST_STAFFS')
                ->where('STATUS','0')
                ->where('GESTATION','!=','1')
                ->groupBy('br_id','br_name')
                ->select('br_id','br_name')->get()
            ]);
        }
        catch(\Throwable $e){
            return response()->json($e, 501);
        }
    }

    public function getPositions (Request $request){
        $request->validate(['br_id' => 'numeric']);
        try {
            $query = DB::connection('oracle')->table('CADR.VW_ST_STAFFS')
                ->where('STATUS','0')
                ->where('GESTATION','!=','1');
            if ($request->input('br_id'))
                $query = $query->where('br_id', $request->br_id);
            
            $result = $query->groupBy('pos_id','pos_name')->select('pos_id','pos_name')->get();

            return response()->json(['data' => $result]);
        }
        catch(\Throwable $e){
            return response()->json($e, 501);
        }
    }


    public function getADGroups() {
        try{
            $groups = [];
            $search = Adldap::search()->groups()
                ->in('OU=Statebank,DC=statebank,DC=mn')->get();
            foreach($search as $group){
                array_push($groups, ['cn' => $group->cn[0], 'objectguid' => $this->_to_p_guid($group->objectguid[0])]);
            }
            return response()->json(['data' => $groups]);
        }
        catch(\Throwable $e){
            return response()->json($e, 501);
        }
    }

    protected function getstaffs () {
        try{
            // $staffs = Cache::remember('staffs', now()->addMinutes(30), function() {
                return DB::connection('oracle')->table('CADR.VW_ST_STAFFS')
                    ->where('STATUS','0')
                    ->where('GESTATION','!=','1')
                    ->select('st_id','br_id','br_name','lname','mname','pos_id','pos_name','domain_user')
                    ->orderBy("br_name")
                    ->get();
            // });
            // return $staffs;
            // return DB::connection('oracle')->select($this->staffsfromora);
        }
        catch(\Throwable $e){
            return $e;
        }
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

    protected function getAdGroupsByUser($dbuser = null){
        try{
            $groups = [];
            $user = Adldap::search()->users()->where('description', '*')
                ->andFilter(function($q) {
                    $q->where('useraccountcontrol','!','514')
                        ->orWhere('useraccountcontrol','!','546');
                    })
                ->where('samaccountname', $dbuser)
                ->select(['samaccountname','displayname','memberOf', 'useraccountcontrol'])->first();
            foreach($user->getGroups(['cn','objectguid'], true) as $group){
                array_push($groups, [$group->cn[0], $this->_to_p_guid($group->objectguid[0])]);
            }
            return $groups;
            // return response()->json(['data' => $users]);
            // return $search;
        }
        catch(\Throwable $e){
            return $e;
        }
    }



    public function checkUserGroups (Request $request){
        $request->validate([
            'page' => 'required|numeric',
            'br_id' => 'numeric',
            'pos_id' => 'numeric'
        ]);
        // dd(gettype($request->branch));
        try{
            // Cache::forget('staffs');
            $staffs = Cache::remember('staffs', now()->addMinutes(30), function() use ($request){
                $dbusers = $this->getstaffs();
                foreach ($dbusers as $user) {
                    $user->activegroups = $this->getAdGroupsByUser($user->domain_user);
                }

                foreach ($dbusers as $user){
                    // dd($user);
                    // echo $user->domain_user."&nbsp";
                    if (gettype($user->activegroups) == "array")
                        // echo gettype($user->activegroups);
                        $user->matches = $this->getMatchedGroups($user->br_id, $user->pos_id, $user->activegroups);
                    // dd($user);
                }
                // $shoudbegroups = GroupMatrix::where('br_id', $user->br_id)->orWhere('pos_id', $user->pos_id)->get()
                // if ()
                return $dbusers->toArray();
            });
            // $branches = array_column($staffs, 'br_name', 'br_id');
            // $positions = array_column($staffs, 'pos_name', 'pos_id');
            if ($request->input('br_id')){
                $br_id = $request->br_id;
                $staffs = array_filter($staffs, function($staff) use ($br_id){
                    return ($staff->br_id == $br_id);
                });
            }

            if ($request->input('pos_id')){
                $pos_id = $request->pos_id;
                $staffs = array_filter($staffs, function($staff) use ($pos_id){
                    return ($staff->pos_id == $pos_id);
                });
            }
            $paginated = $this->convertPagination($staffs, $request->page);
            // $paginated['branches'] = $branches;
            // $paginated['positions'] = $positions;
            return response()->json($paginated);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    protected function getMatchedGroups($br_id = -1, $pos_id, $groups){
        try{
            /*user-iin baih yostoi group-iig awah*/
            $matrix = [];
            /*pos,br hamtdaa matched group-uud*/
            // $matrix = GroupMatrix::where('br_id', $br_id)->Where('pos_id', $pos_id)->select(['objectguid','cn'])->get()->toArray();
            /*if ($pos_id == 30789)
                dd(GroupMatrix::where('br_id', $br_id)->whereNull('pos_id')->select(['objectguid','cn'])->toSql());*/
            $matrix = array_merge(
                GroupMatrix::where('br_id', $br_id)->where('pos_id', $pos_id)->select(['objectguid','cn'])->get()->toArray(),
                GroupMatrix::where('br_id', $br_id)->whereNull('pos_id')->select(['objectguid','cn'])->get()->toArray(),
                GroupMatrix::whereNull('br_id')->where('pos_id', $pos_id)->select(['objectguid','cn'])->get()->toArray(),
            );
            /*if ($pos_id == 30789)
                dd($matrix);*/
            /*matrix-t bhgui group-iig odoo bgaa group-ees shuune/ iluu group /*/
            $iluuGroup = [];
            $iluuNumber = 0;
            for($i = 0; $i < count($groups); $i++){
                if (!in_array($groups[$i][1], array_column($matrix, "objectguid") )){
                    $iluuNumber += 1;
                    array_push($iluuGroup,["objectguid" => $groups[$i][1], "cn" => $groups[$i][0]]);
                }
            }
            $dutuuGroup = [];
            $dutuuNumber = 0;
            for ($i = 0; $i < count($matrix); $i++){
                if (!in_array($matrix[$i]["objectguid"], array_column($groups, 1))){
                    $dutuuNumber += 1;
                    array_push($dutuuGroup, ["objectguid" => $matrix[$i]["objectguid"],"cn" => $matrix[$i]["cn"]]);
                }
            }

            return array("iluu" => $iluuGroup, "iluuNumber" => $iluuNumber, "dutuu" => $dutuuGroup, "dutuuNumber" =>$dutuuNumber);
        }
        catch(Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    protected function convertPagination($data, $page){
        $paginator = new LengthAwarePaginator($data, count($data), 50, $page);
        $from = ($page-1)*50+1;
        $to = ($page*50 > $paginator->total())? $paginator->total(): $page*50;
        $items = array_slice($data, $from - 1, 50);
        if ($paginator->total() % 50 > 0)
            $allpage = floor($paginator->total() / 50) + 1;
        else
            $allpage = $paginator->total() / 50;

        $response = [
                    "links" => [
                        "first" => '/?page=1',
                        "last" => '/?page='.$paginator->lastPage(),
                        "next" => $paginator->nextPageUrl(),
                        "prev" => $paginator->previousPageUrl()
                    ],
                     "meta" => [
                        'current_page' => (int)$page,
                        'from' => $from,
                        'last_page' => $allpage,
                        // 'links' => $links,
                        'path' => $paginator->path(),
                        'per_page' => 50,
                        'to' => $to,
                        'total' => $paginator->total(),
                        
                    ],
                    "data" => $items,
                ];
        return $response;
    }

    public function getGroupMatrix(Request $request){
        $request->validate([
            'page' => 'required|numeric',
            'br_id' => 'nullable|numeric',
            'pos_id' => 'nullable|numeric',
            'objectguid' => ['nullable', new AlphaDashDotRule]
        ]);
        try{
            $br_id = $request->input('br_id');
            $pos_id = $request->input('pos_id');
            $objectguid = $request->input('objectguid');
            // $matrix = GroupMatrix::orderBy('cn')->paginate(50);
            $query = new GroupMatrix();
            $query = $query->when($br_id, function($query, $br_id){
                return $query->where('br_id',$br_id); });
            $query = $query->when($pos_id, function($query, $pos_id){
                return $query->where('pos_id',$pos_id); });
            $query = $query->when($objectguid, function($query, $objectguid){
                return $query->where('objectguid',$objectguid); });
            $matrix = $query->orderBy('cn')->paginate(50);
            return GroupMatrixResource::collection($matrix);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function addGroupMatrix(Request $request){
        $request->validate([
            'branch.br_id' => 'nullable|numeric',
            'branch.br_name' => ['nullable', new AlphaDashDotRule],
            'position.pos_id' => 'nullable|numeric',
            'position.pos_name' => ['nullable',new AlphaDashDotRule],
            'group' => 'required|array',
            'group.cn' => ['required', new AlphaDashDotRule],
            'group.objectguid' =>['required', new AlphaDashDotRule]
        ]);
        try{
            $newrecord = GroupMatrix::create([
                'br_id' => $request->input('branch.br_id'),
                'br_name' => $request->input('branch.br_name'),
                'pos_id' => $request->input('position.pos_id'),
                'pos_name' => $request->input('position.pos_name'),
                'cn' => $request->input('group.cn'),
                'objectguid' => $request->input('group.objectguid'),
            ]);
            return GroupMatrixResource::collection([$newrecord]);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function saveGroupMatrix(Request $request, $id){
        // dd($request);
        $request->validate([
            'branch.br_id' => 'nullable|numeric',
            'branch.br_name' => ['nullable',new AlphaDashDotRule],
            'position.pos_id' => 'nullable|numeric',
            'position.pos_name' => ['nullable',new AlphaDashDotRule],
            'group' => 'required|array',
            'group.cn' => ['required', new AlphaDashDotRule],
            'group.objectguid' =>['required', new AlphaDashDotRule]
        ]);
        $validator = Validator::make(["id" => $id],['id' => 'numeric','not mumber']);
        if($validator->fails())
            return response()->json(array('message' => 'Validation Failed', 'validator' => $validator), 422);
        try{
            $matrix = GroupMatrix::find($id);
            if($matrix){
                $result = $matrix->update([
                    'br_id' => $request->input('branch.br_id'),
                    'br_name' => $request->input('branch.br_name'),
                    'pos_id' => $request->input('position.pos_id'),
                    'pos_name' => $request->input('position.pos_name'),
                    'cn' => $request->input('group.cn'),
                    'objectguid' => $request->input('group.objectguid'),
                ]);
                return response()->json(['data' => $result], 200);
            }
            return response()->json(array('message' => "couldn't found" ), 400);
        }
        catch(Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function deleteGroupMatrix($id){
        $validator = Validator::make(["id" => $id],['id' => 'numeric','not mumber']);
        if($validator->fails())
            return response()->json(array('message' => 'Validation Failed', 'validator' => $validator), 422);
        try{
            $result = GroupMatrix::destroy($id);
            return response()->json(array('data' => $result ));
        }
        catch(Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }
}
