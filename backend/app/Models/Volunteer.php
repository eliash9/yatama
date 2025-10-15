<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Volunteer extends Model
{
    protected $fillable = [
        'code','name','email','phone','address','skills','joined_at','is_active'
    ];
}

