<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['escape'])) {
    header('Location: ../team.php');
    exit;
}

$escape = &$_SESSION['escape'];
$timeLeft = ($escape['start_time'] + $escape['duration']) - time();
if ($timeLeft <= 0) {
    header('Location: ../finish.php?status=lost');
    exit;
}

$riddles = $pdo->query('SELECT id, riddle, answer, hint, roomId FROM riddles WHERE roomId = 1 ORDER BY id ASC')->fetchAll();
$feedback = '';
$feedbackType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riddleId = (int) ($_POST['riddle_id'] ?? 0);
    $answer = trim($_POST['answer'] ?? '');

    foreach ($riddles as $riddle) {
        if ($riddle['id'] === $riddleId) {
            if (mb_strtolower($answer) === mb_strtolower($riddle['answer'])) {
                if (!in_array($riddleId, $escape['solved_room_1'], true)) {
                    $escape['solved_room_1'][] = $riddleId;
                }
                $feedback = 'Goed antwoord.';
                $feedbackType = 'success';
            } else {
                $feedback = 'Dat antwoord is niet goed. Gebruik de hint en probeer opnieuw.';
                $feedbackType = 'error';
            }
            break;
        }
    }

    if (count($escape['solved_room_1']) === count($riddles)) {
        header('Location: room_2.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamer 1</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="site-header">
        <h1>Kamer 1 · Strand</h1>
        <p>Vind de eerste aanwijzingen en maak je klaar voor het binnenland.</p>
    </header>

    <main class="content-card">
        <section class="hud-grid">
            <article class="hud-item">
                <span class="hud-label">Team</span>
                <strong><?= htmlspecialchars($escape['team_name']) ?></strong>
            </article>
            <article class="hud-item">
                <span class="hud-label">Spelers</span>
                <strong><?= htmlspecialchars($escape['player_one']) ?> & <?= htmlspecialchars($escape['player_two']) ?></strong>
            </article>
            <article class="hud-item">
                <span class="hud-label">Timer</span>
                <strong id="timer" data-time-left="<?= $timeLeft ?>">00:00</strong>
            </article>
            <article class="hud-item">
                <span class="hud-label">Voortgang</span>
                <strong><?= count($escape['solved_room_1']) ?> / <?= count($riddles) ?></strong>
            </article>
        </section>

        <?php if ($feedback !== ''): ?>
            <p class="message <?= $feedbackType === 'success' ? 'message-success' : 'message-error' ?>"><?= htmlspecialchars($feedback) ?></p>
        <?php endif; ?>

        <section class="room-grid">
            <?php foreach ($riddles as $riddle): ?>
                <?php $solved = in_array($riddle['id'], $escape['solved_room_1'], true); ?>
                <article class="riddle-card <?= $solved ? 'riddle-card-solved' : '' ?>">
                    <div class="riddle-card-top">
                        <span class="badge">Raadsel <?= htmlspecialchars((string) $riddle['id']) ?></span>
                        <?php if ($solved): ?>
                            <span class="status-pill">Opgelost</span>
                        <?php endif; ?>
                    </div>
                    <h2><?= htmlspecialchars($riddle['riddle']) ?></h2>
                    <p class="hint">Hint: <?= htmlspecialchars($riddle['hint']) ?></p>
                    <?php if (!$solved): ?>
                        <form method="post" class="inline-form">
                            <input type="hidden" name="riddle_id" value="<?= $riddle['id'] ?>">
                            <input type="text" name="answer" placeholder="Typ je antwoord" required>
                            <button class="btn btn-primary" type="submit">Controleer</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="button-row button-row-center">
            <a class="btn btn-secondary" href="../index.php">Terug naar home</a>
        </div>
    </main>

    <script src="../app.js"></script>
</body>
</html>
