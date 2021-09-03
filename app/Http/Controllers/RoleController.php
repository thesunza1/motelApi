<?php

namespace App\Http\Controllers;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    //
    public function index(Role $role) {
        $roles = $role->loadMissing(['users.role']);
        return new RoleResource($roles);
    }
}
