<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Criteria extends Model
{
    protected $fillable = [
        'id', 'ketCriteria', 'bobot', 'created_at', 'updated_at'
    ];

    protected $hidden = [];
}
