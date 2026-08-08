<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'divisi';
    protected $primaryKey = 'id_divisi';
    public $timestamps = false;
    protected $guarded = [];

    public function karyawan()
    {
        return $this->belongsToMany(Karyawan::class, 'karyawan_divisi', 'id_divisi', 'id_karyawan');
    }
}
