<?php
declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Application\Services\GameService;

final class GameController
{
    private GameService $service;

    public function __construct(private array $config)
    {
        $this->service = new GameService($config);
    }

    public function handle(): void
    {
        session_start();
        $action = $_GET['action'] ?? 'play';

        switch ($action) {
            case 'new':
                $game = $this->service->newGame();
                $_SESSION['game_id'] = $game->id;
                header('Location: index.php');
                exit;
            case 'guess':
                if (isset($_SESSION['game_id'], $_POST['letter'])) {
                    $this->service->guess($_SESSION['game_id'], $_POST['letter']);
                }
                header('Location: index.php');
                exit;
            case 'play':
            default:
                if (!isset($_SESSION['game_id'])) {
                    $game = $this->service->newGame();
                    $_SESSION['game_id'] = $game->id;
                }
                $game = $this->service->getGame($_SESSION['game_id']);
                require __DIR__ . '/../Views/game.php';
        }
    }
}
