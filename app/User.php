<?php

namespace App;

use App\Models\File;
use App\Models\Sending;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;
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

    protected static function booted()
    {
        $auth_user = auth()->user();
        static::addGlobalScope('active_user', function (Builder $builder) use($auth_user){
            if($auth_user && !$auth_user->can('users.activate'))
            {
                $builder->where('state', 'A');
            }
        });
    }

    protected $appends = ['fullName', 'urlProfilePicture'];

    //Attributes
    function getFullNameAttribute()
    {
        return $this->firstname .' '.($this->secondname ?? '').' '. $this->lastname;
    }

    function getUrlProfilePictureAttribute()
    {
        if(config('filesystems.default') == 's3'){
            return Storage::exists($this->profile_picture) ? Storage::temporaryUrl($this->profile_picture, now()->addMinutes(30)) : null;
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

    //Relations
    public function sent_files()
    {
        return $this->hasMany(Sending::class, 'from_user', 'id');
    }

    public function received_files()
    {
        return $this->hasMany(Sending::class, 'to_user', 'id');
    }

    public function blocked_users()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'from_user', 'to_user')->withTimestamps();
    }

    public function users_blocked_me()
    {
        return $this->belongsToMany(User::class, 'blocked_users', 'to_user', 'from_user')->withTimestamps();
    }

}
