<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PO extends Model {
    protected $table = 'po';
    protected $primaryKey = 'no_po';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'id_vendor', 'id_vendor');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'id_lokasi', 'id_lokasi');
    }

    public function workOrderRelease()
    {
        return $this->belongsTo(WorkOrderRelease::class, 'job_id', 'job_id');
    }

    public function details()
    {
        return $this->hasMany(PODetail::class, 'no_po', 'no_po');
    }
}
