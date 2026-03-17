<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PqrsfEstado extends Model
{
    use HasFactory;

    protected $table = 'pqrsf_estados';

    protected $fillable = ['nombre', 'slug', 'color', 'es_cierre', 'activo'];

    protected function casts(): array
    {
        return ['es_cierre' => 'boolean', 'activo' => 'boolean'];
    }

    public function pqrsf(): HasMany
    {
        return $this->hasMany(Pqrsf::class);
    }
}
