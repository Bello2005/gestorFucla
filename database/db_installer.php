<?php

/**
 * Gestor de Base de Datos - Uniclaretiana Proyectos
 * Script PHP para crear y gestionar la base de datos
 */

class DatabaseManager
{
    private $host = '127.0.0.1';
    private $username = 'root';
    private $password = '';
    private $database = 'uniclaretiana_proyectos';
    private $connection;

    public function __construct($host = 'localhost', $username = 'root', $password = '', $database = 'uniclaretiana_proyectos')
    {
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
    }

    /**
     * Conectar a MySQL (sin seleccionar base de datos específica)
     */
    public function connectToServer()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};unix_socket=/opt/lampp/var/mysql/mysql.sock",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            
            echo "✅ Conexión exitosa al servidor MySQL\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error conectando al servidor: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Conectar a la base de datos específica
     */
    public function connectToDatabase()
    {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->database};unix_socket=/opt/lampp/var/mysql/mysql.sock",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
            
            echo "✅ Conexión exitosa a la base de datos {$this->database}\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error conectando a la base de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Crear la base de datos
     */
    public function createDatabase()
    {
        try {
            $sql = "CREATE DATABASE IF NOT EXISTS {$this->database} 
                   CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
            
            $this->connection->exec($sql);
            echo "✅ Base de datos '{$this->database}' creada correctamente\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error creando la base de datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Crear tabla de proyectos
     */
    public function createProjectsTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS proyectos (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre_del_proyecto VARCHAR(255) NOT NULL,
            objeto_contractual VARCHAR(255) NULL,
            lineas_de_accion TEXT NULL,
            cobertura VARCHAR(255) NULL,
            entidad_contratante VARCHAR(255) NULL,
            fecha_de_ejecucion DATE NULL,
            plazo DECIMAL(8,2) NULL COMMENT 'Plazo en meses',
            valor_total DECIMAL(15,2) NULL COMMENT 'Valor total del proyecto',
            cargar_archivo_proyecto VARCHAR(255) NULL COMMENT 'Ruta del archivo principal',
            cargar_contrato_o_convenio VARCHAR(255) NULL COMMENT 'Ruta del contrato',
            cargar_evidencias JSON NULL COMMENT 'Array de rutas de evidencias',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_nombre_proyecto (nombre_del_proyecto),
            INDEX idx_entidad_contratante (entidad_contratante),
            INDEX idx_fecha_ejecucion (fecha_de_ejecucion),
            INDEX idx_valor_total (valor_total),
            INDEX idx_created_at (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->connection->exec($sql);
            echo "✅ Tabla 'proyectos' creada correctamente\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error creando tabla proyectos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Crear tabla de usuarios
     */
    public function createUsersTable()
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS usuarios (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(255) NOT NULL,
            email VARCHAR(255) UNIQUE NOT NULL,
            email_verified_at TIMESTAMP NULL,
            password VARCHAR(255) NOT NULL,
            rol ENUM('admin', 'editor', 'viewer') DEFAULT 'editor',
            activo BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_email (email),
            INDEX idx_rol (rol)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->connection->exec($sql);
            echo "✅ Tabla 'usuarios' creada correctamente\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error creando tabla usuarios: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Insertar datos de prueba
     */
    public function insertSampleData()
    {
        try {
            // Insertar usuarios de prueba
            $stmt = $this->connection->prepare("
                INSERT INTO usuarios (nombre, email, password, rol) VALUES
                ('Administrador Sistema', 'admin@uniclaretiana.edu.co', ?, 'admin'),
                ('Editor Proyectos', 'editor@uniclaretiana.edu.co', ?, 'editor'),
                ('Visualizador', 'viewer@uniclaretiana.edu.co', ?, 'viewer')
            ");
            
            $hashedPassword = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->execute([$hashedPassword, $hashedPassword, $hashedPassword]);
            echo "✅ Usuarios de prueba insertados\n";

            // Insertar proyectos de prueba
            $proyectos = [
                [
                    'Fortalecimiento de Capacidades Educativas en Comunidades Rurales',
                    'Desarrollo de programas educativos para mejorar la calidad de la educación en zonas rurales del Chocó',
                    "Línea 1: Formación docente\nLínea 2: Dotación de material educativo\nLínea 3: Construcción de infraestructura educativa\nLínea 4: Implementación de tecnologías educativas",
                    'Municipios de Quibdó, Istmina, Condoto',
                    'Ministerio de Educación Nacional',
                    '2024-03-15',
                    18.00,
                    850000000.00
                ],
                [
                    'Programa de Emprendimiento Juvenil Sostenible',
                    'Capacitación y acompañamiento a jóvenes emprendedores en el desarrollo de proyectos productivos sostenibles',
                    "Línea 1: Identificación de oportunidades de negocio\nLínea 2: Formación en emprendimiento\nLínea 3: Acompañamiento técnico\nLínea 4: Acceso a financiación",
                    'Departamento del Chocó',
                    'SENA - Servicio Nacional de Aprendizaje',
                    '2024-01-20',
                    12.00,
                    320000000.00
                ],
                [
                    'Investigación en Biodiversidad del Pacífico Chocoano',
                    'Estudio y catalogación de especies endémicas de la región del Chocó para la conservación ambiental',
                    "Línea 1: Investigación de campo\nLínea 2: Análisis de laboratorio\nLínea 3: Catalogación de especies\nLínea 4: Propuestas de conservación",
                    'Región del Pacífico - Chocó',
                    'Instituto Alexander von Humboldt',
                    '2024-06-01',
                    24.00,
                    1200000000.00
                ],
                [
                    'Fortalecimiento de la Cadena Productiva del Cacao',
                    'Mejoramiento de la producción, transformación y comercialización del cacao en comunidades afrodescendientes',
                    "Línea 1: Asistencia técnica agrícola\nLínea 2: Capacitación en transformación\nLínea 3: Desarrollo de marca\nLínea 4: Canales de comercialización",
                    'Municipios cacaoteros del Chocó',
                    'Ministerio de Agricultura y Desarrollo Rural',
                    '2023-11-10',
                    15.00,
                    680000000.00
                ],
                [
                    'Centro de Innovación Tecnológica para el Desarrollo Regional',
                    'Creación de un centro de innovación que promueva el desarrollo tecnológico en la región del Chocó',
                    "Línea 1: Infraestructura tecnológica\nLínea 2: Formación en TIC\nLínea 3: Incubación de empresas tecnológicas\nLínea 4: Transferencia de tecnología",
                    'Quibdó y área metropolitana',
                    'MinTIC - Ministerio de Tecnologías de la Información',
                    '2024-08-15',
                    36.00,
                    2500000000.00
                ]
            ];

            $stmt = $this->connection->prepare("
                INSERT INTO proyectos (
                    nombre_del_proyecto, objeto_contractual, lineas_de_accion, 
                    cobertura, entidad_contratante, fecha_de_ejecucion, 
                    plazo, valor_total
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($proyectos as $proyecto) {
                $stmt->execute($proyecto);
            }

            echo "✅ Proyectos de prueba insertados\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error insertando datos de prueba: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Obtener todos los proyectos
     */
    public function getAllProjects()
    {
        try {
            $stmt = $this->connection->query("
                SELECT 
                    id,
                    nombre_del_proyecto,
                    entidad_contratante,
                    fecha_de_ejecucion,
                    valor_total,
                    created_at
                FROM proyectos 
                ORDER BY created_at DESC
            ");
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "❌ Error obteniendo proyectos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Obtener estadísticas de la base de datos
     */
    public function getStatistics()
    {
        try {
            $stats = [];
            
            // Total de proyectos
            $stmt = $this->connection->query("SELECT COUNT(*) as total FROM proyectos");
            $stats['total_proyectos'] = $stmt->fetch()['total'];
            
            // Total de inversión
            $stmt = $this->connection->query("SELECT SUM(valor_total) as total FROM proyectos WHERE valor_total IS NOT NULL");
            $stats['inversion_total'] = $stmt->fetch()['total'] ?? 0;
            
            // Proyectos por entidad
            $stmt = $this->connection->query("
                SELECT entidad_contratante, COUNT(*) as cantidad 
                FROM proyectos 
                WHERE entidad_contratante IS NOT NULL 
                GROUP BY entidad_contratante 
                ORDER BY cantidad DESC
            ");
            $stats['por_entidad'] = $stmt->fetchAll();
            
            // Proyectos por mes
            $stmt = $this->connection->query("
                SELECT 
                    YEAR(fecha_de_ejecucion) as año,
                    MONTH(fecha_de_ejecucion) as mes,
                    COUNT(*) as cantidad
                FROM proyectos 
                WHERE fecha_de_ejecucion IS NOT NULL
                GROUP BY YEAR(fecha_de_ejecucion), MONTH(fecha_de_ejecucion)
                ORDER BY año DESC, mes DESC
                LIMIT 12
            ");
            $stats['por_mes'] = $stmt->fetchAll();
            
            return $stats;
        } catch (PDOException $e) {
            echo "❌ Error obteniendo estadísticas: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Insertar un nuevo proyecto
     */
    public function insertProject($data)
    {
        try {
            $stmt = $this->connection->prepare("
                INSERT INTO proyectos (
                    nombre_del_proyecto, objeto_contractual, lineas_de_accion,
                    cobertura, entidad_contratante, fecha_de_ejecucion,
                    plazo, valor_total, cargar_archivo_proyecto,
                    cargar_contrato_o_convenio, cargar_evidencias
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['nombre_del_proyecto'],
                $data['objeto_contractual'] ?? null,
                $data['lineas_de_accion'] ?? null,
                $data['cobertura'] ?? null,
                $data['entidad_contratante'] ?? null,
                $data['fecha_de_ejecucion'] ?? null,
                $data['plazo'] ?? null,
                $data['valor_total'] ?? null,
                $data['cargar_archivo_proyecto'] ?? null,
                $data['cargar_contrato_o_convenio'] ?? null,
                $data['cargar_evidencias'] ? json_encode($data['cargar_evidencias']) : null
            ]);
            
            if ($result) {
                echo "✅ Proyecto insertado correctamente\n";
                return $this->connection->lastInsertId();
            }
            
            return false;
        } catch (PDOException $e) {
            echo "❌ Error insertando proyecto: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Actualizar un proyecto
     */
    public function updateProject($id, $data)
    {
        try {
            $stmt = $this->connection->prepare("
                UPDATE proyectos SET
                    nombre_del_proyecto = ?,
                    objeto_contractual = ?,
                    lineas_de_accion = ?,
                    cobertura = ?,
                    entidad_contratante = ?,
                    fecha_de_ejecucion = ?,
                    plazo = ?,
                    valor_total = ?,
                    cargar_archivo_proyecto = ?,
                    cargar_contrato_o_convenio = ?,
                    cargar_evidencias = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");
            
            $result = $stmt->execute([
                $data['nombre_del_proyecto'],
                $data['objeto_contractual'] ?? null,
                $data['lineas_de_accion'] ?? null,
                $data['cobertura'] ?? null,
                $data['entidad_contratante'] ?? null,
                $data['fecha_de_ejecucion'] ?? null,
                $data['plazo'] ?? null,
                $data['valor_total'] ?? null,
                $data['cargar_archivo_proyecto'] ?? null,
                $data['cargar_contrato_o_convenio'] ?? null,
                $data['cargar_evidencias'] ? json_encode($data['cargar_evidencias']) : null,
                $id
            ]);
            
            if ($result) {
                echo "✅ Proyecto actualizado correctamente\n";
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            echo "❌ Error actualizando proyecto: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Eliminar un proyecto
     */
    public function deleteProject($id)
    {
        try {
            $stmt = $this->connection->prepare("DELETE FROM proyectos WHERE id = ?");
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo "✅ Proyecto eliminado correctamente\n";
                return true;
            } else {
                echo "⚠️ No se encontró el proyecto con ID: $id\n";
                return false;
            }
        } catch (PDOException $e) {
            echo "❌ Error eliminando proyecto: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Buscar proyectos
     */
    public function searchProjects($search)
    {
        try {
            $stmt = $this->connection->prepare("
                SELECT 
                    id, nombre_del_proyecto, entidad_contratante,
                    fecha_de_ejecucion, valor_total
                FROM proyectos 
                WHERE nombre_del_proyecto LIKE ? 
                   OR objeto_contractual LIKE ?
                   OR entidad_contratante LIKE ?
                ORDER BY created_at DESC
            ");
            
            $searchTerm = "%$search%";
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo "❌ Error buscando proyectos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Exportar datos a CSV
     */
    public function exportToCSV($filename = 'proyectos.csv')
    {
        try {
            $stmt = $this->connection->query("
                SELECT 
                    nombre_del_proyecto,
                    objeto_contractual,
                    lineas_de_accion,
                    cobertura,
                    entidad_contratante,
                    fecha_de_ejecucion,
                    plazo,
                    valor_total,
                    created_at
                FROM proyectos 
                ORDER BY created_at DESC
            ");
            
            $file = fopen($filename, 'w');
            
            // Escribir encabezados
            fputcsv($file, [
                'Nombre del Proyecto',
                'Objeto Contractual',
                'Líneas de Acción',
                'Cobertura',
                'Entidad Contratante',
                'Fecha de Ejecución',
                'Plazo (meses)',
                'Valor Total',
                'Fecha de Creación'
            ]);
            
            // Escribir datos
            while ($row = $stmt->fetch()) {
                fputcsv($file, $row);
            }
            
            fclose($file);
            echo "✅ Datos exportados a: $filename\n";
            return true;
        } catch (PDOException $e) {
            echo "❌ Error exportando datos: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Verificar integridad de la base de datos
     */
    public function checkDatabaseIntegrity()
    {
        try {
            echo "🔍 Verificando integridad de la base de datos...\n";
            
            // Verificar tablas existentes
            $stmt = $this->connection->query("SHOW TABLES");
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $expectedTables = ['proyectos', 'usuarios'];
            $missingTables = array_diff($expectedTables, $tables);
            
            if (empty($missingTables)) {
                echo "✅ Todas las tablas requeridas existen\n";
            } else {
                echo "❌ Faltan las siguientes tablas: " . implode(', ', $missingTables) . "\n";
                return false;
            }
            
            // Verificar estructura de tabla proyectos
            $stmt = $this->connection->query("DESCRIBE proyectos");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            
            $requiredColumns = [
                'id', 'nombre_del_proyecto', 'objeto_contractual', 
                'lineas_de_accion', 'cobertura', 'entidad_contratante',
                'fecha_de_ejecucion', 'plazo', 'valor_total'
            ];
            
            $missingColumns = array_diff($requiredColumns, $columns);
            
            if (empty($missingColumns)) {
                echo "✅ Estructura de tabla proyectos es correcta\n";
            } else {
                echo "❌ Faltan las siguientes columnas en proyectos: " . implode(', ', $missingColumns) . "\n";
            }
            
            return true;
        } catch (PDOException $e) {
            echo "❌ Error verificando integridad: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Cerrar conexión
     */
    public function close()
    {
        $this->connection = null;
        echo "🔐 Conexión cerrada\n";
    }

    /**
     * Obtener la conexión PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }
}

// =====================================================
// SCRIPT DE INSTALACIÓN AUTOMÁTICA
// =====================================================

function installDatabase($config = [])
{
    echo "🚀 Iniciando instalación de la base de datos...\n\n";
    
    // Configuración por defecto
    $defaultConfig = [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'uniclaretiana_proyectos'
    ];
    
    $config = array_merge($defaultConfig, $config);
    
    $db = new DatabaseManager(
        $config['host'],
        $config['username'],
        $config['password'],
        $config['database']
    );
    
    // Paso 1: Conectar al servidor
    if (!$db->connectToServer()) {
        return false;
    }
    
    // Paso 2: Crear la base de datos
    if (!$db->createDatabase()) {
        return false;
    }
    
    // Paso 3: Conectar a la base de datos específica
    if (!$db->connectToDatabase()) {
        return false;
    }
    
    // Paso 4: Crear tablas
    if (!$db->createProjectsTable() || !$db->createUsersTable()) {
        return false;
    }
    
    // Paso 5: Insertar datos de prueba
    if (!$db->insertSampleData()) {
        return false;
    }
    
    // Paso 6: Verificar integridad
    if (!$db->checkDatabaseIntegrity()) {
        return false;
    }
    
    // Paso 7: Mostrar estadísticas
    echo "\n📊 Estadísticas iniciales:\n";
    $stats = $db->getStatistics();
    if ($stats) {
        echo "   • Total de proyectos: {$stats['total_proyectos']}\n";
        echo "   • Inversión total: $" . number_format($stats['inversion_total'], 2) . "\n";
        echo "   • Entidades contratantes: " . count($stats['por_entidad']) . "\n";
    }
    
    $db->close();
    
    echo "\n🎉 ¡Base de datos instalada exitosamente!\n";
    echo "📝 Credenciales de acceso:\n";
    echo "   • Admin: admin@uniclaretiana.edu.co / 123456\n";
    echo "   • Editor: editor@uniclaretiana.edu.co / 123456\n";
    echo "   • Viewer: viewer@uniclaretiana.edu.co / 123456\n\n";
    
    return true;
}

// =====================================================
// EJECUCIÓN DIRECTA DEL SCRIPT
// =====================================================

if (php_sapi_name() === 'cli') {
    echo "=================================================\n";
    echo "  UNICLARETIANA - INSTALADOR DE BASE DE DATOS\n";
    echo "=================================================\n\n";
    
    // Verificar si se pasaron argumentos
    $config = [];
    
    if ($argc > 1) {
        parse_str(implode('&', array_slice($argv, 1)), $config);
    }
    
    // Mostrar configuración
    echo "📋 Configuración:\n";
    echo "   • Host: " . ($config['host'] ?? 'localhost') . "\n";
    echo "   • Usuario: " . ($config['username'] ?? 'root') . "\n";
    echo "   • Base de datos: " . ($config['database'] ?? 'uniclaretiana_proyectos') . "\n\n";
    
    // Confirmar instalación
    echo "¿Desea continuar con la instalación? (s/N): ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);
    
    if (strtolower($response) !== 's' && strtolower($response) !== 'si') {
        echo "❌ Instalación cancelada\n";
        exit(1);
    }
    
    // Ejecutar instalación
    if (installDatabase($config)) {
        exit(0);
    } else {
        exit(1);
    }
}