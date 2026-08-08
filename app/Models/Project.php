<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    protected $table = 'project';
    protected $primaryKey = 'job_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function subprojects()
    {
        return $this->hasMany(Subproject::class, 'job_id', 'job_id');
    }

    public function management()
    {
        return $this->belongsTo(Management::class, 'management_id', 'management_id');
    }

    public function workOrderRelease()
    {
        return $this->belongsTo(WorkOrderRelease::class, 'job_id', 'job_id');
    }
}