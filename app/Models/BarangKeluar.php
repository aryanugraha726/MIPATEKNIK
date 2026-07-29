<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model {
    protected $table = 'barang_keluar';
    protected $primaryKey = 'id_keluar';
    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang', 'id_barang');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'job_id', 'job_id');
    }
}