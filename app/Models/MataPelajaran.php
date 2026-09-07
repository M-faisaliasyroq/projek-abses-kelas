<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['kode_mapel', 'nama_mapel', 'deskripsi'])]
class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';

    public function jadwal(): HasMany
    {
        return $this->hasMany(Jadwal::class);
    }
}
