<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
class Storage {
    private string $key;

    /**
     * Constructor por defecto
     */
    public function __construct(string $key = 'ahorcado') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->key = $key;

        if (!isset($_SESSION[$this->key])) {
            $_SESSION[$this->key] = [];
        }
    }

    public function get(string $name, $default = null) {
        return $_SESSION[$this->key][$name] ?? $default;
    }

    public function set(string $name, $value): void {
        $_SESSION[$this->key][$name] = $value;
    }

    public function reset(): void {
        $_SESSION[$this->key] = [];
    }
}
