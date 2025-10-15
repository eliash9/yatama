<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'actor_id','action','ref_type','ref_id','changes','created_at'
    ];
}
