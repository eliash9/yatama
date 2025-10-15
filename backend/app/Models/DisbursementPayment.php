<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DisbursementPayment extends Model
{
    protected $fillable = [
        'disbursement_id','channel','account_id','amount','paid_at','recipient_name','bank_name','account_no','ewallet_id','ref_no','receipt_url','created_by'
    ];
    public function disbursement(){ return $this->belongsTo(Disbursement::class); }
    public function account(){ return $this->belongsTo(Account::class); }
}

