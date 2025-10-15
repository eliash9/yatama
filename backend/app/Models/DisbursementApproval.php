<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbursementApproval extends Model
{
    protected $fillable = [
        'disbursement_id','level','approver_id','status','note','decided_at'
    ];
    public function disbursement(){ return $this->belongsTo(Disbursement::class); }
    public function approver(){ return $this->belongsTo(User::class,'approver_id'); }
}

