<?php
declare(strict_types=1);

use App\{WordProvider, Storage, Renderer};
use App\Domain\Game;

require __DIR__ . '/../vendor/autoload.php';

$storage = new Storage();
$provider = new WordProvider(__DIR__ . '/../data/words.txt');
$renderer = new Renderer();

$state = $storage->get('state');
$word  = $storage->get('word');

if (!$word) {
    $word = $provider->randomWord();
    $storage->set('word', $word);
}

$maxAttempts = 6;
$game = new Game($word, $maxAttempts, $state);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if (isset($_POST['letter'])) {
        $letter = strtoupper(trim($_POST['letter']));
        if ($letter !== '') {
            $game->guessLetter($letter);
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'reset') {
        $storage->reset();
        header("Location: /");
        exit;
    }

    $storage->set('state', $game->toState());
}

function e($s): string {
    return htmlentities((string)$s, ENT_QUOTES, 'UTF-8');
}

$masked   = $game->getMaskedWord();
$used     = implode(', ', $game->getUsedLetters());
$attempts = $game->getAttemptsLeft();
$status   = $game->isWon() ? '🎉 ¡Has ganado!' : ($game->isLost() ? '💀 Has perdido' : 'En juego');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ahorcado en La Révolution</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root {
            --azul: #0b3d91;
            --rojo: #c31b2a;
            --verde: #228B22;
            --marfil: #f6efe6;
            --marco-dark: rgba(0,0,0,0.35);
            --accent: #b0894a;
        }

        html, body {
            height: 100%;
            margin: 0;
            font-family: "Garamond", "Times New Roman", Times, serif;
            background-color: var(--marfil);
            color: #222;
        }

        body {
            background-image:
                linear-gradient(180deg, rgba(11,61,145,0.14) 0 10%, rgba(255,255,255,0.0) 10%),
                linear-gradient(180deg, rgba(195,27,42,0.06) 90%, rgba(255,255,255,0.0) 90%);
            background-repeat: repeat;
            background-size: 240px;
            background-attachment: fixed;
        }

        .container {
            max-width: 920px;
            margin: 36px auto;
            background: linear-gradient(180deg, rgba(255,255,255,0.85), rgba(250,245,240,0.9));
            border: 6px solid rgba(0,0,0,0.08);
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            border-radius: 12px;
            padding: 28px;
            position: relative;
            overflow: hidden;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .title {
            font-size: 2.4rem;
            letter-spacing: 1px;
            color: #111;
            text-shadow: 0 1px 0 #fff;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .rosette {
            width: 56px;
            height: 56px;
            background: linear-gradient(90deg, var(--azul) 0 33%, #fff 33% 66%, var(--rojo) 66% 100%);
            border-radius: 50%;
            border: 4px solid var(--accent);
            box-shadow: 0 3px 0 rgba(0,0,0,0.15) inset;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #fff;
            font-size: 0.9rem;
        }

        .subtitle {
            font-style: italic;
            color: #4b3a2a;
            margin: 0;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 22px;
            align-items: start;
        }

        .card {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(0,0,0,0.06);
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }

        .hangman {
            background: linear-gradient(180deg, rgba(240,238,235,0.9), rgba(240,235,230,0.8));
            padding: 12px;
            border-radius: 8px;
            font-family: "Courier New", monospace;
            font-size: 14px;
            line-height: 1;
            text-align: center;
            border: 1px solid rgba(0,0,0,0.08);
        }

        .masked {
            font-size: 1.6rem;
            letter-spacing: 6px;
            margin: 14px 0;
            font-weight: 700;
            color: #1c1c1c;
        }

        .meta {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 12px;
            color: #3a2e24;
        }

        .used-letters {
            background: linear-gradient(90deg, rgba(255,255,255,0.6), rgba(250,248,245,0.6));
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid rgba(0,0,0,0.04);
            font-weight: 600;
            letter-spacing: 1px;
        }

        .controls form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 8px;
        }

        .controls input[type="text"] {
            width: 68px;
            padding: 8px 10px;
            font-size: 1.1rem;
            border: 1px solid rgba(0,0,0,0.12);
            border-radius: 8px;
            text-transform: uppercase;
            text-align: center;
        }

        .controls button {
            background: linear-gradient(180deg, var(--azul), #06306f);
            color: #fff;
            border: 0;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            box-shadow: 0 4px 0 rgba(0,0,0,0.12);
        }

        .controls .reset {
            background: linear-gradient(180deg, var(--rojo), #8e1218);
            box-shadow: 0 4px 0 rgba(0,0,0,0.12);
        }

        footer {
            margin-top: 18px;
            text-align: center;
            color: #6b4f3a;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1 class="title">
                    <span class="rosette" aria-hidden="true">☩</span>
                    Ahorcado en La Révolution
                </h1>
                <p class="subtitle">Fuiste sentenciado a muerte. Adivina la palabra para pedir clemencia.</p>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700; color:#3b2c25;">Intentos restantes</div>
                <div style="font-size:1.6rem; color: var(--azul); font-weight:900;"><?= e($attempts) ?></div>
            </div>
        </header>

        <section class="card">
            <div class="hangman"><?= $renderer->ascii($attempts); ?></div>
            <div class="masked"><?= e($masked) ?></div>
            <div class="meta">
                <div class="used-letters">Letras usadas: <?= e($used ?: '—') ?></div>
                <div>Máx intentos: <?= e($maxAttempts) ?></div>
            </div>

            <div class="controls">
                <?php if (!$game->isWon() && !$game->isLost()): ?>
                    <form method="post">
                        <label style="font-weight:700;">Probar letra:</label>
                        <input type="text" name="letter" maxlength="1" required pattern="[A-Za-zÀ-ÖØ-öø-ÿ]" title="Introduce una letra" />
                        <button type="submit">Probar</button>
                    </form>
                <?php endif; ?>

                <form method="post" style="display:inline; margin-left:12px;">
                    <input type="hidden" name="action" value="reset">
                    <button class="reset" type="submit">Reiniciar</button>
                </form>
            </div>

            <footer>
                <?= $status ?><?= $game->isLost() ? " — La palabra era <strong>" . e($game->getWord()) . "</strong>." : "" ?>
            </footer>
        </section>
    </div>
</body>
</html>
