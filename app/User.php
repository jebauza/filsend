<?php

namespace App;

use App\Models\File;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstname', 'secondname', 'lastname', 'username', 'email'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['fullName', 'urlProfilePicture'];

    //Attributes
    function getFullNameAttribute()
    {
        return $this->firstname .' '.($this->secondname ?? '').' '. $this->lastname;
    }

    //Attributes
    function getUrlProfilePictureAttribute()
    {
        if(config('filesystems.default') == 's3'){
            return Storage::exists($this->profile_picture) ? Storage::temporaryUrl($this->profile_picture, now()->addMinutes(5)) : null;
        }else {
            return Storage::exists($this->profile_picture) ? asset(Storage::url($this->profile_picture)) : null;
        }
    }

    //Scopes
    public function scopeEmail($query, $email)
    {
        if($email){
            return $query->where('email', 'like', "%$email%");
        }
    }

    public function scopeUserName($query, $username)
    {
        if($username){
            return $query->where('username', 'like', "%$username%");
        }
    }

    public function scopeName($query, $name)
    {
        if($name){
            return $query->whereRaw("CONCAT_WS(' ', firstname, secondname, lastname) like '%$name%'");
        }
    }

    public function scopeState($query, $state)
    {
        if($state){
            return $query->where('state', $state);
        }
    }
}
