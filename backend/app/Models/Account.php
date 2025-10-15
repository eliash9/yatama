<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'code','name','type','bank_name','account_no','opening_balance','is_active'
    ];

    public function transactions(){ return $this->hasMany(Transaksi::class); }
}

