<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    protected $fillable = [
        'pengajuan_id','approver_id','level','status','note','decided_at'
    ];
    public function pengajuan(){ return $this->belongsTo(Pengajuan::class); }
    public function approver(){ return $this->belongsTo(User::class,'approver_id'); }
}
