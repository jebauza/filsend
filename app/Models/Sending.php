<?php

namespace App\Models;

use App\User;
use App\Models\File;
use Illuminate\Database\Eloquent\Model;

class Sending extends Model
{
    protected $table = 'sendings';

    protected $fillable = ['from_user', 'to_user', 'file_id', 'message'];

    //Relations
    public function file()
    {
        return $this->belongsTo(File::class, 'file_id', 'id');
    }

    public function from()
    {
        return $this->belongsTo(User::class, 'from_user', 'id');
    }

    public function to()
    {
        return $this->belongsTo(User::class, 'to_user', 'id');
    }

}
