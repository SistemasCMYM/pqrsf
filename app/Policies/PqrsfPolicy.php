<?php

namespace App\Policies;

use App\Models\Pqrsf;
use App\Models\User;

class PqrsfPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Administrador', 'Admin PQRSF', 'Gestor PQRSF', 'Asesor']);
    }

    public function view(User $user, Pqrsf $pqrsf): bool
    {
        if ($user->hasAnyRole(['Administrador', 'Admin PQRSF', 'Gestor PQRSF'])) {
            return true;
        }

        return $user->hasRole('Asesor') && $user->document_number === $pqrsf->numero_documento;
    }

    public function update(User $user, Pqrsf $pqrsf): bool
    {
        return $user->hasAnyRole(['Administrador', 'Admin PQRSF', 'Gestor PQRSF']);
    }

    public function delete(User $user, Pqrsf $pqrsf): bool
    {
        return $user->hasRole('Administrador');
    }
}
