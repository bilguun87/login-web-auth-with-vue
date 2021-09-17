<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Rules\AlphaDashDotRule;
use App\Models\Link;
use Illuminate\Support\Facades\Validator;

class LinksController extends Controller
{
    public function index(){
        try{
            $links = Link::all();
            return response()->json(['data' => $links->toArray()]);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function addLink(Request $request){
        $request->validate([
            'name' => ['required', new AlphaDashDotRule],
            'link' => 'required|url',
            'desc' => [new AlphaDashDotRule],
            'icon'  => 'required|mimes:jpeg,jpg,png|max:2048'
        ]);

        try{
            $iconname = $request->icon->getClientOriginalName();
            $filepath = $request->file('icon')->storeAs('uploads', $iconname, 'public');

            $link = Link::create([
                'name' => $request->name,
                'link' => $request->link,
                'description' => $request->input('desc') ? $request->desc : '',
                'icon' => '/storage/'.$filepath
            ]);
            return response()->json(['data' => [$link->toArray()]]);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);   
        }
    }

    public function editLink(Request $request, $id)
    {
        $valdate = Validator::make(['id' => $id], ['id' => 'required|numeric']);
        if($valdate->fails()){
            return response()->json(array('message' => 'Validation Failed'), 422);
        }
        
        $request->validate([
            'name' => ['required', new AlphaDashDotRule],
            'link' => 'required|url',
            'desc' => [new AlphaDashDotRule],
            'icon'  => 'mimes:jpeg,jpg,png|max:2048'
        ]);

        try{
            $iconname = '';
            $filepath = '';
            if($request->file('icon')){
                $iconname = $request->icon->getClientOriginalName();
                $filepath = $request->file('icon')->storeAs('uploads', $iconname, 'public');
            }
            $link = Link::find($id);
            $link->name = $request->name;
            $link->link = $request->link;
            if(strlen($request->desc))
                $link->description = $request->desc;
            if($request->file('icon'))
                $link->icon = '/storage/'.$filepath;
            $link->save();
            return response()->json(['data' => 'updated']);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }

    public function deleteLink($id){
        $valdate = Validator::make(['id' => $id], ['id' => 'required|numeric']);
        if($valdate->fails()){
            return response()->json(array('message' => 'Validation Failed'), 422);
        }
        try{
            $response = Link::destroy($id);
            return response()->json(['data' => $response]);
        }
        catch(\Throwable $e){
            return response()->json(['message' => $e->getMessage()], 501);
        }
    }
}
