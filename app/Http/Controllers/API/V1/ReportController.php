<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\Group;
use App\Models\Vulnerability;
use DB;

class ReportController extends Controller
{
    protected function getPrevSeason($cur_sid){
        $cur_season = Season::find($cur_sid);
        $prev_season = DB::table('seasons')
            ->join('nesresults','nesresults.season_id','=','seasons.id')
            ->where(function ($query) use ($cur_season) {
                $query->where('seasons.year','=',$cur_season->year)
                ->where('seasons.season','<',$cur_season->season);
                })
            ->orWhere(function ($query) use ($cur_season) {
                $query->where('seasons.year','<',$cur_season->year)
                ->where('seasons.season','<=', 4);
                })
            ->groupBy('seasons.id')
            ->orderBy('seasons.year','desc')
            ->orderBy('seasons.season','desc')
            ->select('seasons.id as id','seasons.year as year','seasons.season_name as season_name')
            ->first();
        return $prev_season;
    }
    protected function getAllFromVulnerCount($cur_sid, $status = null, $risk = null, $ip = null, $groups = null, $group_null = null){
        $all_current = Vulnerability::where('season_id','=',$cur_sid)
            ->where('unfixable','<>',1);
        if ($status != null)
            $all_current = $all_current->whereIn('status',$status);
        if ($risk != null)
            $all_current = $all_current->whereIn('risk',$risk);
        if ($ip != null)
            $all_current = $all_current->whereIn('host',$ip);
		if ($groups != null)
			if ($group_null)
				$all_current = $all_current->where(function($query) use ($groups) {
					$query->whereIn('group_id', $groups)->orWhereNull('group_id');
				});
			else
		    	$all_current = $all_current->whereIn('group_id', $groups);
		else
			if ($group_null)
				$all_current = $all_current->whereNull('group_id');
		//dd($all_current->toSql());		
		$result = $all_current->count();
        return $result;
    }

    protected function getVulnerByRiskCount($sid, $risk, $status = null, $group = null, $fix = null, $ip = null, $group_null = null){
        //dd($group);
        $by_risk_count = Vulnerability::where('season_id',$sid)
            ->where('unfixable','<>',1)
            ->where('risk',$risk);
        if ($status != null)
            $by_risk_count = $by_risk_count->whereIn('status',$status);

        if ($group !== null)
            if ($group_null)
                $by_risk_count = $by_risk_count->where(function($query) use ($group) {
                    $query->whereIn('group_id', $group)->orWhereNull('group_id');
                });
            else
                $by_risk_count = $by_risk_count->whereIn('group_id',$group);
        else
            if ($group_null)
                $by_risk_count = $by_risk_count->whereNull('group_id');

        if ($fix != null)
            $by_risk_count = $by_risk_count->whereIn('fix',$fix);
        if ($ip != null)
            $by_risk_count = $by_risk_count->whereIn('host',$ip);
        //dd($by_risk_count->ToSql());
        $result = $by_risk_count->count();
        //dd($by_risk_count);
        return $result;
    }

    protected function getVulnerByStatusCount($sid, $status = null, $risk = null, $group = null, $fix = null, $ip = null, $group_null = null){
        
        $by_status_count = DB::table('nesresults')
            ->where('season_id','=',$sid)
            ->where('unfixable','<>',1);
        if ($status != null)
            $by_status_count = $by_status_count->where('status','=',$status);

        if ($group !== null)
            if ($group_null)
                $by_status_count = $by_status_count->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $by_status_count = $by_status_count->whereIn('group_id',$group);
        else
            if ($group_null)
                $by_status_count = $by_status_count->whereNull('group_id');

        if ($risk != null)
            $by_status_count = $by_status_count->whereIn('risk',$risk);
        if ($fix != null)
            $by_status_count = $by_status_count->whereIn('fix', $fix);
        if ($ip != null)
            $by_status_count = $by_status_count->whereIn('host', $ip);
        $by_status_count = $by_status_count->count();
        #return $by_status_count->toSql();
        return $by_status_count;
    }

    protected function getVulnerByFixCount($sid, $status = null, $risk = null, $group = null, $fix = null, $ip = null, $group_null = null){
        $by_fix_count = DB::table('nesresults')
            ->where('season_id','=',$sid)
            ->where('unfixable','<>',1);
        if ($status != null)
            $by_fix_count = $by_fix_count->whereIn('status', $status);
        if ($risk != null)
            $by_fix_count = $by_fix_count->whereIn('risk', $risk);

        if ($group !== null)
            if ($group_null)
                $by_fix_count = $by_fix_count->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $by_fix_count = $by_fix_count->whereIn('group_id',$group);
        else
            if ($group_null)
                $by_fix_count = $by_fix_count->whereNull('group_id');

        if ($fix != null)
            $by_fix_count = $by_fix_count->where('fix','=',$fix-1);
        if ($ip != null)
            $by_fix_count = $by_fix_count->whereIn('host', $ip);
        $by_fix_count = $by_fix_count->count();
        return $by_fix_count;

    }

    protected function addToVulnersQuery($res ,$status = null, $risk = null, $fix = null, $ip = null){
        $result = $res;
        $result = $result->where('unfixable','<>',1);
        if ($status != null)
            $result = $result->whereIn('status',$status);
        else 
            $result = $result->whereNull('status');
        if ($risk != null)
            $result = $result->whereIn('risk', $risk);
        else
            $result = $result->whereNull('risk');
        if ($fix != null)
            $result = $result->whereIn('fix', $fix);
        if ($ip != null)
            $result = $result->whereIn('host', $ip);

        $result = $result->orderBy('risk')->orderBy('host');
        return $result;
    }
    /*
    protected function getHostCount($cur_sid){
        //DB::enableQueryLog();
        $hostcount = Vulnerability::select('host')->where('season_id', $cur_sid)->groupBy('host')->get();
        //dd(DB::getQueryLog());
        return $hostcount;
    }*/

    protected function getVulnerableHosts($cur_sid){
        $vulnerhosts = Vulnerability::select('host')->where('season_id', $cur_sid)->groupBy('host')->get();
        return $vulnerhosts;
    }

    protected function getTopFive($cur_sid, $risk = null, $ip = null, $group = null, $group_null = null){
        //return $ip;
        //$topfive = DB::select("select count(`name`) as `counted`,`name` FROM `scan_result`.`nesresults` where `season_id` = :cur_sid group by `name` order by `counted` desc limit 5", ['cur_sid'=>$cur_sid]);
        $topfive = DB::table('nesresults')
            ->select(DB::raw("count(name) as counted, name"))
            ->where('season_id','=',$cur_sid)
            ->where('unfixable','<>',1);

        if ($ip !== null){
            $topfive = $topfive->whereIn('host',$ip);
        }
        if ($risk !== null){
            $topfive = $topfive->whereIn('risk',$risk);
        }

        if ($group !== null)
            if ($group_null)
                $topfive = $topfive->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $topfive = $topfive->whereIn('group_id',$group);
        else
            if ($group_null)
                $topfive = $topfive->whereNull('group_id');

        $topfive = $topfive->groupBy('name')
            ->orderBy('counted','DESC')
            ->take(5)->get();
        
        return $topfive;
    }

    protected function getTopFiveCritical($cur_sid, $ip = null, $group = null, $group_null = null){
        //$topfive = DB::select("select count(`name`) as `counted`,`name` FROM `scan_result`.`nesresults` where `season_id` = :cur_sid and `risk` = 'critical' group by `name` order by `counted` desc limit 5", ['cur_sid'=>$cur_sid]);
        $topfive = DB::table('nesresults')
            ->select(DB::raw("count(name) as counted, name"))
            ->where('season_id','=',$cur_sid)
            ->where('unfixable','<>',1)
            ->where('risk','=','critical');
        if ($ip !== null){
            $topfive = $topfive->whereIn('host',$ip);
        }
        if ($group !== null)
            if ($group_null)
                $topfive = $topfive->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $topfive = $topfive->whereIn('group_id',$group);
        else
            if ($group_null)
                $topfive = $topfive->whereNull('group_id');
        $topfive = $topfive->groupBy('name')
            ->orderBy('counted','DESC')
            ->take(5)->get();
        return $topfive;
    }

    protected function getVulnerFixedCount($sid, $risk = null, $ip = null, $group = null, $group_null){
        $fixed = DB::table('nesresults')
                ->where('season_id','=',$sid)
                ->where('unfixable','<>',1)
                ->where('fix','=','1');
        if ($ip !== null)
            $fixed = $fixed->whereIn('host',$ip);
        if ($risk !== null)
            $fixed = $fixed->whereIn('risk',$risk);
        if ($group !== null)
            if ($group_null)
                $fixed = $fixed->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $fixed = $fixed->whereIn('group_id',$group);
        else
            if ($group_null)
                $fixed = $fixed->whereNull('group_id');
        //dd($fixed->count());
        $fixed = $fixed->count();
        return $fixed;
    }

    protected function getVulnersCount($sid, $risk = null, $status = null, $fix = null, $group = null, $ip = null, $group_null = null){
        $vcount = DB::table('nesresults')
                ->where('season_id','=',$sid)
                ->where('unfixable','<>',1);
        if ($risk !== null)
            $vcount = $vcount->whereIn('risk', $risk);
        if ($status !== null)
            $vcount = $vcount->whereIn('status', $status);
        if ($fix !== null)
            $vcount = $vcount->whereIn('fix', $fix);
        if ($ip !== null) 
            $vcount = $vcount->whereIn('host', $ip);
        if ($group !== null)
            if ($group_null)
                $vcount = $vcount->where(function($query) use ($group) {
                    $query->whereIn('group_id',$group)->orWhereNull('group_id');
                });
            else
                $vcount = $vcount->whereIn('group_id',$group);
        else
            if ($group_null)
                $vcount = $vcount->whereNull('group_id');
        $vcount = $vcount->count();
        return $vcount;
    }

    public function result(Request $request){
        #return $this->getVulnerByStatusCount($request->season, null, null,25,0);
        //return $this->getHostCount($request->season);
        //return $this->getVulnerByStatusCount($request->season, null, null,7,0);
        $ipArray = null;
        $data = array();
        /*if (!$request->season)
            return response()->json(array('message' => 'Улирал хоосон байна.'), 422);
        if (!$request->risk)
            return response()->json(array('message' => 'Эрсдлийн түвшинг сонгоно уу.'), 422);
        if (!$request->fix)
            return response()->json(array('message' => 'Засварласан байдлаас сонгоно уу.'), 422);
        if (!$request->group && !$request->group_null)
            return response()->json(array('message' => 'Бүлгээс сонгоно уу.'), 422);*/

        $request->validate([
        	'season' => 'required|numeric',
        	'risk' => 'array|max:4|min:1',
        	'fix' => 'array|max:2|min:1',
        	/*'group_null' => 'required_without:group',*/
        	'group' => 'required_if:group_null,false',
        ]);
        
        $season = Season::find($request->season);//$this->getCurrentSeason($request->season_id);
        
        $data['season'] = $season->year." оны ".$season->season_name." улирал";
        if ($request->input('ip')){
            $ipArray = explode(",",preg_replace('/\s+/', '', $request->input('ip')));
            $data['hostcount'] = count($ipArray);
            //return $ipArray;
        }
        else{
            $data['hostcount'] = count($this->getVulnerableHosts($request->season));
        }
        
        $data['scandate'] = $request->date;
        $data['allcurrentvulners'] = $this->getAllFromVulnerCount($request->season, null, $request->risk, $ipArray,$request->group, $request->group_null);
        //return $data['allcurrentvulners'];
        
        //$allcurrentcritical = $this->getVulnerByRiskCount($request->season,"critical", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
        //$allcurrenthigh = $this->getVulnerByRiskCount($request->season,"high", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
        //$allcurrentmedium = $this->getVulnerByRiskCount($request->season,"medium", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
        //$allcurrentlow = $this->getVulnerByRiskCount($request->season,"low", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
        //return $allcurrentlow;
        
        /*Шинэ цоорхойнууд*/
        //$allcurrent0 = $this->getVulnerByStatusCount($request->season,"0", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        /*Хостын хувьд шинэ цоорхойнууд*/
        //$allcurrent1 = $this->getVulnerByStatusCount($request->season,"1", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        /*Дахин илэрсэн цоорхойнууд*/
        //$allcurrent2 = $this->getVulnerByStatusCount($request->season,"2", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        //return $allcurrent2;
        //$allcurrent4 = $this->getVulnerFixedCount($request->season, $request->risk, $ipArray,$request->group, $request->group_null);
        //return $allcurrent4;
        //dd(in_array("critical", $request->risk));
        /*Сонгоогүй түвшний цоорхойг 0 гэж харуулна*/
        /*Critical цоорхойнууд*/
        if (in_array("critical", $request->risk))
            $data['allcurrentcritical'] = $this->getVulnerByRiskCount($request->season,"critical", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);//$allcurrentcritical;
        else
            $data['allcurrentcritical'] = 0;
        /*High цоорхойнууд*/
        if (in_array("high", $request->risk))
            $data['allcurrenthigh'] = $this->getVulnerByRiskCount($request->season,"high", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);//$allcurrenthigh;
        else
            $data['allcurrenthigh'] = 0;
        /*Medium цоорхойнууд*/
        if (in_array("medium", $request->risk))
            $data['allcurrentmedium'] = $this->getVulnerByRiskCount($request->season,"medium", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);//$allcurrentmedium;
        else
            $data['allcurrentmedium'] = 0;
        /*Low цоорхойнууд*/
        if (in_array("low", $request->risk))
            $data['allcurrentlow'] = $this->getVulnerByRiskCount($request->season,"low", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);//$allcurrentlow;
        else
            $data['allcurrentlow'] = 0;
        /*Шинэ цоорхойнууд*/
        $data['allcurrent0'] = $this->getVulnerByStatusCount($request->season,"0", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        /*Хостын хувьд шинэ цоорхойнууд*/
        $data['allcurrent1'] = $this->getVulnerByStatusCount($request->season,"1", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        /*Дахин илэрсэн цоорхойнууд*/
        $data['allcurrent2'] = $this->getVulnerByStatusCount($request->season,"2", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
        /*Засагдсан цоорхойнууд*/
        $data['allcurrent4'] = $this->getVulnerFixedCount($request->season, $request->risk, $ipArray,$request->group, $request->group_null);
        
        /*Өнгөрсөн улиралтай харьцуулахын тулд өнгөрсөн улирлыг авч байна*/
        $prev_season = $this->getPrevSeason($request->season);
        /*Хэрэв өнгөрсөн улирал гэж байвал өнгөрсөн улирлын статусыг харуулах нөхцөл*/
        if ($prev_season){
            /*Өнгөрсөн улирал*/
            $data["prevseason"] = $prev_season->year." оны ".$prev_season->season_name." улирал";
            /*Өнгөрсөн улиралд илэрсэн нийт цоорхой*/
            $data["prevvulners"] = $this->getAllFromVulnerCount($prev_season->id, null, null, $ipArray);
            /*Өнгөрсөн улиралд илэрсэн цоорхойнуудыг сонгосон түвшингүүдээр тус бүрд нь тоолж, 
              сонгосон хэлтэс буюу бүлгүүдээр гаргана. Хэрэв түвшинг сонгоогүй бол 0 гэж гаргана.
            */
            if (in_array("critical", $request->risk))
                $data['allprevcritical'] = $this->getVulnerByRiskCount($prev_season->id,"critical", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
            else
                $data['allprevcritical'] = 0;
            if (in_array("high", $request->risk))
                $data['allprevhigh'] = $this->getVulnerByRiskCount($prev_season->id,"high", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
            else
                $data['allprevhigh'] = 0;
            if (in_array("medium", $request->risk))
                $data['allprevmedium'] = $this->getVulnerByRiskCount($prev_season->id,"medium", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
            else
                $data['allprevmedium'] = 0;
            if (in_array("low", $request->risk))
                $data['allprevlow'] = $this->getVulnerByRiskCount($prev_season->id,"low", $request->status, $request->group, $request->fix, $ipArray, $request->group_null);
            else
                $data['allprevlow'] = 0;
            /*---------------*/
            /*Өнгөрсөн улирлын цоорхойнууд шинэ, хостын хувьд шинэ, дахин илэрсэн гэсэн төлөвөөр*/
            $data['allprev0'] = $this->getVulnerByStatusCount($prev_season->id,"0", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
            $data['allprev1'] = $this->getVulnerByStatusCount($prev_season->id,"1", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
            $data['allprev2'] = $this->getVulnerByStatusCount($prev_season->id,"2", $request->risk, $request->group, $request->fix, $ipArray, $request->group_null);
            /*Өнгөрсөн улиралд нийт засварласан тоо*/
            $data['allprev4'] = $this->getVulnerFixedCount($prev_season->id, $request->risk, $ipArray,$request->group, $request->group_null);
            
            //return $data['allprev4'];
        }
        /*Хамгийн их илэрсэн 5 цоорхой*/
        $data['topfive'] = $this->getTopFive($request->season, $request->risk, $ipArray, $request->group, $request->group_null);
        /*Хамгийн их илэсэн 5 critical цоорхой*/
        if (in_array("critical", $request->risk))
            $data['topfivecrit'] = $this->getTopFiveCritical($request->season, $ipArray, $request->group, $request->group_null);
        else
            $data['topfivecrit'] = null;

        $data['groups'] = null;
        $data['header'] = $request->header;
        $data['headgraph'] = $request->headgraph;
        $data['ips'] = str_replace(",",", ",preg_replace('/\s+/', '', $request->input('ip')));
        //return $data['header'];
        if ($request->group){
            foreach ($request->group as $gr){
                $group = Group::find($gr);
                $result = DB::table('nesresults');
                $result = $result->join('seasons as ss','detected_sid','=','ss.id');
                $result = $result->where('season_id','=',$request->season)->where('group_id','=', $gr);
                $result = $this->addToVulnersQuery($result, $request->status, $request->risk, $request->fix, $ipArray);
                $result = $result->select('cvss','cve','risk','host','protocol','port','name','synopsis','status','fix','year','season_name');
                #$a = $result->get();
                #return $result->toSql();
                
                $data['depbydep'][$group->name]['vulners'] = $result->get();

                /*Тухайн улиралд илэрсэн шинэ эмзэг байдлуудыг түвшингээр нь харуулах*/
                $data['depbydep'][$group->name]['newall'] = $this->getVulnerByStatusCount($request->season,"0", $request->risk, [$gr], $request->fix, $ipArray);
                if (in_array('critical', $request->risk))
                    $data['depbydep'][$group->name]['newcrit'] = $this->getVulnerByStatusCount($request->season,"0", ['critical'], [$gr], $request->fix, $ipArray);
                else
                    $data['depbydep'][$group->name]['newcrit'] = 0;
                if (in_array('high', $request->risk))
                    $data['depbydep'][$group->name]['newhigh'] = $this->getVulnerByStatusCount($request->season,"0", ['high'], [$gr], $request->fix, $ipArray);
                else
                    $data['depbydep'][$group->name]['newhigh'] = 0;
                if (in_array('medium', $request->risk))
                    $data['depbydep'][$group->name]['newmed'] = $this->getVulnerByStatusCount($request->season,"0", ['medium'], [$gr], $request->fix, $ipArray);
                else
                    $data['depbydep'][$group->name]['newmed'] = 0;
                if (in_array('low', $request->risk))
                    $data['depbydep'][$group->name]['newlow'] = $this->getVulnerByStatusCount($request->season,"0", ['low'], [$gr], $request->fix, $ipArray);
                else
                    $data['depbydep'][$group->name]['newlow'] = 0;
                /*Газар хэлтэс буюу бүлгүүдийн нэрийг авна*/
                if ($data['groups'] == null)
                    $data['groups'] = $group->name;
                else
                    $data['groups'] .= ",".$group->name;
                /*------------------------*/
                /*Өмнөх улирлын цоорхойнуудыг түвшингээр нь тоолж авна*/
                if ($prev_season){
                    $data['depbydep'][$group->name]['prevall'] = $this->getVulnersCount($prev_season->id, $request->risk, $request->status, $request->fix, [$gr], $ipArray);
                    if (in_array('critical', $request->risk))
                        $data['depbydep'][$group->name]['prevcrit'] = $this->getVulnerByRiskCount($prev_season->id, ['critical'], $request->status, [$gr], $request->fix, $ipArray);
                    else
                        $data['depbydep'][$group->name]['prevcrit'] = 0;
                    if (in_array('high', $request->risk))
                        $data['depbydep'][$group->name]['prevhigh'] = $this->getVulnerByRiskCount($prev_season->id, ['high'], $request->status, [$gr], $request->fix, $ipArray);
                    else
                        $data['depbydep'][$group->name]['prevhigh'] = 0;
                    if (in_array('medium', $request->risk))
                        $data['depbydep'][$group->name]['prevmed'] = $this->getVulnerByRiskCount($prev_season->id, ['medium'], $request->status, [$gr], $request->fix, $ipArray);
                    else
                        $data['depbydep'][$group->name]['prevmed'] = 0;
                    if (in_array('low', $request->risk))
                        $data['depbydep'][$group->name]['prevlow'] = $this->getVulnerByRiskCount($prev_season->id, ['low'], $request->status, [$gr], $request->fix, $ipArray);
                    else
                        $data['depbydep'][$group->name]['prevlow'] = 0;
                }
                if (count($request->risk) > 0)
                    foreach ($request->risk as $risk){
                        /*Эмзэг байдлын түвшин бүрээр эмзэг байдлын тоог гаргана*/
                        $data['depbydep'][$group->name][$risk] = $this->getVulnerByRiskCount($request->season,$risk, $request->status, [$gr], $request->fix, $ipArray);
                    }
                if (count($request->status) > 0)
                    foreach ($request->status as $status) {
                        /*Илэрсэн байдал тус бүрээр эмзэг байдлын тоог гаргана*/
                        $data['depbydep'][$group->name]['stat'.$status] = $this->getVulnerByStatusCount($request->season, $status, $request->risk, [$gr], $request->fix, $ipArray);
                    }

                if (count($request->fix) > 0){
                    foreach ($request->fix as $fix) {
                        /*Засварласан болон засварлаагүй тоо*/
                        $data['depbydep'][$group->name]["f".$fix] = $this->getVulnerByFixCount($request->season, $request->status, $request->risk, [$gr], $fix+1, $ipArray);
                        if($fix == 1){
                            if (in_array('critical', $request->risk))
                                $data['depbydep'][$group->name]['fixcrit'] = $this->getVulnerByFixCount($request->season, $request->status, ['critical'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['fixcrit'] = 0;
                            if (in_array('high', $request->risk))
                                $data['depbydep'][$group->name]['fixhigh'] = $this->getVulnerByFixCount($request->season, $request->status, ['high'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['fixhigh'] = 0;
                            if (in_array('medium', $request->risk))
                                $data['depbydep'][$group->name]['fixmed'] = $this->getVulnerByFixCount($request->season, $request->status, ['medium'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['fixmed'] = 0;
                            if (in_array('low', $request->risk))
                                $data['depbydep'][$group->name]['fixlow'] = $this->getVulnerByFixCount($request->season, $request->status, ['low'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['fixlow'] = 0;
                            /*Өнгөрсөн улиралд засварласан тоог vulnerability-ийн түвшингээр нь тоолно*/
                            if ($prev_season){
                                $data['depbydep'][$group->name]["pf".$fix] = $this->getVulnerByFixCount($prev_season->id, $request->status, $request->risk, [$gr], $fix+1, $ipArray);
                                if (in_array('critical', $request->risk))
                                    $data['depbydep'][$group->name]['prevfixcrit'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['critical'], [$gr], $fix+1, $ipArray);
                                else 
                                    $data['depbydep'][$group->name]['prevfixcrit'] = 0;
                                if (in_array('high', $request->risk))
                                    $data['depbydep'][$group->name]['prevfixhigh'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['high'], [$gr], $fix+1, $ipArray);
                                else
                                    $data['depbydep'][$group->name]['prevfixhigh'] = 0;
                                if (in_array('medium', $request->risk))
                                    $data['depbydep'][$group->name]['prevfixmed'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['medium'], [$gr], $fix+1, $ipArray);
                                else
                                    $data['depbydep'][$group->name]['prevfixmed'] = 0;
                                if (in_array('low', $request->risk))
                                    $data['depbydep'][$group->name]['prevfixlow'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['low'], [$gr], $fix+1, $ipArray);
                                else 
                                    $data['depbydep'][$group->name]['prevfixlow'] = 0;
                            }
                        }
                        if($fix == 0){
                            if (in_array('critical', $request->risk))
                                $data['depbydep'][$group->name]['unfixcrit'] = $this->getVulnerByFixCount($request->season, $request->status, ['critical'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['fixcrit'] = 0;
                            if (in_array('high', $request->risk))
                                $data['depbydep'][$group->name]['unfixhigh'] = $this->getVulnerByFixCount($request->season, $request->status, ['high'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['unfixhigh'] = 0;
                            if (in_array('medium', $request->risk))
                                $data['depbydep'][$group->name]['unfixmed'] = $this->getVulnerByFixCount($request->season, $request->status, ['medium'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['unfixmed'] = 0;
                            if (in_array('low', $request->risk))
                                $data['depbydep'][$group->name]['unfixlow'] = $this->getVulnerByFixCount($request->season, $request->status, ['low'], [$gr], $fix+1, $ipArray);
                            else
                                $data['depbydep'][$group->name]['unfixlow'] = 0;
                            if ($prev_season){
                                $data['depbydep'][$group->name]["pf".$fix] = $this->getVulnerByFixCount($prev_season->id, $request->status, $request->risk, [$gr], $fix+1, $ipArray);
                                if (in_array('critical', $request->risk))
                                    $data['depbydep'][$group->name]['prevunfixcrit'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['critical'], [$gr], $fix+1, $ipArray);
                                else 
                                    $data['depbydep'][$group->name]['prevunfixcrit'] = 0;
                                if (in_array('high', $request->risk))
                                    $data['depbydep'][$group->name]['prevunfixhigh'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['high'], [$gr], $fix+1, $ipArray);
                                else
                                    $data['depbydep'][$group->name]['prevunfixhigh'] = 0;
                                if (in_array('medium', $request->risk))
                                    $data['depbydep'][$group->name]['prevunfixmed'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['medium'], [$gr], $fix+1, $ipArray);
                                else
                                    $data['depbydep'][$group->name]['prevunfixmed'] = 0;
                                if (in_array('low', $request->risk))
                                    $data['depbydep'][$group->name]['prevunfixlow'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['low'], [$gr], $fix+1, $ipArray);
                                else 
                                    $data['depbydep'][$group->name]['prevunfixlow'] = 0;
                            }
                        }
                    }
                    
                }
            }
        }
        //return "success";
        /*Бүлэгт хамрагдаагүй цоорхойнууд*/
        if ($request->group_null){
            $result = DB::table('nesresults');
            $result = $result->join('seasons as ss','detected_sid','=','ss.id');
            $result = $result->where('season_id','=',$request->season)->whereNull('group_id');
            $result = $this->addToVulnersQuery($result, $request->status, $request->risk, $request->fix, $ipArray);
            $result = $result->select('cvss','cve','risk','host','protocol','port','name','synopsis','status','fix','year','season_name');

            $data['depbydep']['Without Group']['vulners'] = $result->get();
            $data['depbydep']['Without Group']['newall'] = $this->getVulnerByStatusCount($request->season,"0", $request->risk, null, $request->fix, $ipArray, $request->group_null);
                if (in_array('critical', $request->risk))
                    $data['depbydep']['Without Group']['newcrit'] = $this->getVulnerByStatusCount($request->season,"0", ['critical'], null, $request->fix, $ipArray, $request->group_null);
                else
                    $data['depbydep']['Without Group']['newcrit'] = 0;
                if (in_array('high', $request->risk))
                    $data['depbydep']['Without Group']['newhigh'] = $this->getVulnerByStatusCount($request->season,"0", ['high'], null, $request->fix, $ipArray, $request->group_null);
                else
                    $data['depbydep']['Without Group']['newhigh'] = 0;
                if (in_array('medium', $request->risk))
                    $data['depbydep']['Without Group']['newmed'] = $this->getVulnerByStatusCount($request->season,"0", ['medium'], null, $request->fix, $ipArray, $request->group_null);
                else
                    $data['depbydep']['Without Group']['newmed'] = 0;
                if (in_array('low', $request->risk))
                    $data['depbydep']['Without Group']['newlow'] = $this->getVulnerByStatusCount($request->season,"0", ['low'], null, $request->fix, $ipArray, $request->group_null);
                else
                    $data['depbydep']['Without Group']['newlow'] = 0;

            /*Өмнөх улирлын цоорхойнуудыг түвшингээр нь тоолж авна*/
                if ($prev_season){
                    $data['depbydep']['Without Group']['prevall'] = $this->getVulnersCount($prev_season->id, $request->risk, $request->status, $request->fix, null, $ipArray, $request->group_null);
                    if (in_array('critical', $request->risk))
                        $data['depbydep']['Without Group']['prevcrit'] = $this->getVulnerByRiskCount($prev_season->id, ['critical'], $request->status, null, $request->fix, $ipArray, $request->group_null);
                    else
                        $data['depbydep']['Without Group']['prevcrit'] = 0;
                    if (in_array('high', $request->risk))
                        $data['depbydep']['Without Group']['prevhigh'] = $this->getVulnerByRiskCount($prev_season->id, ['high'], $request->status, null, $request->fix, $ipArray, $request->group_null);
                    else
                        $data['depbydep']['Without Group']['prevhigh'] = 0;
                    if (in_array('medium', $request->risk))
                        $data['depbydep']['Without Group']['prevmed'] = $this->getVulnerByRiskCount($prev_season->id, ['medium'], $request->status, null, $request->fix, $ipArray, $request->group_null);
                    else
                        $data['depbydep']['Without Group']['prevmed'] = 0;
                    if (in_array('low', $request->risk))
                        $data['depbydep']['Without Group']['prevlow'] = $this->getVulnerByRiskCount($prev_season->id, ['low'], $request->status, null, $request->fix, $ipArray, $request->group_null);
                    else
                        $data['depbydep']['Without Group']['prevlow'] = 0;
                }

            if (count($request->risk) > 0)
                foreach ($request->risk as $risk){
                    $data['depbydep']['Without Group'][$risk] = $this->getVulnerByRiskCount($request->season,$risk, $request->status, null, $request->fix, $ipArray, $request->group_null);
                }
            if (count($request->status) > 0)
                foreach ($request->status as $status) {
                    $data['depbydep']['Without Group']['stat'.$status] = $this->getVulnerByStatusCount($request->season, $status, $request->risk, null, $request->fix, $ipArray, $request->group_null);
                }
            if (count($request->fix) > 0){
                foreach ($request->fix as $fix) {
                    $data['depbydep']['Without Group']["f".$fix] = $this->getVulnerByFixCount($request->season, $request->status, $request->risk, null, $fix+1, $ipArray, $request->group_null);
                    if ($fix == 1){
                        if (in_array('critical', $request->risk))
                            $data['depbydep']['Without Group']['fixcrit'] = $this->getVulnerByFixCount($request->season, $request->status, ['critical'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['fixcrit'] = 0;
                        if (in_array('high', $request->risk))
                            $data['depbydep']['Without Group']['fixhigh'] = $this->getVulnerByFixCount($request->season, $request->status, ['high'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['fixhigh'] = 0;
                        if (in_array('medium', $request->risk))
                            $data['depbydep']['Without Group']['fixmed'] = $this->getVulnerByFixCount($request->season, $request->status, ['medium'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['fixmed'] = 0;
                        if (in_array('low', $request->risk))
                            $data['depbydep']['Without Group']['fixlow'] = $this->getVulnerByFixCount($request->season, $request->status, ['low'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['fixlow'] = 0;
                        if ($prev_season){
                            $data['depbydep']['Without Group']["pf".$fix] = $this->getVulnerByFixCount($prev_season->id, $request->status, $request->risk, null, $fix+1, $ipArray, $request->group_null);
                            if (in_array('critical', $request->risk))
                                $data['depbydep']['Without Group']['prevfixcrit'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['critical'], null, $fix+1, $ipArray, $request->group_null);
                            else 
                                $data['depbydep']['Without Group']['prevfixcrit'] = 0;
                            if (in_array('high', $request->risk))
                                $data['depbydep']['Without Group']['prevfixhigh'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['high'], null, $fix+1, $ipArray, $request->group_null);
                            else
                                $data['depbydep']['Without Group']['prevfixhigh'] = 0;
                            if (in_array('medium', $request->risk))
                                $data['depbydep']['Without Group']['prevfixmed'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['medium'], null, $fix+1, $ipArray, $request->group_null);
                            else
                                $data['depbydep']['Without Group']['prevfixmed'] = 0;
                            if (in_array('low', $request->risk))
                                $data['depbydep']['Without Group']['prevfixlow'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['low'], null, $fix+1, $ipArray, $request->group_null);
                            else 
                                $data['depbydep']['Without Group']['prevfixlow'] = 0;
                        }
                    }
                    if ($fix == 0){
                        if (in_array('critical', $request->risk))
                            $data['depbydep']['Without Group']['unfixcrit'] = $this->getVulnerByFixCount($request->season, $request->status, ['critical'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['unfixcrit'] = 0;
                        if (in_array('high', $request->risk))
                            $data['depbydep']['Without Group']['unfixhigh'] = $this->getVulnerByFixCount($request->season, $request->status, ['high'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['unfixhigh'] = 0;
                        if (in_array('medium', $request->risk))
                            $data['depbydep']['Without Group']['unfixmed'] = $this->getVulnerByFixCount($request->season, $request->status, ['medium'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['unfixmed'] = 0;
                        if (in_array('low', $request->risk))
                            $data['depbydep']['Without Group']['unfixlow'] = $this->getVulnerByFixCount($request->season, $request->status, ['low'], null, $fix+1, $ipArray, $request->group_null);
                        else
                            $data['depbydep']['Without Group']['unfixlow'] = 0;
                        if ($prev_season){
                            $data['depbydep']['Without Group']["pf".$fix] = $this->getVulnerByFixCount($prev_season->id, $request->status, $request->risk, null, $fix+1, $ipArray, $request->group_null);
                            if (in_array('critical', $request->risk))
                                $data['depbydep']['Without Group']['prevunfixcrit'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['critical'], null, $fix+1, $ipArray, $request->group_null);
                            else 
                                $data['depbydep']['Without Group']['prevunfixcrit'] = 0;
                            if (in_array('high', $request->risk))
                                $data['depbydep']['Without Group']['prevunfixhigh'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['high'], null, $fix+1, $ipArray, $request->group_null);
                            else
                                $data['depbydep']['Without Group']['prevunfixhigh'] = 0;
                            if (in_array('medium', $request->risk))
                                $data['depbydep']['Without Group']['prevunfixmed'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['medium'], null, $fix+1, $ipArray, $request->group_null);
                            else
                                $data['depbydep']['Without Group']['prevunfixmed'] = 0;
                            if (in_array('low', $request->risk))
                                $data['depbydep']['Without Group']['prevunfixlow'] = $this->getVulnerByFixCount($prev_season->id, $request->status, ['low'], null, $fix+1, $ipArray, $request->group_null);
                            else 
                                $data['depbydep']['Without Group']['prevunfixlow'] = 0;
                        }
                    }
                                  
                }
            }
        }/*
        //$lava = new Lavacharts;
        $riskcharttable = \Lava::DataTable();
        $riskcharttable->addStringColumn('risk')
                       ->addNumberColumn('number');
        $riskcharttable->addRow(['critical', $data['allcurrentcritical']]) //$allcurrentcritical])
                       ->addRow(['high', $data['allcurrenthigh']])//$allcurrenthigh])
                       ->addRow(['medium', $data['allcurrentmedium']]) //$allcurrentmedium])
                       ->addRow(['low', $data['allcurrentlow']]);//$allcurrentlow]);
        \Lava::PieChart('allbyrisk', $riskcharttable, [
            'title'  => 'Эрсдлээр',
            'is3D'   => false,
            //'slices' => [
            //    ['offset' => 0.1],
            //    ['offset' => 0.1],
            //    ['offset' => 0.1]
            //]
        ]);
        $statuscharttable = \Lava::DataTable();
        $statuscharttable->addStringColumn('status')
                         ->addNumberColumn('number');
        $statuscharttable->addRow(['Шинэ',$allcurrent0])
                         ->addRow(['Хостын хувьд шинэ',$allcurrent1])
                         ->addRow(['Засагдаагүй',$allcurrent2]);
        \Lava::PieChart('allbystatus', $statuscharttable, [
            'title' => 'Илэрсэн байдлаар']);*/
        //return $lava->render('PieChart', 'IMDB', 'chart-div');
        //$data['chart'] = $lava;
        
        
        /*$table = \Lava::DataTable();
        $table->addStringColumn('status')
                ->addNumberColumn('number');
        $table->addRow(['A','3'])
              ->addRow(['B','4'])
              ->addRow(['c','2']);
        \Lava::PieChart('test', $table, [
            'title' => 'Test']);*/
        //$data['lava'] = $lava;
        //return $data['Without Group']['vulners'];
        //return $data;
    	//return view('vulnerabilities.report')->with('data',$data);
        return response()->json(array('data' => $data));
    }
}
