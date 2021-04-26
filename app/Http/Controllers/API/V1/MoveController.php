<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Move;
use App\Models\Record;
use App\Http\Resources\MoveResource;
use App\Rules\AlphaDashDotRule;
use DB;

class MoveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'record_id' => 'required|numeric',
            'place_id' => 'required|numeric',
            'requestor' => ['required', new AlphaDashDotRule],
            'performer' => ['required', new AlphaDashDotRule],
            'date' => 'required|date_format:Y-m-d',
            'move_end_date' => 'date_format:Y-m-d',
            'description' => [new AlphaDashDotRule]
        ]);

        $newmove = [];
        try{
            array_push($newmove, Move::create([
                'record_id' => $request->record_id,
                'place_id' => $request->place_id,
                'requester_name' => $request->requestor,
                'performer_name' => $request->performer,
                'move_start_date' => $request->date,
                'description' => $request->description
            ]));

            $record = Record::find($request->record_id);
            $record->ogson = $request->performer;
            $record->awsan = $request->requestor;
            $record->place_id = $request->place_id;
            $record->save();

            return MoveResource::collection($newmove);
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
        $request->validate([
            'record_id' => 'numeric',
            'place_id' => 'numeric',
            'requester_name' => [new AlphaDashDotRule],
            'performer_name' => [new AlphaDashDotRule],
            'description' => [new AlphaDashDotRule],
            'date' => 'date_format:Y-m-d',
        ]);
        try{
            //DB::enableQueryLog();
            $result = Move::find($id)->update([
                'place_id' => $request->place_id,
                'requester_name' => $request->requester_name,
                'performer_name' => $request->performer_name,
                'description' => $request->description,
                'move_start_date' => $request->date
            ]);

            $record = Record::find($request->record_id);
            $record->ogson = $request->performer_name;
            $record->awsan = $request->requester_name;
            $record->place_id = $request->place_id;
            $record->save();
            //dd(DB::getQueryLog());
            return response()->json(array('data' => $result));
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

    public function search(Request $request){
        $request->validate([
            'record_id' => 'numeric',
            'place_id' => 'numeric',
            'type_id' => 'numeric',
            'requestor' => [new AlphaDashDotRule],
            'performer' => [new AlphaDashDotRule],
            'date' => 'date_format:Y-m-d',
            'description' => [new AlphaDashDotRule]
        ]);
        try{
            $query = new Move;
            if ($request->input('record_id'))
                $query = $query->where('record_id', $request->record_id);
            if ($request->input('type_id'))
                $query = $query->where('type_id', $request->type_id);
            if ($request->input('requester'))
                $query = $query->where('requester_name', $request->requestor);
            if ($request->input('performer'))
                $query = $query->where('performer_name', $request->performer);
            if ($request->input('place_id')){
                if ($request->condition == 'is')
                    $query = $query->where('place_id', $request->place_id);
                else
                    $query = $query->where('place_id', '<>', $request->place_id);
            }
            if ($request->input('date'))
                $query = $query->where('move_start_date', $request->date);

            $result = $query->orderBy('id', 'desc')->paginate(10);
            //dd(DB::getQueryLog());
            return MoveResource::collection($result);
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
}
