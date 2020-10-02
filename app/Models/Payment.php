<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillbale = ['user_id', 'payment_title', 'payment_description', 'payment_file_name', 'payment_file_path'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
