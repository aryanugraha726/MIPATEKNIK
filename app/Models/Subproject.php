<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subproject extends Model
{
    protected $table = 'subproject';
    protected $primaryKey = 'subproject_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'subproject_id', 'subproject_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function management()
    {
        return $this->belongsTo(Management::class, 'management_id', 'management_id');
    }
}
