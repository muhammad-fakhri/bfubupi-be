<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['code', 'value'];
    protected $hidden = ['created_at', 'updated_at'];
}
