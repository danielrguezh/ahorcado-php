<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */

declare(strict_types=1);

namespace App\Domain\Entity;

final class Game
{
    public string $id;
    private string $word;
    private array $guesses = [];
    private int $maxAttempts;
    private string $status = 'playing'; // playing | won | lost

    public function __construct(string $id, string $word, int $maxAttempts)
    {
        $this->id = $id;
        $this->word = strtolower($word);
        $this->maxAttempts = $maxAttempts;
    }

    public function maskedWord(): string
    {
        return implode('', array_map(fn($c) => in_array($c, $this->guesses) ? $c : '_', str_split($this->word)));
    }

    public function guess(string $letter): void
    {
        $letter = strtolower($letter);
        if (!in_array($letter, $this->guesses)) {
            $this->guesses[] = $letter;
        }
        if ($this->maskedWord() === $this->word) {
            $this->status = 'won';
        } elseif ($this->remainingAttempts() <= 0) {
            $this->status = 'lost';
        }
    }

    public function remainingAttempts(): int
    {
        $fails = count(array_diff($this->guesses, str_split($this->word)));
        return $this->maxAttempts - $fails;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'word' => $this->word,
            'guesses' => $this->guesses,
            'maxAttempts' => $this->maxAttempts,
            'status' => $this->status,
        ];
    }

    public static function fromArray(array $data): self
    {
        $game = new self($data['id'], $data['word'], $data['maxAttempts']);
        $game->guesses = $data['guesses'];
        $game->status = $data['status'];
        return $game;
    }
}
