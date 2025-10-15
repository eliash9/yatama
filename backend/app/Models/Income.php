<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'receipt_no','tanggal','channel','amount','donor_id','program_id','status','ref_no','notes'
    ];

    public function donor(){ return $this->belongsTo(Donor::class); }
    public function program(){ return $this->belongsTo(Program::class); }
}

