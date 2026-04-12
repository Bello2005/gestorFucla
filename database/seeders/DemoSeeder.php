<?php

namespace Database\Seeders;

use App\Models\AccessRequest;
use App\Models\Audit;
use App\Models\BancoProyecto;
use App\Models\BancoProyectoHistorial;
use App\Models\CatalogoLineaInvestigacion;
use App\Models\CatalogoPrograma;
use App\Models\CatalogoTipoProyecto;
use App\Models\Proyecto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'test1@uniclaretiana.edu.co')->first();
        $super = User::where('email', 'test2@uniclaretiana.edu.co')->first();
        $user  = User::where('email', 'test3@uniclaretiana.edu.co')->first();

        // ── Catálogos ─────────────────────────────────────────────────────────
        DB::table('catalogo_programas')->truncate();
        CatalogoPrograma::insert([
            ['nombre' => 'Ingeniería de Sistemas',    'facultad' => 'Ingeniería',          'activo' => true,  'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Administración de Empresas','facultad' => 'Ciencias Económicas',  'activo' => true,  'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Derecho',                   'facultad' => 'Ciencias Jurídicas',   'activo' => true,  'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Licenciatura en Ciencias',  'facultad' => 'Ciencias de la Educ.','activo' => true,  'orden' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Trabajo Social',            'facultad' => 'Ciencias Sociales',    'activo' => true,  'orden' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Contaduría Pública',        'facultad' => 'Ciencias Económicas',  'activo' => false, 'orden' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('catalogo_tipos_proyecto')->truncate();
        CatalogoTipoProyecto::insert([
            ['nombre' => 'Investigación Formativa',   'descripcion' => 'Proyectos de formación investigativa', 'activo' => true,  'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Extensión Universitaria',   'descripcion' => 'Proyectos de impacto social',          'activo' => true,  'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Investigación Aplicada',    'descripcion' => 'Soluciones a problemas concretos',     'activo' => true,  'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Interinstitucional',        'descripcion' => 'Convenios con entidades externas',     'activo' => true,  'orden' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Consultorías y Asesorías',  'descripcion' => null,                                   'activo' => false, 'orden' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        DB::table('catalogo_lineas_investigacion')->truncate();
        CatalogoLineaInvestigacion::insert([
            ['nombre' => 'Educación y Desarrollo Social',     'area' => 'Social',       'activo' => true,  'orden' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Tecnología e Innovación',           'area' => 'Tecnología',   'activo' => true,  'orden' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Salud y Bienestar Comunitario',     'area' => 'Salud',        'activo' => true,  'orden' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Gestión Ambiental y Sostenibilidad','area' => 'Ambiental',    'activo' => true,  'orden' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Derechos Humanos y Justicia',       'area' => 'Jurídica',     'activo' => true,  'orden' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Emprendimiento y Desarrollo Local', 'area' => 'Económica',    'activo' => false, 'orden' => 6, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Proyectos (Gestión) ───────────────────────────────────────────────
        // Desactivar auditoría para el seed para evitar FK sin usuario autenticado
        DB::table('proyectos')->truncate();

        $proyectos = [
            [
                'nombre_del_proyecto'    => 'Fortalecimiento de Competencias Digitales en Comunidades Rurales del Chocó',
                'objeto_contractual'     => 'Capacitar a 500 personas en habilidades digitales básicas mediante talleres presenciales y virtuales',
                'lineas_de_accion'       => 'Educación, Tecnología, Inclusión Digital',
                'cobertura'              => 'Departamental',
                'entidad_contratante'    => 'Ministerio de Tecnologías de la Información',
                'fecha_de_ejecucion'     => now()->subMonths(8),
                'plazo'                  => 12,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 380000000,
                'estado'                 => 'activo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Investigación en Bienestar Comunitario — Municipios del Atrato',
                'objeto_contractual'     => 'Diagnóstico participativo de necesidades de salud en 12 municipios ribereños',
                'lineas_de_accion'       => 'Salud, Investigación Comunitaria',
                'cobertura'              => 'Regional',
                'entidad_contratante'    => 'Gobernación del Chocó',
                'fecha_de_ejecucion'     => now()->subMonths(5),
                'plazo'                  => 8,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 215000000,
                'estado'                 => 'activo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Programa de Capacitación Docente en Pedagogía Crítica',
                'objeto_contractual'     => 'Formación continua de 200 docentes de básica y media en estrategias pedagógicas innovadoras',
                'lineas_de_accion'       => 'Educación, Desarrollo Profesional Docente',
                'cobertura'              => 'Municipal',
                'entidad_contratante'    => 'Secretaría de Educación Departamental',
                'fecha_de_ejecucion'     => now()->subMonths(10),
                'plazo'                  => 6,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 95000000,
                'estado'                 => 'cerrado',
                'certificado_cumplimiento' => 'certificados/proyecto3_cert.pdf',
                'certificado_fecha'      => now()->subMonths(4),
                'certificado_observaciones' => 'Proyecto ejecutado satisfactoriamente con cumplimiento del 100% de los indicadores.',
            ],
            [
                'nombre_del_proyecto'    => 'Consultoría para el Diseño del Plan de Ordenamiento Territorial de Quibdó',
                'objeto_contractual'     => 'Elaborar el diagnóstico territorial y propuesta de zonificación del municipio de Quibdó',
                'lineas_de_accion'       => 'Planificación Territorial, Gestión Ambiental',
                'cobertura'              => 'Municipal',
                'entidad_contratante'    => 'Alcaldía de Quibdó',
                'fecha_de_ejecucion'     => now()->subMonths(14),
                'plazo'                  => 18,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 540000000,
                'estado'                 => 'cerrado',
                'certificado_cumplimiento' => 'certificados/proyecto4_cert.pdf',
                'certificado_fecha'      => now()->subMonths(2),
                'certificado_observaciones' => 'Entrega recibida a conformidad por la Alcaldía.',
            ],
            [
                'nombre_del_proyecto'    => 'Extensión Universitaria: Clínica Jurídica Gratuita para Comunidades Afrodescendientes',
                'objeto_contractual'     => 'Brindar asesoría jurídica gratuita en derechos territoriales, laborales y familia a comunidades vulnerables',
                'lineas_de_accion'       => 'Extensión, Derechos Humanos, Justicia Social',
                'cobertura'              => 'Departamental',
                'entidad_contratante'    => 'UNICLARETIANA — Proyecto Propio',
                'fecha_de_ejecucion'     => now()->subMonths(3),
                'plazo'                  => 24,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 68000000,
                'estado'                 => 'activo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Estudio de Factibilidad: Energías Renovables en el Pacífico Colombiano',
                'objeto_contractual'     => 'Evaluar el potencial solar y eólico en zonas no interconectadas del Chocó para propuesta de minicentrales',
                'lineas_de_accion'       => 'Investigación, Sostenibilidad, Energía',
                'cobertura'              => 'Nacional',
                'entidad_contratante'    => 'IDEAM — Instituto de Hidrología',
                'fecha_de_ejecucion'     => now()->subMonths(1),
                'plazo'                  => 10,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 820000000,
                'estado'                 => 'activo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Sistematización de Experiencias de Paz en el Medio Atrato',
                'objeto_contractual'     => 'Documentar y sistematizar experiencias comunitarias de construcción de paz en 8 consejos comunitarios',
                'lineas_de_accion'       => 'Paz, Derechos Humanos, Investigación Social',
                'cobertura'              => 'Regional',
                'entidad_contratante'    => 'PNUD Colombia',
                'fecha_de_ejecucion'     => now()->subMonths(6),
                'plazo'                  => 5,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 145000000,
                'estado'                 => 'inactivo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Convenio Interinstitucional de Investigación en Biodiversidad del Chocó Biogeográfico',
                'objeto_contractual'     => 'Catalogar especies endémicas en tres cuencas hidrográficas con participación comunitaria',
                'lineas_de_accion'       => 'Biodiversidad, Investigación Aplicada, Medio Ambiente',
                'cobertura'              => 'Nacional',
                'entidad_contratante'    => 'Instituto Humboldt',
                'fecha_de_ejecucion'     => now()->subMonths(2),
                'plazo'                  => 36,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 1250000000,
                'estado'                 => 'activo',
                'certificado_cumplimiento' => null,
            ],
            [
                'nombre_del_proyecto'    => 'Programa de Emprendimiento Social para Mujeres Rurales',
                'objeto_contractual'     => 'Fortalecer capacidades empresariales de 150 mujeres rurales mediante mentorías y capital semilla',
                'lineas_de_accion'       => 'Emprendimiento, Género, Desarrollo Rural',
                'cobertura'              => 'Departamental',
                'entidad_contratante'    => 'Fondo Emprender — SENA',
                'fecha_de_ejecucion'     => now()->subMonths(4),
                'plazo'                  => 9,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 175000000,
                'estado'                 => 'cerrado',
                'certificado_cumplimiento' => 'certificados/proyecto9_cert.pdf',
                'certificado_fecha'      => now()->subMonths(1),
                'certificado_observaciones' => 'Metas superadas: 178 mujeres participaron.',
            ],
            [
                'nombre_del_proyecto'    => 'Diseño Curricular para Programa de Pregrado en Gestión Ambiental',
                'objeto_contractual'     => 'Elaborar el currículo y registro calificado para el nuevo programa académico de Gestión Ambiental',
                'lineas_de_accion'       => 'Educación Superior, Gestión Ambiental',
                'cobertura'              => 'Institucional',
                'entidad_contratante'    => 'UNICLARETIANA — Vicerrectoría Académica',
                'fecha_de_ejecucion'     => now()->subMonths(7),
                'plazo'                  => 14,
                'plazo_unidad'           => 'meses',
                'valor_total'            => 55000000,
                'estado'                 => 'inactivo',
                'certificado_cumplimiento' => null,
            ],
        ];

        foreach ($proyectos as $data) {
            DB::table('proyectos')->insert(array_merge($data, [
                'cargar_evidencias' => '[]',
                'created_at' => now()->subDays(rand(10, 300)),
                'updated_at' => now()->subDays(rand(1, 10)),
            ]));
        }

        // ── Banco de Proyectos ────────────────────────────────────────────────
        DB::table('banco_proyecto_historial')->delete();
        DB::table('banco_proyecto_anexos')->delete();
        DB::table('banco_proyectos')->delete();

        $bancoData = [
            [
                'titulo'               => 'Estrategias Pedagógicas Innovadoras para la Educación Intercultural en el Chocó',
                'linea_investigacion'  => 'Educación y Desarrollo Social',
                'area_facultad'        => 'Ciencias de la Educación',
                'tipo_proyecto'        => 'Investigación Formativa',
                'convocatoria'         => 'Convocatoria Interna 2025-1',
                'fecha_registro'       => now()->subMonths(6)->toDateString(),
                'estado'               => 'aprobado',
                'resumen_ejecutivo'    => 'El proyecto busca diseñar e implementar estrategias pedagógicas que integren los saberes ancestrales de las comunidades afrodescendientes e indígenas del Chocó con los contenidos del currículo oficial, fortaleciendo la identidad cultural y el desempeño académico de los estudiantes.',
                'problema_necesidad'   => 'Las comunidades étnicas del Chocó presentan altas tasas de deserción escolar y bajo rendimiento académico, en parte porque los currículos no reflejan sus realidades culturales y lingüísticas.',
                'objetivo_general'     => 'Diseñar e implementar un modelo pedagógico intercultural que mejore los indicadores de permanencia y aprendizaje en 5 instituciones educativas del departamento.',
                'justificacion'        => 'Colombia es un país pluriétnico y multicultural; sin embargo, el sistema educativo sigue siendo homogéneo. UNICLARETIANA, como institución comprometida con la región, tiene la responsabilidad de liderar este proceso de transformación.',
                'alcance'              => 'Cinco instituciones educativas en los municipios de Quibdó, Istmina y Tadó.',
                'poblacion_objetivo'   => '1.200 estudiantes de básica secundaria y 45 docentes.',
                'cobertura_geografica' => 'Departamento del Chocó, subregión del San Juan',
                'presupuesto_estimado' => 185000000,
                'fuente_financiacion'  => 'Ministerio de Educación Nacional',
                'cofinanciacion'       => 25000000,
                'duracion_meses'       => 18,
                'autores'              => [
                    ['nombre' => 'Dra. María Esperanza Palacios', 'rol' => 'Investigadora Principal'],
                    ['nombre' => 'Mg. Carlos Andrade Rentería',   'rol' => 'Co-investigador'],
                    ['nombre' => 'Est. Lorena Mosquera',          'rol' => 'Auxiliar de Investigación'],
                ],
                'tutor_director'       => 'Dr. Hernando Rentería Chaverra',
                'programa_departamento'=> 'Licenciatura en Ciencias Sociales',
                'entidad_aliada'       => 'Secretaría de Educación Departamental del Chocó',
                'evaluador_asignado'   => 'Mg. Patricia López Cuesta',
                'created_by'           => $super?->id,
            ],
            [
                'titulo'               => 'Caracterización de la Flora Medicinal Tradicional en Comunidades Indígenas Embera-Katío',
                'linea_investigacion'  => 'Salud y Bienestar Comunitario',
                'area_facultad'        => 'Ciencias de la Salud',
                'tipo_proyecto'        => 'Investigación Aplicada',
                'convocatoria'         => 'Colciencias — Convocatoria Territorios 2024',
                'fecha_registro'       => now()->subMonths(4)->toDateString(),
                'estado'               => 'en_ejecucion',
                'resumen_ejecutivo'    => 'Estudio etnobotánico que documenta, clasifica y analiza las propiedades farmacológicas de 80 plantas medicinales utilizadas por los mamos Embera-Katío, con miras a su integración en protocolos de atención primaria intercultural.',
                'problema_necesidad'   => 'El conocimiento medicinal ancestral de los pueblos indígenas del Chocó se está perdiendo aceleradamente sin haber sido documentado científicamente.',
                'objetivo_general'     => 'Catalogar y caracterizar desde criterios etnobotánicos y fitoquímicos las 80 principales plantas medicinales usadas por los mamos Embera-Katío.',
                'justificacion'        => 'La biodiversidad del Chocó biogeográfico alberga miles de especies vegetales con potencial medicinal. Documentar este patrimonio biocultural es urgente ante la deforestación y el desplazamiento forzado.',
                'alcance'              => 'Resguardos indígenas de Bagadó, Lloró y Río Murindó.',
                'poblacion_objetivo'   => '12 mamos y 200 familias indígenas.',
                'cobertura_geografica' => 'Alto y Bajo Atrato',
                'presupuesto_estimado' => 320000000,
                'fuente_financiacion'  => 'Minciencias',
                'cofinanciacion'       => 80000000,
                'duracion_meses'       => 24,
                'autores'              => [
                    ['nombre' => 'Dr. Jesús Antonio Córdoba', 'rol' => 'Investigador Principal'],
                    ['nombre' => 'Dra. Yira Mena Blanquicett', 'rol' => 'Co-investigadora'],
                ],
                'tutor_director'       => 'Dr. Rafael Enríquez Murillo',
                'programa_departamento'=> 'Medicina — Ciencias de la Salud',
                'entidad_aliada'       => 'Cabildo Mayor Embera Katío del Alto San Jorge',
                'evaluador_asignado'   => 'Dr. Freddy Mosquera Ibargüen',
                'created_by'           => $admin?->id,
            ],
            [
                'titulo'               => 'Impacto Socioeconómico de la Minería Ilegal en los Ríos San Juan y Baudó',
                'linea_investigacion'  => 'Gestión Ambiental y Sostenibilidad',
                'area_facultad'        => 'Ciencias Sociales',
                'tipo_proyecto'        => 'Investigación Aplicada',
                'convocatoria'         => 'Convocatoria Interna 2025-2',
                'fecha_registro'       => now()->subMonths(2)->toDateString(),
                'estado'               => 'en_evaluacion',
                'resumen_ejecutivo'    => 'Análisis mixto del impacto de la minería ilegal sobre la economía local, los ecosistemas fluviales y la seguridad alimentaria de las comunidades ribereñas del Chocó.',
                'problema_necesidad'   => 'La minería ilegal genera contaminación por mercurio, desplazamiento y conflictividad social, pero no existen estudios integrales que cuantifiquen su impacto en el Chocó.',
                'objetivo_general'     => 'Cuantificar el impacto socioeconómico y ambiental de la minería ilegal en las cuencas del San Juan y el Baudó.',
                'justificacion'        => 'Sin datos rigurosos, las políticas públicas de control minero son ineficaces. La universidad tiene capacidad y responsabilidad de generar esta evidencia.',
                'alcance'              => 'Cuencas del Río San Juan (16 municipios) y Río Baudó (6 municipios).',
                'poblacion_objetivo'   => '85 comunidades ribereñas afro e indígenas.',
                'cobertura_geografica' => 'Departamento del Chocó',
                'presupuesto_estimado' => 240000000,
                'fuente_financiacion'  => 'IDEAM — DNP',
                'cofinanciacion'       => 60000000,
                'duracion_meses'       => 20,
                'autores'              => [
                    ['nombre' => 'Mg. Álvaro Grueso Palacios',    'rol' => 'Investigador Principal'],
                    ['nombre' => 'Mg. Sandra Milena Hinestroza',  'rol' => 'Co-investigadora'],
                    ['nombre' => 'Est. Kevin Murillo Torres',     'rol' => 'Auxiliar'],
                ],
                'tutor_director'       => 'Mg. Rosario Asprilla Mosquera',
                'programa_departamento'=> 'Trabajo Social',
                'entidad_aliada'       => null,
                'evaluador_asignado'   => null,
                'created_by'           => $super?->id,
            ],
            [
                'titulo'               => 'Plataforma Web para la Gestión de Proyectos de Extensión Universitaria',
                'linea_investigacion'  => 'Tecnología e Innovación',
                'area_facultad'        => 'Ingeniería',
                'tipo_proyecto'        => 'Investigación Formativa',
                'convocatoria'         => 'Semilleros de Investigación 2025',
                'fecha_registro'       => now()->subMonths(1)->toDateString(),
                'estado'               => 'borrador',
                'resumen_ejecutivo'    => 'Desarrollo de una plataforma web institucional para la gestión, seguimiento y reporte de proyectos de extensión universitaria, con módulos de indicadores, alertas de vencimiento y exportación de reportes.',
                'problema_necesidad'   => 'La universidad gestiona sus proyectos de extensión en hojas de cálculo y correos electrónicos, lo que dificulta el seguimiento y la rendición de cuentas.',
                'objetivo_general'     => 'Desarrollar e implementar un sistema de información web para la gestión integral de proyectos de extensión universitaria.',
                'justificacion'        => 'La transformación digital de los procesos administrativos es una prioridad institucional que mejora la eficiencia y la transparencia.',
                'alcance'              => 'Vicerrectoría de Extensión y todos los programas académicos de UNICLARETIANA.',
                'poblacion_objetivo'   => '15 coordinadores de proyectos y 3 directivos.',
                'cobertura_geografica' => 'Institucional — Campus Quibdó',
                'presupuesto_estimado' => 45000000,
                'fuente_financiacion'  => 'Recursos propios UNICLARETIANA',
                'cofinanciacion'       => 0,
                'duracion_meses'       => 12,
                'autores'              => [
                    ['nombre' => 'Est. Diana Lorena Palacio',  'rol' => 'Investigadora'],
                    ['nombre' => 'Est. Jhon Fredy Ibargüen',   'rol' => 'Investigador'],
                ],
                'tutor_director'       => 'Ing. Nelson Mosquera Conde',
                'programa_departamento'=> 'Ingeniería de Sistemas',
                'entidad_aliada'       => null,
                'evaluador_asignado'   => null,
                'created_by'           => $user?->id,
            ],
            [
                'titulo'               => 'Acceso a la Justicia y Rutas de Atención para Víctimas del Conflicto en el Chocó',
                'linea_investigacion'  => 'Derechos Humanos y Justicia',
                'area_facultad'        => 'Ciencias Jurídicas',
                'tipo_proyecto'        => 'Extensión Universitaria',
                'convocatoria'         => 'OIM Colombia — Convocatoria Paz 2024',
                'fecha_registro'       => now()->subMonths(9)->toDateString(),
                'estado'               => 'cerrado',
                'resumen_ejecutivo'    => 'Mapeo y fortalecimiento de las rutas de atención jurídica a víctimas del desplazamiento forzado, con énfasis en mujeres y comunidades étnicas del Chocó.',
                'problema_necesidad'   => 'Las víctimas del conflicto armado desconocen sus derechos y las rutas de atención disponibles, lo que impide su acceso efectivo a la justicia y la reparación.',
                'objetivo_general'     => 'Fortalecer las capacidades de 300 víctimas y 20 operadores judiciales para el ejercicio efectivo de derechos en el marco de la Ley 1448.',
                'justificacion'        => 'El Chocó es uno de los departamentos con mayor expulsión de población víctima de Colombia. La intervención jurídica directa es necesaria e improrrogable.',
                'alcance'              => 'Municipios de Quibdó, Riosucio, Carmen del Darién y Bagadó.',
                'poblacion_objetivo'   => '300 víctimas del conflicto armado',
                'cobertura_geografica' => 'Norte y centro del departamento del Chocó',
                'presupuesto_estimado' => 160000000,
                'fuente_financiacion'  => 'Organización Internacional para las Migraciones (OIM)',
                'cofinanciacion'       => 20000000,
                'duracion_meses'       => 12,
                'autores'              => [
                    ['nombre' => 'Abg. Liliana Mena Copete',   'rol' => 'Investigadora Principal'],
                    ['nombre' => 'Mg. Ernesto García Restrepo','rol' => 'Co-investigador'],
                ],
                'tutor_director'       => 'Dr. Luis Alberto Rentería',
                'programa_departamento'=> 'Derecho',
                'entidad_aliada'       => 'OIM Colombia — Unidad para las Víctimas',
                'evaluador_asignado'   => 'Dra. Amparito Cuesta Valencia',
                'certificado_cumplimiento' => 'documentos/cert_banco5.pdf',
                'certificado_fecha'    => now()->subMonths(1)->toDateString(),
                'certificado_observaciones' => 'Proyecto finalizado con todas las metas alcanzadas. Certificado emitido por OIM.',
                'created_by'           => $admin?->id,
            ],
            [
                'titulo'               => 'Modelo de Economía Solidaria para Asociaciones Campesinas del Alto San Juan',
                'linea_investigacion'  => 'Educación y Desarrollo Social',
                'area_facultad'        => 'Ciencias Económicas',
                'tipo_proyecto'        => 'Interinstitucional',
                'convocatoria'         => 'Convocatoria DPS — Redes Productivas 2025',
                'fecha_registro'       => now()->subWeeks(3)->toDateString(),
                'estado'               => 'borrador',
                'resumen_ejecutivo'    => 'Diseño e implementación de un modelo de economía solidaria para 6 asociaciones campesinas productoras de plátano y cacao en el municipio de Condoto y alrededores.',
                'problema_necesidad'   => 'Las asociaciones campesinas de la región carecen de estructuras organizativas sólidas y modelos de negocio que les permitan acceder a mercados formales.',
                'objetivo_general'     => 'Implementar un modelo de economía solidaria que mejore los ingresos y la capacidad organizativa de 6 asociaciones campesinas.',
                'justificacion'        => 'La economía campesina es la base de la seguridad alimentaria regional. Fortalecerla es apostar por la soberanía y el desarrollo endógeno del territorio.',
                'alcance'              => 'Municipios de Condoto, Istmina y Sipí.',
                'poblacion_objetivo'   => '180 familias campesinas',
                'cobertura_geografica' => 'Subregión del San Juan',
                'presupuesto_estimado' => 95000000,
                'fuente_financiacion'  => 'DPS — Departamento para la Prosperidad Social',
                'cofinanciacion'       => 15000000,
                'duracion_meses'       => 16,
                'autores'              => [
                    ['nombre' => 'Mg. Yolanda Quejada Torres', 'rol' => 'Investigadora Principal'],
                ],
                'tutor_director'       => 'Dr. Francisco Palacios Asprilla',
                'programa_departamento'=> 'Administración de Empresas',
                'entidad_aliada'       => 'DPS Colombia',
                'evaluador_asignado'   => null,
                'created_by'           => $user?->id,
            ],
        ];

        foreach ($bancoData as $bp) {
            $certificado = isset($bp['certificado_cumplimiento']) ? [
                'certificado_cumplimiento'  => $bp['certificado_cumplimiento'],
                'certificado_fecha'         => $bp['certificado_fecha'] ?? null,
                'certificado_observaciones' => $bp['certificado_observaciones'] ?? null,
            ] : [];
            unset($bp['certificado_cumplimiento'], $bp['certificado_fecha'], $bp['certificado_observaciones']);

            $bp['autores'] = json_encode($bp['autores']);
            $bp['created_at'] = now()->subDays(rand(5, 200));
            $bp['updated_at'] = now()->subDays(rand(1, 5));

            $id = DB::table('banco_proyectos')->insertGetId(array_merge(
                ['codigo' => BancoProyecto::generarCodigo()],
                $bp,
                $certificado,
            ));

            // Historial de estados
            $historialEntries = match ($bp['estado'] ?? 'borrador') {
                'en_evaluacion' => [
                    ['accion' => 'Creación', 'descripcion' => 'Registro creado en el banco de proyectos', 'created_at' => now()->subMonths(2)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Estado actualizado a En Evaluación', 'campo_modificado' => 'estado', 'valor_anterior' => 'borrador', 'valor_nuevo' => 'en_evaluacion', 'created_at' => now()->subWeeks(3)],
                ],
                'aprobado' => [
                    ['accion' => 'Creación', 'descripcion' => 'Registro creado en el banco de proyectos', 'created_at' => now()->subMonths(6)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Estado actualizado a En Evaluación', 'campo_modificado' => 'estado', 'valor_anterior' => 'borrador', 'valor_nuevo' => 'en_evaluacion', 'created_at' => now()->subMonths(5)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Estado actualizado a Aprobado. Cumple con todos los criterios de evaluación.', 'campo_modificado' => 'estado', 'valor_anterior' => 'en_evaluacion', 'valor_nuevo' => 'aprobado', 'created_at' => now()->subMonths(3)],
                ],
                'en_ejecucion' => [
                    ['accion' => 'Creación', 'descripcion' => 'Registro creado en el banco de proyectos', 'created_at' => now()->subMonths(4)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Enviado a evaluación', 'campo_modificado' => 'estado', 'valor_anterior' => 'borrador', 'valor_nuevo' => 'en_evaluacion', 'created_at' => now()->subMonths(3)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Proyecto aprobado por comité académico', 'campo_modificado' => 'estado', 'valor_anterior' => 'en_evaluacion', 'valor_nuevo' => 'aprobado', 'created_at' => now()->subMonths(2)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Inicio formal de actividades — acta de inicio firmada', 'campo_modificado' => 'estado', 'valor_anterior' => 'aprobado', 'valor_nuevo' => 'en_ejecucion', 'created_at' => now()->subMonths(1)],
                ],
                'cerrado' => [
                    ['accion' => 'Creación', 'descripcion' => 'Registro creado en el banco de proyectos', 'created_at' => now()->subMonths(9)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Enviado a evaluación', 'campo_modificado' => 'estado', 'valor_anterior' => 'borrador', 'valor_nuevo' => 'en_evaluacion', 'created_at' => now()->subMonths(8)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Aprobado', 'campo_modificado' => 'estado', 'valor_anterior' => 'en_evaluacion', 'valor_nuevo' => 'aprobado', 'created_at' => now()->subMonths(7)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'En ejecución', 'campo_modificado' => 'estado', 'valor_anterior' => 'aprobado', 'valor_nuevo' => 'en_ejecucion', 'created_at' => now()->subMonths(6)],
                    ['accion' => 'Cambio de Estado', 'descripcion' => 'Proyecto concluido satisfactoriamente', 'campo_modificado' => 'estado', 'valor_anterior' => 'en_ejecucion', 'valor_nuevo' => 'cerrado', 'created_at' => now()->subMonths(1)],
                ],
                default => [
                    ['accion' => 'Creación', 'descripcion' => 'Registro creado en el banco de proyectos', 'created_at' => now()->subDays(rand(5, 30))],
                ],
            };

            foreach ($historialEntries as $h) {
                DB::table('banco_proyecto_historial')->insert([
                    'banco_proyecto_id' => $id,
                    'accion'            => $h['accion'],
                    'campo_modificado'  => $h['campo_modificado'] ?? null,
                    'valor_anterior'    => $h['valor_anterior'] ?? null,
                    'valor_nuevo'       => $h['valor_nuevo'] ?? null,
                    'descripcion'       => $h['descripcion'],
                    'user_id'           => $admin?->id,
                    'user_name'         => $admin?->name ?? 'Sistema',
                    'created_at'        => $h['created_at'],
                ]);
            }
        }

        // ── Solicitudes de Acceso ─────────────────────────────────────────────
        DB::table('access_requests')->truncate();
        AccessRequest::insert([
            ['name' => 'Jorge Luis Asprilla',  'email' => 'jasprilla@uniclaretiana.edu.co',  'phone' => '3142567890', 'reason' => 'Coordinador de proyectos de extensión. Necesito acceso para registrar y hacer seguimiento a los proyectos del programa de Derecho.', 'status' => 'approved', 'admin_comment' => 'Acceso aprobado. Usuario creado.', 'reviewed_at' => now()->subDays(10), 'created_at' => now()->subDays(15), 'updated_at' => now()->subDays(10)],
            ['name' => 'Claudia Patricia Mena','email' => 'cmena@uniclaretiana.edu.co',       'phone' => '3108834521', 'reason' => 'Investigadora del grupo GIEMSUR. Requiero acceso para gestionar el banco de proyectos del grupo de investigación.', 'status' => 'approved', 'admin_comment' => 'Aprobado. Rol supervisor asignado.', 'reviewed_at' => now()->subDays(5), 'created_at' => now()->subDays(8), 'updated_at' => now()->subDays(5)],
            ['name' => 'Rubén Darío Ibáñez',   'email' => 'rdibañez@gmail.com',              'phone' => '3005671234', 'reason' => 'Docente externo vinculado al proyecto de biodiversidad. Necesito consultar el avance de los informes.',  'status' => 'rejected',  'admin_comment' => 'No cumple requisitos institucionales. Solo se otorga acceso a personal vinculado directamente a UNICLARETIANA.', 'reviewed_at' => now()->subDays(2), 'created_at' => now()->subDays(6), 'updated_at' => now()->subDays(2)],
            ['name' => 'Adriana Lucía Palacios','email' => 'alpalacios@uniclaretiana.edu.co', 'phone' => '3167894532', 'reason' => 'Directora del programa de Trabajo Social. Requiero acceso para revisar los proyectos activos y banco de proyectos del programa.', 'status' => 'pending', 'admin_comment' => null, 'reviewed_at' => null, 'created_at' => now()->subDays(1), 'updated_at' => now()->subDays(1)],
            ['name' => 'Felipe Córdoba Serna',  'email' => 'fcordoba@uniclaretiana.edu.co',   'phone' => '3209988776', 'reason' => 'Coordinador de práctica profesional. Solicito acceso para vincular estudiantes a proyectos de extensión y hacer seguimiento.', 'status' => 'pending', 'admin_comment' => null, 'reviewed_at' => null, 'created_at' => now()->subHours(4), 'updated_at' => now()->subHours(4)],
        ]);

        // ── Audit Log ─────────────────────────────────────────────────────────
        DB::table('audit_log')->truncate();

        $proyectosIds = DB::table('proyectos')->pluck('id')->toArray();
        $bancoIds     = DB::table('banco_proyectos')->pluck('id')->toArray();
        $userIds      = DB::table('users')->pluck('id')->toArray();

        $auditEntries = [];

        foreach ($proyectosIds as $pid) {
            $auditEntries[] = [
                'table_name'  => 'proyectos',
                'operation'   => 'INSERT',
                'record_id'   => $pid,
                'old_values'  => null,
                'new_values'  => json_encode(['estado' => 'activo', 'nombre_del_proyecto' => 'Proyecto registrado']),
                'changed_by'  => $admin?->id,
                'user_name'   => $admin?->name ?? 'Admin',
                'ip_address'  => '192.168.1.' . rand(1, 50),
                'created_at'  => now()->subDays(rand(5, 120)),
            ];
        }

        foreach ($bancoIds as $bid) {
            $auditEntries[] = [
                'table_name'  => 'banco_proyectos',
                'operation'   => 'INSERT',
                'record_id'   => $bid,
                'old_values'  => null,
                'new_values'  => json_encode(['estado' => 'borrador']),
                'changed_by'  => $super?->id,
                'user_name'   => $super?->name ?? 'Supervisor',
                'ip_address'  => '192.168.1.' . rand(1, 50),
                'created_at'  => now()->subDays(rand(2, 60)),
            ];
            $auditEntries[] = [
                'table_name'  => 'banco_proyectos',
                'operation'   => 'UPDATE',
                'record_id'   => $bid,
                'old_values'  => json_encode(['estado' => 'borrador']),
                'new_values'  => json_encode(['estado' => 'en_evaluacion']),
                'changed_by'  => $admin?->id,
                'user_name'   => $admin?->name ?? 'Admin',
                'ip_address'  => '192.168.1.' . rand(1, 50),
                'created_at'  => now()->subDays(rand(1, 30)),
            ];
        }

        foreach ($userIds as $uid) {
            $auditEntries[] = [
                'table_name'  => 'users',
                'operation'   => 'INSERT',
                'record_id'   => $uid,
                'old_values'  => null,
                'new_values'  => json_encode(['email' => 'usuario@uniclaretiana.edu.co']),
                'changed_by'  => $admin?->id,
                'user_name'   => $admin?->name ?? 'Admin',
                'ip_address'  => '192.168.1.1',
                'created_at'  => now()->subDays(rand(30, 200)),
            ];
        }

        foreach ($auditEntries as $entry) {
            DB::table('audit_log')->insert($entry);
        }
    }
}
