<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lampiran extends Model
{
    protected $fillable = [
        'ref_type','ref_id','filename','mime','size','url','uploader_id','uploaded_at'
    ];
}
