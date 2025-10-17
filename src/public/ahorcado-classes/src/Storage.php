<?php
declare(strict_types=1);

namespace App;

/**
 * Clase Storage
 *
 * Encapsula la persistencia de estado en la sesioin PHP.
 */
final class Storage
{
    /**
     * @var string Clave de namespacing para $_SESSION
     */
    private string $key;

    /**
     * Constructor: asegura que la sesioin esta iniciada y el espacio de datos existe
     *
     * @param string $key Clave de namespacing (por defecto 'ahorcado')
     */
    public function __construct(string $key = 'ahorcado')
    {
        $this->key = $key;
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $_SESSION[$this->key] ??= [];
    }

    /**
     * Recupera un valor desde la sesioin
     *
     * @param string $name    Nombre del valor
     * @param mixed  $default Valor por defecto si no existe
     * @return mixed
     */
    public function get(string $name, $default = null)
    {
        return $_SESSION[$this->key][$name] ?? $default;
    }

    /**
     * Guarda un valor en la sesioin
     *
     * @param string $name  Nombre
     * @param mixed  $value Valor
     * @return void
     */
    public function set(string $name, $value): void
    {
        $_SESSION[$this->key][$name] = $value;
    }

    /**
     * Restablece el estado del espacio de sesioin
     *
     * @return void
     */
    public function reset(): void
    {
        $_SESSION[$this->key] = [];
    }
}
