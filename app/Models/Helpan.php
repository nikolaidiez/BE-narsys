<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Helpan extends Model
{
    //
    protected $fillable = [
        'id', 'an_p1' => 'string', 'an_p2' => 'string', 'an_p3' => 'string', 'an_p4' => 'string', 'sc_p1', 'sc_p2', 'sc_p3', 'sc_p4', 'created_at', 'updated_at'
    ];

    protected $primaryKey = 'id';
}
