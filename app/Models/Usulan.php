<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usulan extends Model
{
    //
    protected $fillable = [
        'id', 'nim', 'nipPA' => 'string', 'tahun', 'judul', 'abstraksi', 'status', 'rekomendasi', 'judulFinal', 'bidangIlmu', 'metPen', 'pemb01', 'pemb02', 'peng01', 'peng02', 'tglSemPro', 'wktSemPro', 'ruangSemPro', 'tglSemAkhir', 'wktSemAkhir', 'ruangSemAkhir', 'created_at', 'updated_at'
    ];
}
