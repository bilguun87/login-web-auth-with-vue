<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Vulnerability;
use Illuminate\Http\Request;
use App\Http\Resources\VulnerResource;
use DB;

class VulnerController extends Controller
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
    protected function store($handle, $season, $firstOrLast)
    {
        $q; //Query result
        $data = array();
        $rows=0;
        $table = DB::table('nesresults');
        if ( $firstOrLast == "false" ){
            $table = DB::table('temp');
            try{
                $temp_season =  $table->groupBy('season_id')->select('season_id')->get();
                if ($temp_season[0]->season_id != intval($season) || count($temp_season) > 1)
                    $table->truncate();
            }catch(\Illuminate\Database\QueryException $e){
                return array("q"=>$e,"data"=>false, "rows"=>null);
            }
        }
        if ($handle){
            try{
                while(($line = fgetcsv($handle)) !== false){
                    if($line[1]!="CVE" && $line[3]!="None" && $line[0] != "" && $line[0] != null){
                        $rows++;
                        $data [] = array(
                            "p_id"          =>$line[0],
                            "cve"           =>$line[1],
                            "cvss"          =>$line[2],
                            "risk"          =>$line[3],
                            "host"          =>$line[4],
                            "protocol"      =>$line[5],
                            "port"          =>$line[6],
                            "name"          =>$line[7],
                            "synopsis"      =>$line[8],
                            "description"   =>$line[9],
                            ////
                            "saved_date"    =>date('Y-m-d H:i:s'),
                            "season_id"     =>$season
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return \Illuminate\Http\Response
     */
    public function show(Vulnerability $vulnerability)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return \Illuminate\Http\Response
     */
    public function edit(Vulnerability $vulnerability)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vulnerability $vulnerability)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vulnerability  $vulnerability
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vulnerability $vulnerability)
    {
        //
    }

    /**

     */
    public function search(Request $request){

        if ($request->hasAny(['vulname','ipaddr','group_id','season_id','status','port','protocol'])){
            $vulname = $request->input('vulname');
            $ipaddr = [];
            if ($request->input('ipaddr'))
                $ipaddr = explode(",",preg_replace('/\s+/', '', $request->input('ipaddr')));
            //dd(count($ipaddr));
            $group_id = $request->input('group_id');
            $season_id = $request->input('season_id');
            $status = $request->input('status');
            $port = $request->input('port');
            $protocol = $request->input('protocol');
            $risk = $request->input('risk');
            $fix = $request->input('fix');
            //dd($fix);
            $query = Vulnerability::join('seasons','nesresults.season_id','=','seasons.id');
            $query = $query->when($vulname, function($query, $vulname){
                return $query->where('name','LIKE','%'.$vulname.'%'); });
            if (count($ipaddr) > 0)
                $query = $query->whereIn('host', $ipaddr);//when($ipaddr, function($query, $ipaddr){
                //return $query->whereIn('host', $ipaddr); });
            $query = $query->when($group_id, function($query, $group_id){
                return $query->where('group_id', $group_id); });
            $query = $query->when($season_id, function($query, $season_id){
                return $query->where('season_id', $season_id); });
            if (!is_null($status)){
                //dd($status);
                $query = $query->where('status', $status);//when($status >= 0? $status:null, function($query, $status){
            }
                //return $query->where('status', $status); })
            $query = $query->when($port, function($query, $port){
                return $query->where('port', $port); });
            $query = $query->when($protocol, function($query, $protocol){
                return $query->where('protocol', $protocol); });
            $query = $query->when($risk, function($query, $risk){
                return $query->where('risk', $risk); });
            if (!is_null($fix))
                $query = $query->where('fix', $fix);//when(!is_null($fix), function($query, $fix){
                //dd($fix);
                //return $query->where('fix', $fix); });
            //->get();
            $query = $query->orderBy('host');
            //dd($query->toSql());
            $result = $query->paginate(10);
            //dd($result->total());
            return VulnerResource::collection($result);
        }
        return false;
    }

    protected function csvutf8($file){
    //return $file;
        $encoding='';
        $handle = fopen($file, 'r');
        //return "file opened";
        $bom = fread($handle, 2);
        //return "file readed 2 character: ".$bom;
    //  fclose($handle);
        rewind($handle);
        //return "file rewind-ed";
        if($bom === chr(0xff).chr(0xfe)  || $bom === chr(0xfe).chr(0xff)){
                // UTF16 Byte Order Mark present
            $encoding = 'UTFi-16';
            //return "encoding UTFi-16";
        } else {
            //return "trying to detecting encode";
            $file_sample = fread($handle, 1000);// + 'e'; //read first 1000 bytes
            // + e is a workaround for mb_string bug
            rewind($handle);
            //return "file sapmle: ".$file_sample;  
            $encoding = mb_detect_encoding($file_sample , 'UTF-8, UTF-7, ASCII, EUC-JP,SJIS, eucJP-win, SJIS-win, JIS, ISO-2022-JP');
            //return "detected encode: ".$encoding;
        }
        if ($encoding){
            stream_filter_append($handle, 'convert.iconv.'.$encoding.'/UTF-8');
            //return "encoded";
        }
        return $handle;
    }

    protected function findfixCurSeason($cur_season){
        $cid = $cur_season->id;
        $result = DB::statement("call findfixCurSeason($cid)");
        return $result;
    }

    public function upload(Request $request){
        
        $request->validate([
            'season' => 'required',
            'files' => 'required',
        ]);
        $ffl = null;
        $rows=0;
        $files=$request->file('files');
        $s_id=$request->input('season');
        $firstOrLast = $request->input('beggining');
        try{
            foreach($files as $file){
                //dd($file);
                //$handle = $this->csvutf8($file);
                //dd($file);
                $handle = fopen($file, 'r');
                /*if (($handle = fopen("test.csv", "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
                        $num = count($data);
                        echo "<p> $num fields in line $row: <br /></p>\n";
                        $row++;
                        for ($c=0; $c < $num; $c++) {
                            echo $data[$c] . "<br />\n";
                        }
                    }
                    fclose($handle);
                }*/

                $result = $this->store($handle, $s_id, $firstOrLast);
                if($result["data"] == false){
                    return response()->json(array('message' => " Error on file ".$file->getClientOriginalName()." <br> ".$result["q"]), 400);
                }
                else
                    $rows += $result["rows"];
            }
            /*if (!$firstOrLast) //false буюу улирлын эхнийх биш сүүлийх байвал засах функцыг дуудна
                $ffl = $this->findfixCurSeason($s_id);*/

            return response()->json(array('rows'=>$rows,'ffl'=>$ffl), 200);
        }
        catch(Exception $e){
            return response()->json(array('error'=>$e), 400);
        }
    }

    public function compareable(Request $request){
        $request->validate(['sid' => 'required|numeric']);
        $temp = DB::table('temp')->where("season_id", $request->sid)->count();
        $main = DB::table('nesresults')->where("season_id", $request->sid)->count();
        if ( $temp > 0 && $main > 0)
            return response()->json(array('data' => true ), 200);
        return response()->json(array('data'=> false), 200);
    }

    public function checkfixed(Request $request){
        $request->validate(['sid' => 'required']);
        $result = DB::statement("call findfixCurSeason($request->sid)");
        $fixedNumber = 0;
        //dd(Vulnerability::where('fix','1')->where('season_id', $request->sid)->toSql());
        if ($result)
            $fixedNumber = Vulnerability::where('fix','1')->where('season_id', $request->sid)->count();
        
        return response()->json(array('data' => array('executed' => $result, 'fixed' => $fixedNumber)), 200);
    }

    public function allot(Request $request){
        $request->validate([
            'type' => 'required', 
            'season_id' => 'required|numeric', 
            'group_id' => 'numeric']);
        $result = null;
        if($request->type == "ip"){
            /*Эмзэг байдлыг IP-аар бүлэглэх*/
            $ipaddr = preg_replace('/\s+/', '', $request->ipaddr);
            if(strpos($ipaddr,",") !== false){
                $ips = explode(",",$ipaddr);
                $result = Vulnerability::where('season_id', $request->season_id)->whereIn('host',$ips)->update(['group_id' => $request->group_id]);

            }
            else{
                $result = Vulnerability::where('season_id',$request->season_id)->where('host',$ipaddr)->update(['group_id' => $request->group_id]);
            }
        }
        elseif ($request->type == "name") {
            /*Нэрээр бүлэглэх*/
            $result = Vulnerability::where('season_id',$request->season_id)->where('name','like',$request->vulname."%")->update(['group_id' => $request->group_id]);
        }
        elseif ($request->type == "all")  {
            /*Бүлэгт ороогүйг сонгосон бүлэгт оруулах*/
            $result = Vulnerability::where('season_id',$request->season_id)->whereNull('group_id')->update(['group_id' => $request->group_id]);
        }
        elseif ($request->type == "toip") {
            /*Бүртгэгдсэн IP-г IP-аар бүлэглэх*/
            $ipaddr = preg_replace('/\s+/', '', $request->ipaddr);
            if(strpos($ipaddr,",") !== false){
                $ips = explode(",",$ipaddr);
                //DB::table('nesresults')->where('season_id','=',$request->season)->whereIn('host',$ips)->update(['group_id' => $request->agroup]);
                $result = DB::table('hosts')->whereIn('ip',$ips)->update(['group_id' => $request->group_id]);
            }
            else{
                $result = DB::table('hosts')->where('ip',$ipaddr)->update(['group_id' => $request->group_id]);
            }
        }
        elseif ($request->type == "reg") {

            $groups = DB::table('hosts')->groupBy('group_id')->select('group_id')->get();
            foreach ($groups as $gr) {
                //print($gr->group_id);
                try{
                    $result = Vulnerability::where('season_id', $request->season_id)
                    ->whereIn('host', function($query) use ($gr){
                        $query->select('ip')->from('hosts')->where('group_id',$gr->group_id);
                    })->update(['group_id' => $gr->group_id]);
                }
                catch(Exception $e){
                    //print $e;
                    return response()->json(array('error' => $e), 500);
                }
            }
            
        }
        elseif ($request->type == "allip") {
            /*Бүлэглэгдээгүй IP-уудыг бүгдийг бүлэгт оноох*/
            try{
                $result = DB::table('hosts')->whereNull('group_id')->update(['group_id' => $request->group_id]);
            }
            catch(Exception $e){
                return response()->json(array('error' => $e), 500);
            }
        }
        //return redirect('groups/edit');
        return response()->json(array('data' => $result), 200);
    }

    protected function lastnseason($n = 1){
        $season = DB::table('seasons')
            ->join('nesresults','nesresults.season_id','=','seasons.id')
            ->groupBy('seasons.id')
            ->orderBy('seasons.year','desc')
            ->orderBy('seasons.season','desc')
            ->select('seasons.id','seasons.year','seasons.season_name')
            ->take($n)->get();
        return $season;
    }

    public function getlastnfixeds(){
        $seasons = $this->lastnseason(5);
        $fix = [];
        foreach ($seasons as $ssn) {
            $fix[$ssn->id]['fixed'] = Vulnerability::where('season_id', $ssn->id)->where('fix', 1)->count();
            $fix[$ssn->id]['sname'] = $ssn->year.'-'.$ssn->season_name;
            $fix[$ssn->id]['new'] = DB::table('vulners_library')->where('season_id', $ssn->id)->count();
            $fix[$ssn->id]['crit'] = Vulnerability::where('season_id', $ssn->id)->where('risk', 'Critical')->count();
            $fix[$ssn->id]['high'] = Vulnerability::where('season_id', $ssn->id)->where('risk', 'High')->count();
            $fix[$ssn->id]['med'] = Vulnerability::where('season_id', $ssn->id)->where('risk', 'Medium')->count();
            $fix[$ssn->id]['low'] = Vulnerability::where('season_id', $ssn->id)->where('risk', 'Low')->count();
        }
        return response()->json($fix);
    }
}
