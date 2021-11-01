<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Variabel extends Model
{
    protected $fillable = [
        'kode', 'kode_induk', 'keterangan', 'batas', 'created_at', 'updated_at'
    ];

    protected $hidden = [];
}
