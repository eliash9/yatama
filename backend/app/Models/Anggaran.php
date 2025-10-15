<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    protected $fillable = [
        'unit_id','periode_id','total_pagu','notes','status'
    ];

    public function unit() { return $this->belongsTo(Unit::class); }
    public function periode() { return $this->belongsTo(Periode::class); }
    public function items() { return $this->hasMany(AnggaranItem::class); }
}
