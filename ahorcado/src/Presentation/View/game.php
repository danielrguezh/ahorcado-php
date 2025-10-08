<?php
/** @var \App\Domain\Entity\Game $game */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Juego del Ahorcado</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .word { font-size: 2rem; letter-spacing: 10px; }
        .status { margin: 20px; font-weight: bold; }
        .letters input { margin: 2px; padding: 5px; font-size: 1rem; }
    </style>
</head>
<body>
    <h1>🎮 Juego del Ahorcado</h1>

    <p class="word"><?= htmlspecialchars($game->maskedWord()) ?></p>
    <p>Intentos restantes: <?= $game->remainingAttempts() ?></p>

    <?php if ($game->status() === 'playing'): ?>
        <form method="post" action="index.php?action=guess">
            <input type="text" name="letter" maxlength="1" required autofocus>
            <button type="submit">Probar letra</button>
        </form>
    <?php elseif ($game->status() === 'won'): ?>
        <p class="status" style="color: green;">¡Ganaste! 🎉 La palabra era <b><?= htmlspecialchars($game->maskedWord()) ?></b></p>
        <a href="index.php?action=new">Nueva partida</a>
    <?php elseif ($game->status() === 'lost'): ?>
        <p class="status" style="color: red;">Perdiste 😢 La palabra era <b><?= htmlspecialchars($game->toArray()['word']) ?></b></p>
        <a href="index.php?action=new">Nueva partida</a>
    <?php endif; ?>

    <p>Letras probadas: <?= implode(', ', $game->toArray()['guesses']) ?></p>
</body>
</html>
