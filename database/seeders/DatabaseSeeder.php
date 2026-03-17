<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            HoldingCompanySeeder::class,
            PqrsfCatalogSeeder::class,
            EstadoCuentaDemoSeeder::class,
        ]);
    }
}
