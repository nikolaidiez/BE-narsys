<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helpab extends Model
{
    //
    protected $fillable = [
        'id', 'ab_p1' => 'string', 'ab_p2' => 'string', 'ab_p3' => 'string', 'sco_p1', 'sco_p2', 'sco_p3', 'created_at', 'updated_at'
    ];

    protected $primaryKey = 'id';
}
