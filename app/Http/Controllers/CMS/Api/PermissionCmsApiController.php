<?php

namespace App\Http\Controllers\CMS\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;

class PermissionCmsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::all();
        return $permissions;
    }

    public function getAllPermissions()
    {
        $permissions = Permission::orderBy('name')->get();
        return $permissions;
    }

    public function authUserAllPermissions(Request $request)
    {
        return $request->user()->getAllPermissions();
    }


}
