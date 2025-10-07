<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */
declare(strict_types=1);

return [
    'storage' => [
        'words_file' => __DIR__ . '/../storage/words.json',
        'games_file' => __DIR__ . '/../storage/games.json',
    ],
    'game' => [
        'max_attempts' => 7,
    ],
];