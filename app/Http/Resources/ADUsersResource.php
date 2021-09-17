<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ADUsersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        $groups = [];
        foreach ($this->groups[0] as $gr)
            array_push($groups, [
                    $gr->cn[0], 
                    (count($gr->objectguid) > 0)?$this->_to_p_guid($gr->objectguid[0]):null
                ]);
        // var_dump($this->description);
        return [
            'name' => $this->name[0],
            'displayname' => (gettype($this->displayname) == 'array')?((count($this->displayname) > 0 )?$this->displayname[0]:null):null,
            'samaccountname' => $this->samaccountname[0],
            'groups' => $groups,
            'department' => (gettype($this->department) == 'array')?((count($this->department) > 0)?$this->department[0]:null):null,
            'description' => (gettype($this->description) == 'array')?((count($this->description) > 0)?$this->description[0]:null):null,
        ];
    }


    protected function _to_p_guid( $guid )
    {
        $hex_guid = unpack( "H*hex", $guid );
        $hex    = $hex_guid["hex"];

        $hex1   = substr( $hex, -26, 2 ) . substr( $hex, -28, 2 ) . substr( $hex, -30, 2 ) . substr( $hex, -32, 2 );
        $hex2   = substr( $hex, -22, 2 ) . substr( $hex, -24, 2 );
        $hex3   = substr( $hex, -18, 2 ) . substr( $hex, -20, 2 );
        $hex4   = substr( $hex, -16, 4 );
        $hex5   = substr( $hex, -12, 12 );

        $guid = $hex1 . "-" . $hex2 . "-" . $hex3 . "-" . $hex4 . "-" . $hex5;

        return $guid;
    }
}
