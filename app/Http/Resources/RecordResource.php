<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RecordResource extends JsonResource
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
            'name' => $this->name,
            'ogson' => $this->ogson,
            'awsan' => $this->awsan,
            'date' => $this->date,
            'place_id' => $this->place_id,
            'type_id' => $this->type_id,
            'place_name' => $this->place->name,
            'type_name' => $this->type->name,
        ];
        //return parent::toArray($request);
    }
}
