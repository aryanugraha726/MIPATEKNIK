<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WorkOrderRelease extends Model {
    protected $table = 'work_order_release';
    protected $primaryKey = 'job_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function project()
    {
        return $this->hasOne(Project::class, 'job_id', 'job_id');
    }

    public function details()
    {
        return $this->hasMany(WorkOrderReleaseDetail::class, 'job_id', 'job_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}
