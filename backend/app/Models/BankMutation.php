<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankMutation extends Model
{
    protected $fillable = [
        'tanggal','description','amount','channel','ref_no','matched_income_id'
    ];

    public function income(){ return $this->belongsTo(Income::class,'matched_income_id'); }
}

