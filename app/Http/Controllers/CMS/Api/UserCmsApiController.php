<?php

namespace App\Http\Controllers\CMS\Api;

use App\User;
use App\Models\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UserStoreUpdateRequest;

class UserCmsApiController extends Controller
{
    public function index(Request $request)
    {
        $users = User::email($request->email)->userName($request->username)->name($request->name)
                    ->state($request->state)->with('permissions','roles')->orderBy('username')
                    ->paginate();

        return $users;
    }

    public function store(UserStoreUpdateRequest $request)
    {
        $path = null;
        try {
            DB::beginTransaction();
            $authUser = $request->user();

            $new_user = new User($request->all());
            $new_user->secondname = $request->secondname ?? null;
            $new_user->password = Hash::make($request->password);
            if($request->file('image')) {
                $image_name = Str::random(10)."_".Str::limit($new_user->fullName, 200).(strrchr($request->file('image')->getClientOriginalName(), ".") ?? '');
                $path = Storage::putFileAs('public/users', $request->file('image'), $image_name);
                $new_user->profile_picture = $path;
            }
            $new_user->created_by = $authUser->id;
            $new_user->updated_by = $authUser->id;
            $new_user->save();
            $new_user->syncRoles($request->roles);
            $new_user->syncPermissions($request->permissions);

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

    public function update(UserStoreUpdateRequest $request, $id)
    {
        $path = null;
        $user = User::findOrfail($id);
        Gate::authorize('users.updateAndShow', ['users.update', $user]);

        try {
            DB::beginTransaction();
            $authUser = $request->user();

            $user->firstname = $request->firstname;
            $user->secondname = $request->secondname ?? null;
            $user->lastname = $request->lastname;
            $user->username = $request->username;
            $user->email = $request->email;
            if($request->password) {
                $user->password = Hash::make($request->password);
            }
            if($request->file('image')) {
                $image_name = Str::random(10)."_".Str::limit($user->fullName, 200).(strrchr($request->file('image')->getClientOriginalName(), ".") ?? '');
                $path = Storage::putFileAs('public/users', $request->file('image'), $image_name);
                if($user->profile_picture && Storage::exists($user->profile_picture)) {
                    Storage::delete($user->profile_picture);
                }
                $user->profile_picture = $path;
            }
            $user->updated_by = $authUser->id;
            $user->save();
            if($user->can('users.update')) {
                $user->syncRoles($request->roles);
                $user->syncPermissions($request->permissions);
            }

            DB::commit();
            return response()->json(['msg'=>__('Save successfully'), 'user'=>$user->refresh()], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            if($path && Storage::exists($path)){
                Storage::delete($path);
            }
            return response()->json(['msg_error' => $e->getMessage()], 500);
        }
    }

    public function setState(Request $request, $id)
    {
        $user = User::findOrfail($id);
        $request->validate([
            'state' => 'required|in:A,I'
        ]);

        if (($request->state == 'A' && !Gate::allows('users.set-state', 'users.activate'))
        || ($request->state == 'I' && !Gate::allows('users.set-state', 'users.deactivate'))) {
            return response()->json(['msg_error' => __('Forbidden access')], 403);
        }

        $user->state = $request->state;
        $user->updated_by = $request->user()->id;
        if($user->save()) {
            return response()->json([
                'msg'=>__('User :state successfully', ['state'=>__($user->state == 'A' ? 'activated' : 'deactivated')]),
                'user'=>$user
            ], 200);
        }else {
            return response()->json(['msg_error' => __('Internal Server Error')], 500);
        }
    }

    public function show(Request $request, $id)
    {
        $user = User::findOrfail($id);
        Gate::authorize('users.updateAndShow', ['users.show', $user]);
        return $user;
    }

    public function getPermissions(Request $request, $id)
    {
        $user = User::findOrfail($id);
        return $user->getAllPermissions();
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
