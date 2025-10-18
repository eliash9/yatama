<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
    protected $fillable = [
        'code','type','name','date_of_birth','guardian_name','national_id','family_card_no','gender','education','occupation','guardian_phone','email','phone','address','city','province','postal_code','notes','is_active'
    ];
}
