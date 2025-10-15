<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengajuan extends Model
{
    protected $fillable = [
        'kode','unit_id','periode_id','pemohon_id','judul','deskripsi','total_diminta','status','submitted_at'
    ];
    public function unit(){ return $this->belongsTo(Unit::class); }
    public function periode(){ return $this->belongsTo(Periode::class); }
    public function pemohon(){ return $this->belongsTo(User::class,'pemohon_id'); }
    public function items(){ return $this->hasMany(PengajuanItem::class); }
    public function approvals(){ return $this->hasMany(Approval::class); }
    public function pencairans(){ return $this->hasMany(Pencairan::class); }
}
