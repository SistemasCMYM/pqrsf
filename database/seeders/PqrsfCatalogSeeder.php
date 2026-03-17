<?php

namespace Database\Seeders;

use App\Models\ParametrizacionSla;
use App\Models\PqrsfDestinatario;
use App\Models\PqrsfEstado;
use App\Models\PqrsfTipo;
use App\Models\User;
use Illuminate\Database\Seeder;

class PqrsfCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Petición', 'slug' => 'peticion', 'dias_sla' => 15],
            ['nombre' => 'Queja', 'slug' => 'queja', 'dias_sla' => 10],
            ['nombre' => 'Reclamo', 'slug' => 'reclamo', 'dias_sla' => 15],
            ['nombre' => 'Sugerencia', 'slug' => 'sugerencia', 'dias_sla' => 20],
            ['nombre' => 'Felicitación', 'slug' => 'felicitacion', 'dias_sla' => 20],
        ];

        foreach ($tipos as $tipo) {
            $item = PqrsfTipo::query()->firstOrCreate(
                ['slug' => $tipo['slug']],
                ['nombre' => $tipo['nombre'], 'dias_sla' => $tipo['dias_sla'], 'activo' => true]
            );

            ParametrizacionSla::query()->firstOrCreate(
                ['pqrsf_tipo_id' => $item->id, 'prioridad' => 'media'],
                ['dias_respuesta' => $tipo['dias_sla'], 'activo' => true]
            );
        }

        $estados = [
            ['nombre' => 'Radicada', 'slug' => 'radicada', 'color' => 'blue', 'es_cierre' => false],
            ['nombre' => 'En revisión', 'slug' => 'en-revision', 'color' => 'yellow', 'es_cierre' => false],
            ['nombre' => 'Asignada', 'slug' => 'asignada', 'color' => 'indigo', 'es_cierre' => false],
            ['nombre' => 'En gestión', 'slug' => 'en-gestion', 'color' => 'purple', 'es_cierre' => false],
            ['nombre' => 'Pendiente de información', 'slug' => 'pendiente-informacion', 'color' => 'orange', 'es_cierre' => false],
            ['nombre' => 'Respondida', 'slug' => 'respondida', 'color' => 'green', 'es_cierre' => false],
            ['nombre' => 'Cerrada', 'slug' => 'cerrada', 'color' => 'emerald', 'es_cierre' => true],
            ['nombre' => 'Vencida', 'slug' => 'vencida', 'color' => 'red', 'es_cierre' => false],
        ];

        foreach ($estados as $estado) {
            PqrsfEstado::query()->firstOrCreate(['slug' => $estado['slug']], $estado);
        }

        $defaultResponsible = User::query()->where('email', 'analista@pqrsf.local')->value('id');
        foreach (['Gestión Humana', 'Operaciones', 'Abastecimiento', 'Tecnología'] as $nombre) {
            PqrsfDestinatario::query()->firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($nombre)],
                ['nombre' => $nombre, 'responsable_user_id' => $defaultResponsible, 'activo' => true]
            );
        }
    }
}
