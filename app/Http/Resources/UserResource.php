<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Role;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $roles = [];
        $perms = [];
        $permsviaroles = [];

        foreach ($this->roles as $role){
            array_push($roles, $role->name);
            foreach ($role->permissions as $perm)
                array_push($permsviaroles, $perm);
        }
        foreach ($this->permissions as $perm)
            array_push($perms, $perm->name);

        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'domain'    => $this->domain,
            'email'     => $this->email,
            /*'roles1'     => $this->getRoleNames(),
            'permissions1'     => $this->getPermissionNames(),
            'permissionsviaroles'     => $this->getPermissionsViaRoles(),*/
            'roles'     => $roles,
            'permissions'     => $perms,
            'permissionsviaroles'     => $permsviaroles,
        ];
    }
}
