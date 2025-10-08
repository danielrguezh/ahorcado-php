<?php
declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Entity\Game;
use App\Infrastructure\Persistence\JsonGameRepository;
use App\Infrastructure\Persistence\JsonWordRepository;

final class GameService
{
    private JsonGameRepository $gameRepo;
    private JsonWordRepository $wordRepo;
    private int $maxAttempts;

    public function __construct(array $config)
    {
        $this->gameRepo = new JsonGameRepository($config['storage']['games_file']);
        $this->wordRepo = new JsonWordRepository($config['storage']['words_file']);
        $this->maxAttempts = $config['game']['max_attempts'];
    }

    public function newGame(): Game
    {
        $id = uniqid('game_', true);
        $word = $this->wordRepo->randomWord();
        $game = new Game($id, $word, $this->maxAttempts);
        $this->gameRepo->save($game);
        return $game;
    }

    public function guess(string $gameId, string $letter): void
    {
        $game = $this->gameRepo->find($gameId);
        if ($game) {
            $game->guess($letter);
            $this->gameRepo->save($game);
        }
    }

    public function getGame(string $gameId): ?Game
    {
        return $this->gameRepo->find($gameId);
    }
}
