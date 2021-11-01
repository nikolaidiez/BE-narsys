<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scoring extends Model
{
    protected $fillable = [
        'id', 'nip' => 'string', 'idCriteria', 'idVariabel', 'score', 'created_at', 'updated_at'
    ];

    protected $hidden = [];
    // protected $primaryKey = 'nip';
    // protected $keyType = 'string';
}
