<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAktual extends Model
{
    // 1. Memberitahu Laravel nama View yang benar di database
    protected $table = 'view_stock_aktual';

    // 2. Mematikan fitur pencarian kolom created_at dan updated_at
    public $timestamps = false;

    public function satuan()
    {
        return $this->belongsTo(Satuan::class, 'id_satuan', 'id_satuan');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }
}