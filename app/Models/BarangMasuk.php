<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model {
    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_masuk';
    public $timestamps = false;
    protected $guarded = []; // Mengizinkan penyimpanan massal (Mass Assignment)

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}