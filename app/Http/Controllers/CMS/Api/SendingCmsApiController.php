<?php

namespace App\Http\Controllers\CMS\Api;

use App\Models\File;
use App\Models\Sending;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Storage;

class SendingCmsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $auth_user = $request->user();
        return $auth_user->sent_files()->with('file','to_user')->latest()->paginate();
    }

    public function received(Request $request)
    {
        $auth_user = $request->user();
        return $auth_user->received_files()->with('file','from_user')->latest()->paginate();
    }

    public function can_send_users(Request $request)
    {
        $auth_user = $request->user();
        $blocked_users_id = $auth_user->blocked_users()->get()->pluck('id');
        $users_blocked_me_id = $auth_user->users_blocked_me()->get()->pluck('id');
        $users = User::where('id', '!=', $auth_user->id)
                        ->whereNotIn('id', $blocked_users_id->merge($users_blocked_me_id)->toArray())
                        ->get();

        return $users;
    }

    public function users_not_blocked(Request $request)
    {
        $auth_user = $request->user();
        $blocked_users_id = $auth_user->blocked_users()->get()->pluck('id');
        $users = User::where('id', '!=', $auth_user->id)
                        ->whereNotIn('id', $blocked_users_id->toArray())
                        ->get();

        return $users;
    }

    public function blocked_users(Request $request)
    {
        $auth_user = $request->user();

        return $auth_user->blocked_users()->orderBY('email')->get();
    }

    public function lock_unlock(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'action' => 'required|string|in:lock,unlock'
        ]);

        $user = User::findOrfail($request->user_id);
        if($request->action == 'lock') {
            $request->user()->blocked_users()->attach($user->id);
            return response()->json(['msg'=> __('Successfully blocked')], 200);
        }else {
            $request->user()->blocked_users()->detach($user->id);
            return response()->json(['msg'=> __('Successfully unlocked')], 200);
        }
    }

    public function store(Request $request)
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
                if($new_sending->save()) {
                    DB::commit();
                    return response()->json(['msg'=>__('Save successfully'), 'sending' => $new_sending], 201);
                }
            }

        } catch (\Exception $e) {
            DB::rollBack();
            if($path && Storage::exists($path)){
                Storage::delete($path);
            }
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
