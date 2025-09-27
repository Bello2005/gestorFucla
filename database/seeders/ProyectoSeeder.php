<?php

namespace Database\Seeders;

use App\Models\Proyecto;
use Illuminate\Database\Seeder;

class ProyectoSeeder extends Seeder
{
    public function run(): void
    {
        $proyectos = [
            [
                'nombre_del_proyecto' => 'Proyecto de Ejemplo 1',
                'objeto_contractual' => 'Desarrollo de software educativo',
                'lineas_de_accion' => 'Educación, Tecnología',
                'cobertura' => 'Nacional',
                'entidad_contratante' => 'Ministerio de Educación',
                'fecha_de_ejecucion' => now(),
                'plazo' => 12.00,
                'valor_total' => 150000000.00,
                'estado' => 'activo',
            ],
            [
                'nombre_del_proyecto' => 'Proyecto de Ejemplo 2',
                'objeto_contractual' => 'Investigación en energías renovables',
                'lineas_de_accion' => 'Investigación, Medio Ambiente',
                'cobertura' => 'Regional',
                'entidad_contratante' => 'Gobernación del Chocó',
                'fecha_de_ejecucion' => now()->addMonths(1),
                'plazo' => 6.00,
                'valor_total' => 75000000.00,
                'estado' => 'inactivo',
            ],
            [
                'nombre_del_proyecto' => 'Proyecto de Ejemplo 3',
                'objeto_contractual' => 'Programa de capacitación docente',
                'lineas_de_accion' => 'Educación, Desarrollo Profesional',
                'cobertura' => 'Departamental',
                'entidad_contratante' => 'Secretaría de Educación',
                'fecha_de_ejecucion' => now()->subMonths(2),
                'plazo' => 3.00,
                'valor_total' => 45000000.00,
                'estado' => 'cerrado',
            ],
        ];

        foreach ($proyectos as $proyecto) {
            Proyecto::create($proyecto);
        }
    }
}