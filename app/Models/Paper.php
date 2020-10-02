<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paper extends Model
{
    protected $fillable = ['user_id', 'paper_title', 'paper_file_name', 'paper_file_path'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
