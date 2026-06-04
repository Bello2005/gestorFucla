<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            ['slug' => 'proyectos',   'label' => 'Proyectos Activos',  'sort_order' => 1, 'read_only' => false],
            ['slug' => 'banco',       'label' => 'Banco de Proyectos', 'sort_order' => 2, 'read_only' => false],
            ['slug' => 'estadistica', 'label' => 'Estadísticas',       'sort_order' => 3, 'read_only' => true],
            ['slug' => 'auditoria',   'label' => 'Auditoría',          'sort_order' => 4, 'read_only' => true],
            ['slug' => 'usuarios',    'label' => 'Usuarios',           'sort_order' => 5, 'read_only' => true],
            ['slug' => 'solicitudes', 'label' => 'Solicitudes',        'sort_order' => 6, 'read_only' => true],
            ['slug' => 'catalogos',   'label' => 'Catálogos',          'sort_order' => 7, 'read_only' => true],
        ];

        foreach ($modules as $module) {
            Module::updateOrCreate(
                ['slug' => $module['slug']],
                ['label' => $module['label'], 'sort_order' => $module['sort_order'], 'read_only' => $module['read_only']]
            );
        }
    }
}
