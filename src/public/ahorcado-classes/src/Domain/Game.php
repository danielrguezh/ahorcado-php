<?php
declare(strict_types=1);

namespace App\Domain;

/**
 * Clase Game
 *
 * Gestiona el estado y la loigica del juego del Ahorcado.
 */
final class Game
{
    /** @var string[] Letras ya jugadas (en MAYUSCULAS) */
    private array $usedLetters;

    /** @var string Palabra objetivo en MAYUSCULAS */
    private string $word;

    /** @var int Numero maximo de intentos permitidos */
    private int $maxAttempts;

    /** @var int Intentos restantes */
    private int $attemptsLeft;

    /**
     * Constructor
     *
     * @param string               $word        Palabra objetivo en MAYUSCULAS
     * @param int                  $maxAttempts Numero maximo de intentos (por defecto 6)
     * @param array<string,mixed>|null $state   Estado serializado para restaurar partida
     */
    public function __construct(string $word, int $maxAttempts = 6, ?array $state = null)
    {
        $this->word = $word;
        $this->maxAttempts = $maxAttempts;

        if ($state) {
            $this->attemptsLeft = (int)($state['attemptsLeft'] ?? $maxAttempts);
            $this->usedLetters  = array_values(array_unique(array_map('strval', $state['usedLetters'] ?? [])));
        } else {
            $this->attemptsLeft = $maxAttempts;
            $this->usedLetters  = [];
        }
    }

    /**
     * Procesa un intento de letra
     *
     * - Normaliza a MAYUSCULA
     * - Ignora entradas invalidas o repetidas
     * - Resta intentos si la letra no pertenece a la palabra
     *
     * @param string $letter Letra propuesta
     * @return void
     */
    public function guessLetter(string $letter): void
    {
        $letter = mb_strtoupper($letter, 'UTF-8');
        if (!preg_match('/^[A-Z]$/', $letter)) {
            return; // ignora entradas invalidas
        }
        if (in_array($letter, $this->usedLetters, true) || $this->isWon() || $this->isLost()) {
            return;
        }
        $this->usedLetters[] = $letter;

        if (!str_contains($this->word, $letter)) {
            $this->attemptsLeft = max(0, $this->attemptsLeft - 1);
        }
    }

    /**
     * Devuelve la palabra enmascarada con guiones bajos y letras acertadas
     *
     * @return string Palabra enmascarada separada por espacios (p. ej., "C _ S A")
     */
    public function getMaskedWord(): string
    {
        $letters = mb_str_split($this->word);
        $masked = array_map(function ($ch) {
            return in_array($ch, $this->usedLetters, true) ? $ch : '_';
        }, $letters);
        return implode(' ', $masked);
    }

    /**
     * Obtiene el numero de intentos restantes
     *
     * @return int Intentos restantes
     */
    public function getAttemptsLeft(): int
    {
        return $this->attemptsLeft;
    }

    /**
     * Devuelve las letras ya jugadas
     *
     * @return string[] Letras usadas en MAYUSCULAS
     */
    public function getUsedLetters(): array
    {
        return $this->usedLetters;
    }

    /**
     * Indica si la partida esta ganada
     *
     * @return bool true si la palabra esta completamente descubierta, false en caso contrario
     */
    public function isWon(): bool
    {
        return !str_contains($this->getMaskedWord(), '_');
    }

    /**
     * Indica si la partida esta perdida
     *
     * @return bool true si no quedan intentos y no se ganoi, false en caso contrario
     */
    public function isLost(): bool
    {
        return $this->attemptsLeft <= 0 && !$this->isWon();
    }

    /**
     * Devuelve la palabra objetivo completa
     *
     * @return string Palabra en MAYUSCULAS
     */
    public function getWord(): string
    {
        return $this->word;
    }

    /**
     * Serializa el estado relevante de la partida para persistir en sesioin
     *
     * @return array{attemptsLeft:int, usedLetters: string[]}
     */
    public function toState(): array
    {
        return [
            'attemptsLeft' => $this->attemptsLeft,
            'usedLetters'  => $this->usedLetters,
        ];
    }
}
