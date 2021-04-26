<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MoveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'requester_name' => $this->requester_name,
            'performer_name' => $this->performer_name,
            'move_start_date' => $this->move_start_date,
            'place_id' => $this->place_id,
            'record_id' => $this->record_id,
            'place_name' => $this->place->name,
            'record_name' => $this->record->name,
            'description' => $this->description,
        ];
        //return parent::toArray($request);
    }
}
