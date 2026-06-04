<?php

use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\BancoProyectoAnexoController;
use App\Http\Controllers\BancoProyectoController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\ConvocatoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuditAuthentication;
use App\Http\Middleware\VerifyProjectEditRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Rutas públicas
Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm']);
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Recuperación de contraseña (público)
Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update');

// Solicitudes de Acceso (público)
Route::get('/access-requests/create', [AccessRequestController::class, 'create'])->name('access-requests.create');
Route::post('/access-requests', [AccessRequestController::class, 'store'])->name('access-requests.store');

// Verificación de correo (público)
Route::get('/email/verify/{token}', [EmailVerificationController::class, 'verify'])
    ->name('verify.email.change')
    ->withoutMiddleware([AuditAuthentication::class]); // Excluir middleware de auditoría
Route::post('/email/confirm-change', [EmailVerificationController::class, 'confirmEmailChange'])
    ->name('email.confirm.change');
Route::get('/perfil/verificar-email/{token}', [ProfileController::class, 'verifyEmail'])->name('profile.verify-email');

// Rutas protegidas por autenticación
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gestión de contraseña
    Route::post('/change-temporary-password', [PasswordController::class, 'changeTemporaryPassword'])
        ->name('password.change.temporary');

    // Perfil de usuario
    Route::post('/perfil/actualizar', [ProfileController::class, 'update'])->name('profile.update');

    // CRUD de proyectos — lectura (requiere permiso de vista)
    Route::get('/proyectos', [ProyectoController::class, 'index'])
        ->middleware('module.permission:proyectos,view')->name('proyectos.index');
    Route::get('/proyectos-export/excel', [ProyectoController::class, 'exportExcel'])
        ->middleware('module.permission:proyectos,view')->name('proyectos.export.excel');
    Route::get('/proyectos-export/pdf', [ProyectoController::class, 'exportPdf'])
        ->middleware('module.permission:proyectos,view')->name('proyectos.export.pdf');
    Route::get('/proyectos-export/word', [ProyectoController::class, 'exportWord'])
        ->middleware('module.permission:proyectos,view')->name('proyectos.export.word');

    // CRUD de proyectos — escritura (requiere permiso de edición)
    // IMPORTANTE: las rutas estáticas deben ir ANTES de las paramétricas /{proyecto}
    Route::get('/proyectos/create', [ProyectoController::class, 'create'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.create');
    Route::post('/proyectos', [ProyectoController::class, 'store'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.store');
    // Rutas paramétricas — van DESPUÉS de las estáticas para evitar que {proyecto} capture "create"
    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])
        ->middleware('module.permission:proyectos,view')->name('proyectos.show');
    Route::get('/proyectos/{proyecto}/edit', [ProyectoController::class, 'edit'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.edit');
    Route::match(['PUT', 'PATCH'], '/proyectos/{proyecto}', [ProyectoController::class, 'update'])
        ->middleware(['module.permission:proyectos,edit', VerifyProjectEditRequest::class])
        ->name('proyectos.update');
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.destroy');
    Route::post('/proyectos/{proyecto}/certificado', [ProyectoController::class, 'subirCertificado'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.certificado.store');
    Route::delete('/proyectos/{proyecto}/certificado', [ProyectoController::class, 'eliminarCertificado'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.certificado.destroy');
    Route::delete('/proyectos/{proyecto}/archivo', [ProyectoController::class, 'deleteProyectoArchivo'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.delete.archivo');
    Route::delete('/proyectos/{proyecto}/contrato', [ProyectoController::class, 'deleteContratoArchivo'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.delete.contrato');
    Route::delete('/proyectos/{proyecto}/presupuesto', [ProyectoController::class, 'deletePresupuestoArchivo'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.delete.presupuesto');
    Route::delete('/proyectos/{proyecto}/cronograma', [ProyectoController::class, 'deleteCronogramaArchivo'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.delete.cronograma');
    Route::delete('/proyectos/{proyecto}/evidencia/{index}', [ProyectoController::class, 'deleteEvidenciaArchivo'])
        ->middleware('module.permission:proyectos,edit')->name('proyectos.delete.evidencia');

    // Convocatorias (FGE-05) — accesible para quienes pueden ver proyectos
    Route::get('/convocatorias', [ConvocatoriaController::class, 'index'])
        ->middleware('module.permission:proyectos,view')->name('convocatorias.index');
    Route::post('/convocatorias', [ConvocatoriaController::class, 'store'])
        ->middleware('module.permission:proyectos,edit')->name('convocatorias.store');
    Route::get('/convocatorias/{convocatoria}', [ConvocatoriaController::class, 'show'])
        ->middleware('module.permission:proyectos,view')->name('convocatorias.show');
    Route::put('/convocatorias/{convocatoria}', [ConvocatoriaController::class, 'update'])
        ->middleware('module.permission:proyectos,edit')->name('convocatorias.update');
    Route::delete('/convocatorias/{convocatoria}', [ConvocatoriaController::class, 'destroy'])
        ->middleware('module.permission:proyectos,edit')->name('convocatorias.destroy');

    // Estadísticas
    Route::get('/estadistica', [EstadisticaController::class, 'index'])
        ->middleware('module.permission:estadistica,view')->name('estadistica');

    // Banco de Proyectos — estáticas ANTES de paramétricas
    Route::prefix('banco-proyectos')->name('banco.')->group(function () {
        Route::get('/export/excel', [BancoProyectoController::class, 'exportExcel'])
            ->middleware('module.permission:banco,view')->name('export.excel');
        Route::get('/export/pdf', [BancoProyectoController::class, 'exportPdf'])
            ->middleware('module.permission:banco,view')->name('export.pdf');
        Route::get('/', [BancoProyectoController::class, 'index'])
            ->middleware('module.permission:banco,view')->name('index');
        // create debe ir ANTES de /{bancoProyecto} para evitar que "create" sea capturado como ID
        Route::get('/create', [BancoProyectoController::class, 'create'])
            ->middleware('module.permission:banco,edit')->name('create');
        Route::post('/', [BancoProyectoController::class, 'store'])
            ->middleware('module.permission:banco,edit')->name('store');
        // Paramétricas después de las estáticas
        Route::get('/{bancoProyecto}/historial', [BancoProyectoController::class, 'historialJson'])
            ->middleware('module.permission:banco,view')->name('historial');
        Route::get('/{bancoProyecto}/anexos/{anexo}/download', [BancoProyectoAnexoController::class, 'download'])
            ->middleware('module.permission:banco,view')->name('anexos.download');
        Route::get('/{bancoProyecto}', [BancoProyectoController::class, 'show'])
            ->middleware('module.permission:banco,view')->name('show');
        Route::get('/{bancoProyecto}/edit', [BancoProyectoController::class, 'edit'])
            ->middleware('module.permission:banco,edit')->name('edit');
        Route::post('/{bancoProyecto}/certificado', [BancoProyectoController::class, 'subirCertificado'])
            ->middleware('module.permission:banco,edit')->name('certificado.store');
        Route::delete('/{bancoProyecto}/certificado', [BancoProyectoController::class, 'eliminarCertificado'])
            ->middleware('module.permission:banco,edit')->name('certificado.destroy');
        Route::post('/{bancoProyecto}/anexos', [BancoProyectoAnexoController::class, 'store'])
            ->middleware('module.permission:banco,edit')->name('anexos.store');
        Route::delete('/{bancoProyecto}/anexos/{anexo}', [BancoProyectoAnexoController::class, 'destroy'])
            ->middleware('module.permission:banco,edit')->name('anexos.destroy');
        Route::post('/{bancoProyecto}/anexos/{anexo}/restore', [BancoProyectoAnexoController::class, 'restore'])
            ->middleware('module.permission:banco,edit')->name('anexos.restore');
        Route::put('/{bancoProyecto}', [BancoProyectoController::class, 'update'])
            ->middleware('module.permission:banco,edit')->name('update');
        Route::delete('/{bancoProyecto}', [BancoProyectoController::class, 'destroy'])
            ->middleware('module.permission:banco,edit')->name('destroy');
        Route::patch('/{bancoProyecto}/estado', [BancoProyectoController::class, 'cambiarEstado'])
            ->middleware('module.permission:banco,edit')->name('estado');
    });
});

// Rutas de lectura para módulos de administración — accesibles por permiso de matriz
Route::middleware('auth')->group(function () {
    // Auditoría — solo lectura
    Route::prefix('auditoria')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])
            ->middleware('module.permission:auditoria,view')->name('index');
        Route::get('/exportar', [AuditController::class, 'export'])
            ->middleware('module.permission:auditoria,view')->name('export');
        Route::get('/{audit}', [AuditController::class, 'show'])
            ->middleware('module.permission:auditoria,view')->name('show');
    });

    // Solicitudes — ver lista
    Route::get('/access-requests', [AccessRequestController::class, 'index'])
        ->middleware('module.permission:solicitudes,view')->name('access-requests.index');

    // Catálogos — ver
    Route::get('/catalogos', [CatalogoController::class, 'index'])
        ->middleware('module.permission:catalogos,view')->name('catalogos.index');

    // Usuarios — ver lista y detalle (para mostrar en modal)
    Route::get('/users', [UserController::class, 'index'])
        ->middleware('module.permission:usuarios,view')->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->middleware('module.permission:usuarios,view')->name('users.show');
});

// Rutas de escritura de administración — solo admin
Route::middleware(['auth', 'admin'])->group(function () {
    // Gestión de usuarios (operaciones destructivas)
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/permissions', [PermissionController::class, 'update'])->name('users.permissions.update');

    // Solicitudes — aprobar/rechazar
    Route::put('/access-requests/{request}/approve', [AccessRequestController::class, 'approve'])->name('access-requests.approve');
    Route::put('/access-requests/{request}/reject', [AccessRequestController::class, 'reject'])->name('access-requests.reject');

    // Catálogos — escritura
    Route::prefix('catalogos')->name('catalogos.')->group(function () {
        Route::post('/programas', [CatalogoController::class, 'storePrograma'])->name('programas.store');
        Route::put('/programas/{programa}', [CatalogoController::class, 'updatePrograma'])->name('programas.update');
        Route::delete('/programas/{programa}', [CatalogoController::class, 'destroyPrograma'])->name('programas.destroy');
        Route::post('/tipos', [CatalogoController::class, 'storeTipo'])->name('tipos.store');
        Route::put('/tipos/{tipo}', [CatalogoController::class, 'updateTipo'])->name('tipos.update');
        Route::delete('/tipos/{tipo}', [CatalogoController::class, 'destroyTipo'])->name('tipos.destroy');
        Route::post('/lineas', [CatalogoController::class, 'storeLinea'])->name('lineas.store');
        Route::put('/lineas/{linea}', [CatalogoController::class, 'updateLinea'])->name('lineas.update');
        Route::delete('/lineas/{linea}', [CatalogoController::class, 'destroyLinea'])->name('lineas.destroy');
    });
});

// temporal — borra después
Route::get('/__envcheck', function () {
    return response()->json([
        'getenv_APP_KEY' => getenv('APP_KEY'),
        '_ENV_APP_KEY' => $_ENV['APP_KEY'] ?? null,
        '_SERVER_APP_KEY' => $_SERVER['APP_KEY'] ?? null,
        'config_app_key' => config('app.key'),
        'php_sapi' => php_sapi_name(),
        'loaded_ini' => php_ini_loaded_file(),
    ]);
});
