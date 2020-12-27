<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'show'];
    protected $hidden = ['created_at', 'updated_at'];
}
