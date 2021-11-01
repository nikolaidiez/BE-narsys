<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jatah extends Model
{
    protected $fillable = [
        'nip', 'ab_p1', 'ab_p2', 'ab_p3', 'an_p1', 'an_p2', 'an_p3' 
    ];

    protected $primaryKey = 'nip';
    protected $keyType = 'string';
}
