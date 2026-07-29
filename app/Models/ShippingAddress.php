<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model {
    protected $table = 'shipping_address';
    protected $primaryKey = 'id_lokasi';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $guarded = [];
}
