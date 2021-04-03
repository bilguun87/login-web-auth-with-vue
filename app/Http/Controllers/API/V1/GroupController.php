<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Http\Resources\GroupResource;
use Illuminate\Support\Facades\Validator;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return GroupResource::collection(Group::all());
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
        $request->validate(['name' => 'required']);
        $newdep = $request->all();
        //dd($data);
        $newdep['created_date'] = date('Y-m-d H:i:s');
        //dd($newdep);
        $result = Group::create($newdep);
        return response()->json(array('data' => $result));
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
        $reqs = $request->all();
        $reqs['id'] = $id;
        $validator = Validator::make($reqs, ['id' =>'required|numeric', 'name' => 'required']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $group = Group::find($id);
        if ($group){
            $result = $group->update($request->all());
            return response()->json(array('data' => $result ));
        }
        return response()->json(array('message' => "couldn't found" ), 400);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $validator = Validator::make(["id" => $id],['id' => 'numeric','not mumber']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $result = Group::destroy($id);
        return response()->json(array('data' => $result ));
    }
}
