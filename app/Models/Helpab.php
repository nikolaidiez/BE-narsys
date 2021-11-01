<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helpab extends Model
{
    //
    protected $fillable = [
        'id', 'an_p1' => 'string', 'an_p2' => 'string', 'an_p3' => 'string', 'created_at', 'updated_at'
    ];

    protected $primaryKey = 'id';
}
