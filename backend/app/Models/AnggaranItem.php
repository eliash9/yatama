<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnggaranItem extends Model
{
    protected $fillable = [
        'anggaran_id','account_code','description','pagu','notes'
    ];
    public function anggaran() { return $this->belongsTo(Anggaran::class); }
}
