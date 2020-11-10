<?php

namespace App\Models;

use App\Models\Sending;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = ['name', 'path'];

    //protected $appends = ['url'];

    //Attributes
    /* function getUrlAttribute()
    {
        return Storage::exists($this->path) ? asset(Storage::url($this->path)) : null;
    } */

    //Relations
    public function sendings()
    {
        return $this->hasMany(Sending::class, 'file_id', 'id');
    }
}
