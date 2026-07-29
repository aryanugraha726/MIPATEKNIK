<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialRequestDetail extends Model {
    protected $table = 'material_request_detail';
    protected $primaryKey = 'id_req_detail';
    public $timestamps = false;
    protected $guarded = [];

    public function materialRequest()
    {
        return $this->belongsTo(MaterialRequest::class, 'no_nota', 'no_nota');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}
