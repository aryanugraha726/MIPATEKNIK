<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    protected $table = 'tugas';
    protected $primaryKey = 'tugas_id';
    protected $fillable = [
        'tugas_id', 'subproject_id', 'tugas', 'start_tugas', 'target_tugas', 'is_completed', 'tanggal_selesai', 'id_karyawan', 'id_vendor'
    ];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function subproject()
    {
        return $this->belongsTo(Subproject::class, 'subproject_id', 'subproject_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }
}
