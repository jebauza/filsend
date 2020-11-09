<?php

namespace App\Http\Controllers\CMS\Api;

use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Sending;
use Illuminate\Support\Facades\Storage;

class FileCmsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function send(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'to_user_id' => 'required|integer',
            'message' => 'nullable|string'
        ]);

        $path = null;
        try {
            DB::beginTransaction();
            $authUser = $request->user();

            $new_file = new File();
            $new_file->name = $request->file('file')->getClientOriginalName();
            $file_name = Str::random(10)."_".Str::limit($new_file->name, 200);
            $path = Storage::putFileAs('public/files', $request->file('file'), $file_name);
            $new_file->path = $path;
            if($new_file->save()) {
                $new_sending = new Sending([
                    'from_user' => $authUser->id,
                    'to_user' => $request->to_user_id,
                    'file_id' => $new_file->id,
                    'message' => $request->message
                ]);
                if($new_sending->save) {

                }
                $new_file->sendings()->save($new_sending);
            }

            $new_user->created_by = $authUser->id;
            $new_user->updated_by = $authUser->id;
            $new_user->save();
            $new_user->syncRoles($request->roles);
            $base_permission_ids = DB::table('permissions')
                                    ->whereIn('name', config('filsend.basic_user_permissions'))
                                    ->pluck('id')->toArray();
            $new_user->syncPermissions(array_merge($request->permissions ?? [], $base_permission_ids));

            DB::commit();
            return response()->json(['msg'=>__('Save successfully'), 'user'=>$new_user->refresh()], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            if($path && Storage::exists($path)){
                Storage::delete($path);
            }
            return response()->json(['msg_error' => $e->getMessage()], 500);
        }



    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {
        //
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
