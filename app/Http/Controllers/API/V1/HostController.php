<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Host;
use App\Http\Resources\HostResource;
use Illuminate\Support\Facades\Validator;

class HostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $ipaddr = [];
        $group_id = $request->input('group_id');
        $desc = $request->input('desc');
        if ($request->input('ipaddr'))
            $ipaddr = explode(",",preg_replace('/\s+/', '', $request->input('ipaddr')));
        $query = new Host();
        $query = $query->when($desc, function($query, $desc){
            return $query->where('description','LIKE','%'.$desc.'%'); });
        $query = $query->when($group_id, function($query, $group_id){
            return $query->where('group_id', $group_id); });
        if (count($ipaddr) > 0)
            $query = $query->whereIn('ip', $ipaddr);
        $query = $query->orderByDesc('id');
        
        $result = $query->paginate(10);
        //dd($query->toSql());
        return HostResource::collection($result);
        //return HostResource::collection(Host::orderByDesc('id')->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'ipaddr' => 'required',
            'group_id' => 'required|numeric'
        ]);

        $ipaddr = [];
        if ($request->input('ipaddr'))
            $ipaddr = explode(",",preg_replace('/\s+/', '', $request->input('ipaddr')));
        $verifiedIP = [];
        foreach($ipaddr as $ip){
            if (!Validator::make(['ip' => $ip], ['ip' => 'ipv4'])->fails())
                array_push($verifiedIP, $ip);
        }
        //dd($verifiedIP);
        $newHost = [];
        foreach ($verifiedIP as $ip) {
            try{
                array_push($newHost, Host::create([
                    'group_id' => $request->group_id,
                    'ip' => $ip,
                    'description' => $request->input("desc")
                ]));
            }catch(\Throwable $e){
                //return response()->json(['message'=> $e->errorInfo[2]], 500);
                continue;
            }
        }
        return HostResource::collection($newHost);
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
        //dd($request);
        $request->validate(['ip' => 'required|ipv4']);
        $result = Host::find($id)->update([
            'ip' => $request->input('ip'),
            'description' => $request->input('desc'),
            'group_id' => $request->group_id
        ]);
        return response()->json(array('data' => $result));
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
        $validator = Validator::make(["id" => $id],['id' => 'numeric']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $result = Host::destroy($id);
        return response()->json(array('data' => $result ));
    }
}
