<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pencairan extends Model
{
    protected $fillable = [
        'pengajuan_id','nomor_doc','tanggal','metode','total_dicairkan','catatan'
    ];
    public function pengajuan(){ return $this->belongsTo(Pengajuan::class); }
}
