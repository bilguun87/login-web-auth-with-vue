<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
//use App\Http\Resources\SeasonResource;
use App\Models\Season;
use App\Models\Group;

class VulnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {   
        $detected = Season::find($this->detected_sid);
        $allocatedGroup = Group::find($this->group_id);
        return [
            'id' => $this->id,
            'p_id' => $this->p_id,
            'cve' => $this->cve,
            'cvss' => $this->cvss,
            'risk' => $this->risk,
            'host' => $this->host,
            'protocol' => $this->protocol,
            'port' => $this->port,
            'name' => $this->name,
            'group_id' => $this->group_id,
            'group_name' => $allocatedGroup ? $allocatedGroup->name : "",
            'synopsis' => $this->synopsis,
            'season_id' => $this->season_id,
            'season_year' => $this->year,
            'season_name' => $this->season_name,
            'detected_sid' => $this->detected_sid,
            'detected_year' => $detected ? $detected->year : "",
            'detected_sname' => $detected ? $detected->season_name : "",
            'fix' => $this->fix
        ];
        //return parent::toArray($request);
    }
}
