<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderReleaseDetail extends Model
{
    use HasFactory;

    protected $table = 'work_order_release_details';
    protected $guarded = [];

    public function workOrderRelease()
    {
        return $this->belongsTo(WorkOrderRelease::class, 'job_id', 'job_id');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
