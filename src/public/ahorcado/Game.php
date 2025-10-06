<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
class Game {
    private string $word;
    private int $maxAttempts;
    private int $attemptsLeft;
    private array $usedLetters;

    /**
     * Constructor por defecto
     */
    public function __construct(string $word, int $maxAttempts = 6, ?array $state = null) {
        $this->word = strtoupper($word);
        $this->maxAttempts = $maxAttempts;

        if ($state) {
            $this->attemptsLeft = $state['attemptsLeft'];
            $this->usedLetters = $state['usedLetters'];
        } else {
            $this->attemptsLeft = $maxAttempts;
            $this->usedLetters = [];
        }
    }
    /**
     * Funcion para determinar si la palabra contiene determinada letra
     */
    public function guessLetter(string $letter): void {
        $letter = strtoupper($letter);
        if (in_array($letter, $this->usedLetters)) {
            return;
        }

        $this->usedLetters[] = $letter;

        if (strpos($this->word, $letter) === false) {
            $this->attemptsLeft--;
        }
    }

    /**
     * Funcion para enmascarar la palabra en espacios por longitud de caracteres
     */
    public function getMaskedWord(): string {
        $masked = '';
        foreach (str_split($this->word) as $char) {
            $masked .= in_array($char, $this->usedLetters) ? $char : '_';
            $masked .= ' ';
        }
        return trim($masked);
    }

    /**
     * Funcion que obtiene los intentos restantes
     */
    public function getAttemptsLeft(): int {
        return $this->attemptsLeft;
    }

    /**
     * Funcion que obtiene las letras utilizadas
     */
    public function getUsedLetters(): array {
        return $this->usedLetters;
    }

    /**
     * Funcion que determina si el usuario gana la partida
     */
    public function isWon(): bool {
        foreach (str_split($this->word) as $char) {
            if (!in_array($char, $this->usedLetters)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Funcion que determina si el usuario pierde la partida
     */
    public function isLost(): bool {
        return $this->attemptsLeft <= 0 && !$this->isWon();
    }

    /**
     * Funcion que obtiene la palabra
     */
    public function getWord(): string {
        return $this->word;
    }

    /**
     * Funcion que determina el estado de la partida
     */
    public function toState(): array {
        return [
            'attemptsLeft' => $this->attemptsLeft,
            'usedLetters' => $this->usedLetters
        ];
    }
}
