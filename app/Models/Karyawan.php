<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    protected $table = 'karyawan';
    protected $primaryKey = 'id_karyawan';
    public $timestamps = false;
    protected $guarded = [];

    public function divisi()
    {
        return $this->belongsToMany(Divisi::class, 'karyawan_divisi', 'id_karyawan', 'id_divisi');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'id_karyawan', 'id_karyawan');
    }

    public function subproject()
    {
        return $this->hasMany(Subproject::class, 'id_karyawan', 'id_karyawan');
    }
}
