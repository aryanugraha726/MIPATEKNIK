<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model {
    protected $table = 'material_request';
    protected $primaryKey = 'no_nota';
    public $timestamps = false;
    protected $guarded = [];

    public function workOrderRelease()
    {
        return $this->belongsTo(WorkOrderRelease::class, 'job_id', 'job_id');
    }

    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'id_divisi', 'id_divisi');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    public function details()
    {
        return $this->hasMany(MaterialRequestDetail::class, 'no_nota', 'no_nota');
    }
}
