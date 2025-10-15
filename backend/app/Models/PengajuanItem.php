<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanItem extends Model
{
    protected $fillable = [
        'pengajuan_id','account_code','description','qty','unit_price','subtotal','anggaran_item_id'
    ];
    public function pengajuan(){ return $this->belongsTo(Pengajuan::class); }
    public function anggaranItem(){ return $this->belongsTo(AnggaranItem::class,'anggaran_item_id'); }
}
