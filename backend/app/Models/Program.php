<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = [
        'code','name','category','type','unit_id','description','start_date','end_date','target_amount','status'
    ];

    public function unit(){ return $this->belongsTo(Unit::class); }
}

