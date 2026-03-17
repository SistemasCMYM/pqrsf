<?php

namespace Database\Seeders;

use App\Models\HoldingCompany;
use Illuminate\Database\Seeder;

class HoldingCompanySeeder extends Seeder
{
    public function run(): void
    {
        HoldingCompany::query()->firstOrCreate(
            ['slug' => 'syso'],
            [
                'name' => 'SYSO',
                'tagline' => 'Servicios integrados para el crecimiento empresarial',
                'intro' => 'Aquí puedes registrar tus PQRSF, consultar tu estado de cuenta y agendar una sesión de soporte con un consultor.',
                'support_booking_url' => env('SUPPORT_BOOKING_URL'),
                'is_default' => true,
                'active' => true,
            ]
        );
    }
}
