<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderRelease extends Model {
    protected $table = 'work_order_release';
    protected $primaryKey = 'job_id';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;
    protected $guarded = [];

    public function project()
    {
        return $this->hasOne(Project::class, 'job_id', 'job_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
