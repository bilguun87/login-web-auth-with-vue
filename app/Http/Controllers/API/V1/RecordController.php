<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Record;
use App\Http\Resources\RecordResource;
use Illuminate\Support\Facades\Validator;
use App\Rules\AlphaDashDotRule;
use DB;

class RecordController extends Controller
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
        //
        $request->validate([
            'name' => 'required|regex:/^[0-9a-zA-Zа-яА-ЯөӨүҮёЁ\s\.]+$/i',
            'ogson' => ['required', new AlphaDashDotRule],
            'awsan' => ['required', new AlphaDashDotRule],
            'date' => 'required|date_format:Y-m-d',
            'type_id' => 'required|numeric',
            'place_id' => 'required|numeric'
        ]);
        $newRecord = [];
        try{
            array_push($newRecord, Record::create([
                'name' => $request->name,
                'ogson' => $request->ogson,
                'awsan' => $request->awsan,
                'date' => $request->date,
                'place_id' => $request->place_id,
                'type_id' => $request->type_id,
                'isAvailable' => 1
            ]));
            return RecordResource::collection($newRecord);
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
        //
        $validator = Validator::make(["id" => $id],['id' => 'numeric']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        $request->validate([
            'id' => 'required|numeric',
            'name' => ['required', new AlphaDashDotRule],
            'ogson' => ['required', new AlphaDashDotRule],
            'awsan' => ['required', new AlphaDashDotRule],
            'date' => 'required|date_format:Y-m-d',
            'type_id' => 'required|numeric',
            'place_id' => 'required|numeric'
        ]);
        try{
            $result = Record::find($id)->update([
                'name' => $request->name,
                'ogson' => $request->ogson,
                'awsan' => $request->awsan,
                'date' => $request->date,
                'type_id' => $request->type_id,
                'place_id' => $request->place_id
            ]);

            return response()->json(array('data' => $result));
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
    public function destroy($id)
    {
        try{
            $validator = Validator::make(["id" => $id],['id' => 'numeric']);
            if ($validator->fails())
                return response()->json(array('message' => 'Validation Failed'), 422);
            $result = Record::destroy($id);
            return response()->json(array('data' => $result));
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

    public function markAsDelete($id){
        //DB::enableQueryLog();
        $validator = Validator::make(["id" => $id],['id' => 'numeric']);
        if ($validator->fails())
            return response()->json(array('message' => 'Validation Failed'), 422);
        try{
            $record = Record::find($id);
            $record->isAvailable = 0;
            $result = $record->save();
            //dd(DB::getQueryLog());
            return response()->json(array('data' => $result));
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

    public function search(Request $request)
    {
        // dd($request);
        $request->validate([
            'nm' => 'nullable|alpha_num',
            'aw' => ['nullable', new AlphaDashDotRule],
            'og' => ['nullable', new AlphaDashDotRule],
            'date' => 'date_format:Y-m-d',
            'ti' => 'numeric',
            'pi' => 'numeric'
        ]);
        try{
            //DB::enableQueryLog();
            $query = Record::where('isAvailable', 1);
            if ($request->input('nm'))
                $query = $query->where('name', 'like', '%'.$request->nm.'%');
            if ($request->input('aw'))
                $query = $query->where('awsan','like', '%'.$request->aw.'%');
            if ($request->input('og'))
                $query = $query->where('ogson', 'like', '%'.$request->og.'%');
            if ($request->input('ti'))
                $query = $query->where('type_id', $request->ti);
            if ($request->input('pi')){
                if ($request->condition == 'is')
                    $query = $query->where('place_id', $request->pi);
                else
                    $query = $query->where('place_id', '<>', $request->pi);
            }
            if ($request->input('date'))
                $query = $query->where('date', $request->date);

            $result = $query->orderBy('id', 'desc')->paginate(10);
            //dd(DB::getQueryLog());
            return RecordResource::collection($result);
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

    public function searchbyname(Request $request){
        $request->validate(['name' => 'required|regex:/^[0-9a-zA-Zа-яА-ЯөӨүҮёЁ\s\.]+$/i']);
        //$result = [];
        try{
            $data = Record::where('isAvailable', 1)->where('name','like','%'.$request->name.'%')->get();
            if (count($data) > 0){
                //dd($data);
                //array_push($result, $data);
                return RecordResource::collection($data);
            }
            else
                return response()->json(array('data' => []));
            
            
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

    protected function bulkstore($handle, $ogson, $awsan, $type_id, $place_id, $date)
    {
        $q; //Query result
        $data = array();
        $rows=0;
        $table = DB::table('records');
        if ($handle){
            try{
                while(($line = fgetcsv($handle)) !== false){
                    if($line[0]!="Name"){
                        $rows++;
                        $data [] = array(
                            "name"      =>$line[0],
                            "ogson"     =>$ogson,
                            "awsan"     =>$awsan,
                            "type_id"   =>$type_id,
                            "place_id"  =>$place_id,
                            "date"      =>$date,
                            "isAvailable"   => 1
                        );
                    }
                }
                $q = $table->Insert($data);
            }
            catch (\Illuminate\Database\QueryException $e){
                return array("q"=>$e,"data"=>false, "rows"=>null);
            }
        }
        $data = null;
        return array("q"=>$q, "data"=>true, "rows"=>$rows);
    }

    public function import(Request $request)
    {
        // dd($request->file->getMimeType());
        // dd($request->file->extension());
        $request->validate([
            'file' => 'required|mimes:txt,csv',
            /*'name' => 'required|regex:/^[0-9a-zA-Zа-яА-ЯөӨүҮёЁ\s\.]+$/i',*/
            'ogson' => ['required', new AlphaDashDotRule],
            'awsan' => ['required', new AlphaDashDotRule],
            'date' => 'required|date_format:Y-m-d',
            'type_id' => 'required|numeric',
            'place_id' => 'required|numeric'
        ]);
        // $ffl = null;
        $rows=0;
        $file=$request->file('file');
        try{
            $handle = fopen($file, 'r');
            $result = $this->bulkstore($handle, $request->ogson, $request->awsan, $request->type_id, $request->place_id, $request->date);
            if($result["data"] == false){
                return response()->json(array('message' => " Error on file ".$file->getClientOriginalName()." <br> ".$result["q"]), 400);
            }
            else{
                $rows = $result["rows"];
            }
            return response()->json(array('rows'=>$rows), 200);
        }
        catch(Exception $e){
            return response()->json(array('error'=>$e), 400);
        }
    }
}
