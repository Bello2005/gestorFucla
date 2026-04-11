<?php

namespace Database\Seeders;

use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use Illuminate\Database\Seeder;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        if (CatalogoPrograma::count() === 0) {
            CatalogoPrograma::insert([
                ['nombre' => 'Ingeniería de Sistemas', 'facultad' => 'Ingeniería', 'activo' => true, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Derecho', 'facultad' => 'Ciencias Jurídicas', 'activo' => true, 'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if (CatalogoTipoProyecto::count() === 0) {
            CatalogoTipoProyecto::insert([
                ['nombre' => 'Investigación', 'descripcion' => null, 'activo' => true, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['nombre' => 'Extensión', 'descripcion' => null, 'activo' => true, 'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
        if (CatalogoLineaInvestigacion::count() === 0) {
            CatalogoLineaInvestigacion::insert([
                ['nombre' => 'Educación y desarrollo social', 'area' => 'Social', 'activo' => true, 'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
