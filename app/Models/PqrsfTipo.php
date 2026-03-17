<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PqrsfTipo extends Model
{
    use HasFactory;

    protected $table = 'pqrsf_tipos';

    protected $fillable = ['nombre', 'slug', 'descripcion', 'dias_sla', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function pqrsf(): HasMany
    {
        return $this->hasMany(Pqrsf::class);
    }
}
