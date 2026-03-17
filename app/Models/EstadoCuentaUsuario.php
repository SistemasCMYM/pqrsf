<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstadoCuentaUsuario extends Model
{
    use HasFactory;

    protected $table = 'estado_cuenta_usuarios';

    protected $fillable = ['user_id', 'cedula', 'nombre_asesor', 'activo'];

    protected function casts(): array
    {
        return ['activo' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
