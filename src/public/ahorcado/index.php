<?php
/**
 * @author danielrguezh
 * @version 1.0.0
 */

require 'Game.php';
require 'WordProvider.php';
require 'Storage.php';
require 'Renderer.php';

$storage = new Storage();
$provider = new WordProvider('words.txt');
$renderer = new Renderer();

$state = $storage->get('state');
$word = $storage->get('word');

if (!$word) {
    $word = $provider->randomWord();
    $storage->set('word', $word);
    $state = null;
}

$game = new Game($word, 6, $state);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Reiniciar partida sin borrar historial
    if (isset($_POST['reset'])) {
        // Guardar historial antes de limpiar
        $history = $storage->get('history', []);

        // Limpiar solo estado y palabra
        $storage->set('state', null);
        $storage->set('word', null);

        // Restaurar historial
        $storage->set('history', $history);

        header("Location: index.php");
        exit;
    }

    // Procesar letra
    if (isset($_POST['letter'])) {
        $letter = strtoupper(trim($_POST['letter']));
        if ($letter !== '') {
            $game->guessLetter($letter);

            // Guardar estado actual
            $storage->set('state', $game->toState());

            // Si la partida terminó, guardar resumen en historial
            if ($game->isWon() || $game->isLost()) {
                $history = $storage->get('history', []);
                $attemptsUsed = count($game->getUsedLetters());

                $history[] = [
                    'word' => $game->getWord(),
                    'result' => $game->isWon() ? 'Ganó' : 'Perdió',
                    'attemptsUsed' => $attemptsUsed,
                    'maxAttempts' => 6,
                    'time' => date('Y-m-d H:i:s')
                ];
                $storage->set('history', $history);
            }
        }
    }
}

// utilidad para escapar en HTML
function e($s) {
    return htmlentities((string)$s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ahorcado en La Révolution</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        :root{
            --azul: #0b3d91;
            --rojo: #c31b2a;
            --verde: #228B22;
            --marfil: #f6efe6;
            --marco-dark: rgba(0,0,0,0.35);
            --accent: #b0894a;
        }


        html,body{
            height:100%;
            margin:0;
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


        .container{
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
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom: 18px;
        }
        .title {
            font-size: 2.4rem;
            letter-spacing: 1px;
            color: #111;
            text-shadow: 0 1px 0 #fff;
            margin:0;
            display:flex;
            align-items:center;
            gap:12px;
        }
        .rosette {
            width:56px;
            height:56px;
            background:
                linear-gradient(90deg, var(--azul) 0 33%, #fff 33% 66%, var(--rojo) 66% 100%);
            border-radius:50%;
            border:4px solid var(--accent);
            box-shadow: 0 3px 0 rgba(0,0,0,0.15) inset;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            color: #fff;
            font-size:0.9rem;
        }


        .subtitle {
            font-style:italic;
            color:#4b3a2a;
            margin:0;
        }


        .main-grid{
            display:grid;
            grid-template-columns: 1fr 360px;
            gap: 22px;
            align-items:start;
        }


        .card {
            background: rgba(255,255,255,0.6);
            border: 1px solid rgba(0,0,0,0.06);
            padding:18px;
            border-radius:10px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
        }


        .hangman {
            background: linear-gradient(180deg, rgba(240,238,235,0.9), rgba(240,235,230,0.8));
            padding:12px;
            border-radius:8px;
            font-family: "Courier New", monospace;
            font-size:14px;
            line-height:1;
            text-align:center;
            border: 1px solid rgba(0,0,0,0.08);
        }


        .masked {
            font-size:1.6rem;
            letter-spacing: 6px;
            margin:14px 0;
            font-weight:700;
            color:#1c1c1c;
        }


        .meta {
            display:flex;
            gap:12px;
            align-items:center;
            flex-wrap:wrap;
            margin-bottom:12px;
            color:#3a2e24;
        }


        .used-letters {
            background: linear-gradient(90deg, rgba(255,255,255,0.6), rgba(250,248,245,0.6));
            padding:8px 10px;
            border-radius:8px;
            border:1px solid rgba(0,0,0,0.04);
            font-weight:600;
            letter-spacing:1px;
        }


        .controls form {
            display:flex;
            gap:10px;
            align-items:center;
            margin-top:8px;
        }
        .controls input[type="text"]{
            width:68px;
            padding:8px 10px;
            font-size:1.1rem;
            border:1px solid rgba(0,0,0,0.12);
            border-radius:8px;
            text-transform:uppercase;
            text-align:center;
        }
        .controls button {
            background: linear-gradient(180deg, var(--azul), #06306f);
            color:#fff;
            border:0;
            padding:8px 12px;
            border-radius:8px;
            cursor:pointer;
            font-weight:700;
            box-shadow: 0 4px 0 rgba(0,0,0,0.12);
        }
        .controls .reset {
            background: linear-gradient(180deg, var(--rojo), #8e1218);
            box-shadow: 0 4px 0 rgba(0,0,0,0.12);
        }


        .history {
            max-height:540px;
            overflow:auto;
        }
        .history h3 {
            margin-top:0;
            margin-bottom:6px;
            font-size:1.1rem;
            color:#2b2b2b;
        }
        .history ol {
            padding-left:18px;
            margin:0;
        }
        .history li {
            padding:8px;
            margin-bottom:8px;
            border-radius:6px;
            border:1px solid rgba(0,0,0,0.04);
            font-family: "Courier New", monospace;
            font-size:0.95rem;
            display:flex;
            justify-content:space-between;
            gap:8px;
            align-items:center;
        }
        .history .meta-step {
            font-size:0.85rem;
            font-style:italic;
        }


        .history .won {
            background-color: rgba(34,139,34,0.1);
            border-color: rgba(34,139,34,0.25);
        }
        .history .lost {
            background-color: rgba(255,0,0,0.1);
            border-color: rgba(255,0,0,0.25);
        }


        footer {
            margin-top:18px;
            text-align:center;
            color:#6b4f3a;
            font-size:0.9rem;
        }


        @media (max-width:900px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
            .history {
                max-height:240px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1 class="title">
                    <span class="rosette" aria-hidden="true">☩</span>
                    Ahorcado — La Révolution
                </h1>
                <p class="subtitle">Fuiste sentenciado a muerte. Adivina la palabra para pedir clemencia.</p>
            </div>
            <div style="text-align:right;">
                <div style="font-weight:700; color:#3b2c25;">Intentos restantes</div>
                <div style="font-size:1.6rem; color: var(--azul); font-weight:900;"><?= e($game->getAttemptsLeft()) ?></div>
            </div>
        </header>

        <div class="main-grid">
            <section class="card">
                <div class="hangman"><?= $renderer->ascii($game->getAttemptsLeft()); ?></div>
                <div class="masked"><?= e($game->getMaskedWord()) ?></div>
                <div class="meta">
                    <div class="used-letters">Letras usadas: <?= e(implode(', ', $game->getUsedLetters() ?: [])) ?: '—' ?></div>
                    <div>Máx intentos: <?= e($game->getAttemptsLeft() + (6 - $game->getAttemptsLeft())) ?></div>
                </div>

                <div class="controls">
                    <form method="post" style="display:inline-flex; align-items:center; gap:8px;">
                        <label style="font-weight:700;">Probar letra:</label>
                        <input type="text" name="letter" maxlength="1" required 
                            pattern="[A-Za-zÀ-ÖØ-öø-ÿ]" 
                            title="Introduce una letra" />
                        <button type="submit">Probar</button>
                    </form>

                    <form method="post" style="display:inline; margin-left:12px;">
                        <button class="reset" name="reset" type="submit">Reiniciar</button>
                    </form>
                </div>

                <footer>
                    Estás en medio de la Revolución Francesa. Adivina la palabra para evitar ser ejecutado.
                </footer>
            </section>

            <!-- Historial de partidas -->
            <aside class="card history">
                <h3>Historial de partidas</h3>
                <?php
                    $history = $storage->get('history', []);
                    if (empty($history)) {
                        echo "<p style='color:#6b5847;'>Aún no hay partidas finalizadas.</p>";
                    } else {
                        $total = count($history);
                        echo "<ol>";
                        foreach (array_reverse($history) as $i => $entry) {
                            $num = $total - $i;
                            $emoji = $entry['result'] === 'Ganó' ? '✅' : '❌';
                            $class = $entry['result'] === 'Ganó' ? 'won' : 'lost';

                            echo "<li class='{$class}'>";
                            echo "<div style='flex:1;'><strong>{$num}.</strong> {$emoji} ";
                            echo "Palabra: <strong>" . e($entry['word']) . "</strong> — ";
                            echo "Resultado: <em>" . e($entry['result']) . "</em></div>";
                            echo "<div class='meta-step'>Intentos usados: "
                                 . e($entry['attemptsUsed']) . " de " . e($entry['maxAttempts']);
                            echo " · <span style='white-space:nowrap;'>" . e($entry['time']) . "</span></div>";
                            echo "</li>";
                        }
                        echo "</ol>";
                    }
                ?>
            </aside>
        </div>
    </div>
</body>
</html>
