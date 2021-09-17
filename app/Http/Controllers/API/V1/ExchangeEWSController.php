<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class ExchangeEWSController extends Controller
{
    protected function curl(){
        
        $response = [];
        $url = env('EXCH_SERVICE_URL','');
        $domain = env('EXCH_SERVICE_DOMAIN','');
        $username = env('EXCH_SERVICE_USER','');
        $password = env('EXCH_SERVICE_PASS','');
        $ch = curl_init($url);
        $payloads = json_encode(array('domain' => $domain,'username' => $username, 'password' => $password));
        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLINFO_HEADER_OUT => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_POSTFIELDS => $payloads,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array('Content-Type: application/json')
        );
        curl_setopt_array($ch, $options);
        $execresponse = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $execresponse = ($status == 200)?json_decode($execresponse):$execresponse;
        $response = [
            'status'  => $status,
            'err'     => curl_errno( $ch ), 
            'content' => $execresponse,
            'errmsg'  => curl_error( $ch )
        ];
        curl_close($ch);
        return $response;
    }

    protected function ConverToArray($arryofObjects){
        $convertedArray = [];
        foreach($arryofObjects as $object){
            array_push($convertedArray, (array)$object);
        }
        return $convertedArray;
    }

    public function getActiveSyncOWAEnabledUsers(Request $request){
        $request->validate(['page' => 'required|numeric','show' => Rule::in(['all','activesync','owa'])]);
        $data = [];
        if (!Cache::get('exchresponse')){
            $response = $this->curl();
            if ($response['status'] == 200){
                $converted = $this->ConverToArray($response['content']);
                // dd($converted);
                Cache::put('exchresponse', $converted, now()->addMinutes(30));
            }
            else{
            return response()->json(['message' => $response['errmsg']], 500);
            }
        }
        if ($request->show == 'activesync'){
            $collection = collect(Cache::Get('exchresponse'));
            $data = $collection->filter(function($value, $key) {
                return $value['activesyncenabled'] == "True";
            })->all();

        }
        else if ($request->show == 'owa'){
            $collection = collect(Cache::Get('exchresponse'));
            $data = $collection->filter(function($value, $key) {
                return $value['owaenabled'] == "True";
            })->all();
        }
        else
            $data = Cache::Get('exchresponse');
        
        $paginated = $this->convertPagination($data, $request->page);
        return response()->json($paginated);
        
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
}
