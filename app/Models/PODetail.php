<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PODetail extends Model {
    protected $table = 'po_detail';
    protected $primaryKey = 'id_po_detail';
    public $timestamps = false;
    protected $guarded = [];

    public function po()
    {
        return $this->belongsTo(PO::class, 'no_po', 'no_po');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }
}
