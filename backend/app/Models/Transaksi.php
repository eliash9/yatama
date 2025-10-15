<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    protected $fillable = [
        'tanggal','jenis','akun_kas','account_id','amount','ref_type','ref_id','program_id','category','memo','reconciled_at'
    ];
    public function account(){ return $this->belongsTo(Account::class); }
    public function program(){ return $this->belongsTo(Program::class); }
}
