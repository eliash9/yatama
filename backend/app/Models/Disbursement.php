<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    protected $fillable = [
        'code','program_id','beneficiary_id','requested_by','amount','method_preference','purpose','status','submitted_at','assessed_by','assessed_note','assessed_at'
    ];

    public function program(){ return $this->belongsTo(Program::class); }
    public function beneficiary(){ return $this->belongsTo(Beneficiary::class); }
    public function requester(){ return $this->belongsTo(User::class,'requested_by'); }
    public function approvals(){ return $this->hasMany(DisbursementApproval::class); }
    public function payments(){ return $this->hasMany(DisbursementPayment::class); }
}

