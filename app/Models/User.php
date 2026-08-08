<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['user_id', 'username', 'password', 'id_karyawan', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    /**
     * Get user's roles dynamically from divisions they manage.
     * If they don't manage any division, they are just a 'KARYAWAN'.
     */
    public function roles(): array
    {
        if (!$this->karyawan) {
            return ['KARYAWAN'];
        }

        $isManager = \App\Models\Management::where('id_karyawan', $this->id_karyawan)->exists();

        $divisiNames = [];
        if ($isManager && $this->karyawan->divisi->isNotEmpty()) {
            $divisiNames = array_merge($divisiNames, $this->karyawan->divisi->pluck('nama_divisi')->toArray());
        }

        if (empty($divisiNames)) {
            return ['KARYAWAN'];
        }

        // Anyone who manages a division automatically gets MANAGEMENT and KARYAWAN roles
        $divisiNames[] = 'MANAGEMENT';
        $divisiNames[] = 'KARYAWAN';

        return array_unique($divisiNames);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'id_karyawan', 'id_karyawan');
    }

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
