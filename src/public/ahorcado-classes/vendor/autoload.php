<?php
declare(strict_types=1);

/**
 * Autoload manual estilo PSR-4 minimo para el proyecto Ahorcado.
 * Carga clases bajo el namespace raiz 'App\\' desde el directorio 'src/'.
 *
 * Ubicacion esperada: project_root/vendor/autoload.php
 */

spl_autoload_register(static function (string $class): void {
    $prefix  = 'App\\';
    $baseDir = __DIR__ . '/../src/'; 

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative = substr($class, $len);

    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

return true;
