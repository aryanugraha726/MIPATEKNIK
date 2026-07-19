<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    protected $table = 'project';
    protected $primaryKey = 'project_id';
    public $timestamps = false;
    protected $guarded = [];

    public function subprojects()
    {
        return $this->hasMany(Subproject::class, 'project_id', 'project_id');
    }

    public function management()
    {
        return $this->belongsTo(Management::class, 'management_id', 'management_id');
    }
}