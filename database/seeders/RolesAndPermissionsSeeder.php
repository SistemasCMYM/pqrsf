<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Administrador', 'Asesor', 'Gestor PQRSF', 'Admin PQRSF', 'Coordinador Estado Cuenta'];

        foreach ($roles as $role) {
            Role::query()->firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@pqrsf.local'],
            [
                'name' => 'Administrador General',
                'password' => Hash::make('password'),
                'document_type' => 'CC',
                'document_number' => '10000001',
                'status' => 'active',
            ]
        );

        $analista = User::query()->firstOrCreate(
            ['email' => 'analista@pqrsf.local'],
            [
                'name' => 'Gestor PQRSF',
                'password' => Hash::make('password'),
                'document_type' => 'CC',
                'document_number' => '10000002',
                'status' => 'active',
            ]
        );

        $externo = User::query()->firstOrCreate(
            ['email' => 'asesor@pqrsf.local'],
            [
                'name' => 'Asesor Externo',
                'password' => Hash::make('password'),
                'document_type' => 'CC',
                'document_number' => '123456789',
                'is_external' => true,
                'status' => 'active',
            ]
        );

        $admin->syncRoles(['Administrador', 'Admin PQRSF']);
        $analista->syncRoles(['Gestor PQRSF']);
        $externo->syncRoles(['Asesor']);
    }
}
