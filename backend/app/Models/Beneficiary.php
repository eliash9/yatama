<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'code','type','name','date_of_birth','guardian_name','email','phone','address','notes','is_active'
    ];
}

