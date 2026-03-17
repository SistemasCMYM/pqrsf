<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory;
    use HasRoles;
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'document_type',
        'document_number',
        'phone',
        'city',
        'is_external',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_external' => 'boolean',
        ];
    }

    public function pqrsfAsignadas(): HasMany
    {
        return $this->hasMany(Pqrsf::class, 'asignado_a');
    }

    public function estadoCuentaVinculos(): HasMany
    {
        return $this->hasMany(EstadoCuentaUsuario::class);
    }
}
