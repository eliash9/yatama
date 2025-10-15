<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'code','type','name','email','phone','address','tax_id','is_active'
    ];
}

