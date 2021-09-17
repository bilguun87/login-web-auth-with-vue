<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GroupMatrixResource extends JsonResource
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
            'br_id' => $this->br_id,
            'br_name' => ($this->br_name == "")?"All":$this->br_name,
            'pos_id' => $this->pos_id,
            'pos_name' => ($this->pos_name == "")?"All":$this->pos_name,
            'objectguid' => $this->objectguid,
            'cn' => $this->cn,
        ];
        // return parent::toArray($request);
    }
}
