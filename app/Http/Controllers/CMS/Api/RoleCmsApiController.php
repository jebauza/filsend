<?php

namespace App\Http\Controllers\CMS\Api;

use App\Models\Rol;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\RoleStoreUpdateRequest;

class RoleCmsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Rol::orderBy('name')->paginate();

        return $roles;
    }

    public function getAllRoles(Request $request)
    {
        $roles = Rol::orderBy('name')->get();

        return $roles;
    }

    public function getPermissionsByRole(Request $request, $id)
    {
        if(!$role = Role::find($id)){
            return response()->json(['msg_error' => __('Not found')], 404);
        }

        return $role->permissions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreUpdateRequest $request)
    {
        try {
            DB::beginTransaction();

            $role = Rol::create(['name' => $request->name]);
            $role->syncPermissions($request->permissions);

            DB::commit();
            return response()->json(['msg'=>__('Save successfully'), 'role'=>$role->refresh()], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['msg_error' => $e->getMessage()], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleStoreUpdateRequest $request, $id)
    {
        if(!$role = Role::find($id)){
            return response()->json(['msg_error' => __('Not found')], 404);
        }

        try {
            DB::beginTransaction();

            $role->name = $request->name;
            $role->save();
            $role->syncPermissions($request->permissions);

            DB::commit();
            return response()->json(['msg'=>__('Save successfully'), 'role'=>$role], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['msg_error' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
