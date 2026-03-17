<?php

namespace App\Policies;

use App\Models\EstadoCuentaResumen;
use App\Models\User;

class EstadoCuentaResumenPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Administrador', 'Coordinador Estado Cuenta', 'Asesor']);
    }

    public function view(User $user, EstadoCuentaResumen $resumen): bool
    {
        if ($user->hasAnyRole(['Administrador', 'Coordinador Estado Cuenta'])) {
            return true;
        }

        return $user->document_number === $resumen->cedula;
    }
}
