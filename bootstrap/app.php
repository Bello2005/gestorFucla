<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Laravel application instance
| which serves as the "glue" for all the components of Laravel, and is
| the IoC container for the system binding all of the various parts.
|
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
|
| Next, we need to bind some important interfaces into the container so
| we will be able to resolve them when needed. The kernels serve the
| incoming requests to this application from both the web and CLI.
|
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

// --- BEGIN: ensure .env variables are available to getenv()/$_ENV/$_SERVER
// Respect .env.testing when APP_ENV=testing (e.g. when running php artisan test)
$__app_env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'local');
$__env_file = ($__app_env === 'testing') ? '.env.testing' : '.env';
$__env_path = dirname(__DIR__).DIRECTORY_SEPARATOR.$__env_file;
if (!file_exists($__env_path)) {
    $__env_path = dirname(__DIR__).DIRECTORY_SEPARATOR.'.env'; // fallback
}
if (file_exists($__env_path)) {
    $__env_lines = file($__env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($__env_lines as $__line) {
        $__line = trim($__line);
        if ($__line === '' || $__line[0] === '#') {
            continue;
        }
        if (strpos($__line, '=') === false) {
            continue;
        }
        list($__name, $__value) = explode('=', $__line, 2);
        $__name = trim($__name);
        $__value = trim($__value);
        // remove surrounding quotes if present
        if ((substr($__value, 0, 1) === '"' && substr($__value, -1) === '"') ||
            (substr($__value, 0, 1) === '\'' && substr($__value, -1) === '\'')) {
            $__value = substr($__value, 1, -1);
        }
        // Only set if not already defined (e.g. by phpunit.xml <env> elements)
        if (getenv($__name) === false) {
            putenv($__name.'='.$__value);
        }
        if (!array_key_exists($__name, $_ENV)) {
            $_ENV[$__name] = $__value;
        }
        if (!array_key_exists($__name, $_SERVER)) {
            $_SERVER[$__name] = $__value;
        }
    }
}
// --- END: ensure .env variables are available to getenv()/$_ENV/$_SERVER

return $app;
