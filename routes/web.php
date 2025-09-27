<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProyectoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\TestMailController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\EstadisticaController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\UserController;

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
    ->withoutMiddleware([\App\Http\Middleware\AuditAuthentication::class]); // Excluir middleware de auditoría
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
    
    // CRUD de proyectos
    Route::get('/proyectos', [ProyectoController::class, 'index'])->name('proyectos.index');
    Route::get('/proyectos/create', [ProyectoController::class, 'create'])->name('proyectos.create');
    Route::post('/proyectos', [ProyectoController::class, 'store'])->name('proyectos.store');
    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])->name('proyectos.show');
    Route::get('/proyectos/{proyecto}/edit', [ProyectoController::class, 'edit'])->name('proyectos.edit');
    Route::match(['PUT', 'PATCH'], '/proyectos/{proyecto}', [ProyectoController::class, 'update'])
        ->middleware(\App\Http\Middleware\VerifyProjectEditRequest::class)
        ->name('proyectos.update');
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])->name('proyectos.destroy');
    
    // Gestión de archivos de proyectos
    Route::delete('/proyectos/{proyecto}/archivo', [ProyectoController::class, 'deleteProyectoArchivo'])
        ->name('proyectos.delete.archivo');
    Route::delete('/proyectos/{proyecto}/contrato', [ProyectoController::class, 'deleteContratoArchivo'])
        ->name('proyectos.delete.contrato');
    Route::delete('/proyectos/{proyecto}/evidencia/{index}', [ProyectoController::class, 'deleteEvidenciaArchivo'])
        ->name('proyectos.delete.evidencia');
    
    // Exportación de proyectos
    Route::get('/proyectos-export/excel', [ProyectoController::class, 'exportExcel'])->name('proyectos.export.excel');
    Route::get('/proyectos-export/pdf', [ProyectoController::class, 'exportPdf'])->name('proyectos.export.pdf');
    Route::get('/proyectos-export/word', [ProyectoController::class, 'exportWord'])->name('proyectos.export.word');
    
    // Estadísticas
    Route::get('/estadistica', [EstadisticaController::class, 'index'])->name('estadistica');
});

// Rutas protegidas por autenticación y rol de administrador
Route::middleware(['auth', 'admin'])->group(function () {
    // Gestión de usuarios
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
    
    // Rutas de Auditoría
    Route::prefix('auditoria')->name('audit.')->group(function () {
        Route::get('/', [AuditController::class, 'index'])->name('index');
        Route::get('/exportar', [AuditController::class, 'export'])->name('export');
        Route::get('/{audit}', [AuditController::class, 'show'])->name('show');
    });
    
    // Solicitudes de acceso (admin)
    Route::get('/access-requests', [AccessRequestController::class, 'index'])->name('access-requests.index');
    Route::put('/access-requests/{request}/approve', [AccessRequestController::class, 'approve'])->name('access-requests.approve');
    Route::put('/access-requests/{request}/reject', [AccessRequestController::class, 'reject'])->name('access-requests.reject');
});

// temporal — borra después
Route::get('/__envcheck', function(){
    return response()->json([
        'getenv_APP_KEY' => getenv('APP_KEY'),
        '_ENV_APP_KEY'   => $_ENV['APP_KEY'] ?? null,
        '_SERVER_APP_KEY'=> $_SERVER['APP_KEY'] ?? null,
        'config_app_key' => config('app.key'),
        'php_sapi'       => php_sapi_name(),
        'loaded_ini'     => php_ini_loaded_file(),
    ]);
});


