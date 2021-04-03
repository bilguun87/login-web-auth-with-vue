<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Group;

class HostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $group = Group::find($this->group_id);
        return [
            'id' => $this->id,
            'ip' => $this->ip,
            'desc' => $this->description,
            'group_id' => $this->group_id,
            'group_name' => $group ? $group->name : "",
        ];
        //return parent::toArray($request);
    }
}
