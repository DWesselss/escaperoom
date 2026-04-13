<?php
session_start();
require_once '../dbcon.php';

if (!isset($_SESSION['escape'])) {
    header('Location: ../team.php');
    exit;
}

$escape = &$_SESSION['escape'];

if (!empty($escape['finished'])) {
    header('Location: ../finish.php');
    exit;
}

$riddlesRoomOne = $pdo->query('SELECT COUNT(*) FROM riddles WHERE roomId = 1')->fetchColumn();
if (count($escape['answered_room_1'] ?? []) < (int) $riddlesRoomOne) {
    header('Location: room_1.php');
    exit;
}

$players = $escape['players'] ?? array_values(array_filter([$escape['player_one'] ?? '', $escape['player_two'] ?? '']));
$riddles = $pdo->query('SELECT id, riddle, answer, hint, roomId FROM riddles WHERE roomId = 2 ORDER BY id ASC')->fetchAll();
$totalRiddles = 0;

try {
    $totalRiddles = (int) $pdo->query('SELECT COUNT(*) FROM riddles')->fetchColumn();
} catch (PDOException $e) {
    $totalRiddles = 6;
}

$escape['answered_room_2'] = $escape['answered_room_2'] ?? [];
$escape['correct_answers'] = $escape['correct_answers'] ?? [];
$escape['wrong_answers'] = $escape['wrong_answers'] ?? [];

$normalizeAnswer = static function (string $value): string {
    $value = trim(mb_strtolower($value));
    $value = preg_replace('/\s+/', ' ', $value);
    return $value;
};
$feedback = '';
$feedbackType = 'success';

$elapsedTime = max(0, time() - ($escape['start_time'] ?? time()));
$timeLimit = 15 * 60;
$remainingTime = max(0, $timeLimit - $elapsedTime);

if ($elapsedTime >= $timeLimit) {
    $escape['finished'] = true;
    header('Location: ../finish.php?status=lost&reason=time');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riddleId = (int) ($_POST['riddle_id'] ?? 0);
    $givenAnswer = trim($_POST['answer'] ?? '');

    foreach ($riddles as $riddle) {
        if ((int) $riddle['id'] !== $riddleId) {
            continue;
        }

        if (!in_array($riddleId, $escape['answered_room_2'], true)) {
            $escape['answered_room_2'][] = $riddleId;

            if ($normalizeAnswer($givenAnswer) === $normalizeAnswer((string) $riddle['answer'])) {
                $escape['correct_answers'][$riddleId] = true;
                unset($escape['wrong_answers'][$riddleId]);
                $feedback = 'Goed antwoord. Je zit bijna bij de boot.';
            } else {
                $escape['wrong_answers'][$riddleId] = true;
                unset($escape['correct_answers'][$riddleId]);
                $feedback = 'Antwoord opgeslagen. Je mag door, maar deze telt als fout.';
                $feedbackType = 'error';
            }
        } else {
            $feedback = 'Dit raadsel is al opgeslagen.';
            $feedbackType = 'error';
        }

        break;
    }

    if (count($escape['wrong_answers']) >= 3) {
        $escape['finished'] = true;
        header('Location: ../finish.php?status=lost&reason=mistakes');
        exit;
    }

    if (count($escape['answered_room_2']) === count($riddles)) {
        $escape['finished'] = true;
        $finalStatus = count($escape['correct_answers']) >= 4 ? 'won' : 'lost';
        $finalReason = $finalStatus === 'won' ? 'completed' : 'score';
        header('Location: ../finish.php?status=' . $finalStatus . '&reason=' . $finalReason);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamer 2</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-jungle">
    <header class="site-header site-header-jungle">
        <h1>Kamer 2 · Jungle</h1>
        <p>De laatste route naar de ontsnappingsboot ligt voor je.</p>
    </header>

    <main class="content-card content-card-jungle">
        <section class="hud-grid">
            <article class="hud-item hud-item-jungle">
                <span class="hud-label">Team</span>
                <strong><?= htmlspecialchars($escape['team_name']) ?></strong>
            </article>
            <article class="hud-item hud-item-jungle">
                <span class="hud-label">Spelers</span>
                <strong><?= htmlspecialchars(implode(', ', $players)) ?></strong>
            </article>
            <article class="hud-item hud-item-jungle">
                <span class="hud-label">Tijd bezig</span>
                <strong id="timer" data-elapsed="<?= $elapsedTime ?>" data-limit="<?= $timeLimit ?>" data-expire-url="../finish.php?status=lost&amp;reason=time">00:00</strong>
            </article>
            <article class="hud-item hud-item-jungle">
                <span class="hud-label">Tijd over</span>
                <strong id="countdown"><?= sprintf('%02d:%02d', floor($remainingTime / 60), $remainingTime % 60) ?></strong>
            </article>
            <article class="hud-item hud-item-jungle">
                <span class="hud-label">Voortgang</span>
                <strong><?= count($escape['answered_room_2']) ?> / <?= count($riddles) ?></strong>
            </article>
        </section>

        <section class="score-strip score-strip-jungle">
            <div class="score-pill score-pill-good">Goed: <?= count($escape['correct_answers']) ?> / <?= $totalRiddles ?></div>
            <div class="score-pill score-pill-bad">Fout: <?= count($escape['wrong_answers']) ?> / 3</div>
            <div class="score-pill">Minimaal 4 goed om te winnen</div>
        </section>

        <?php if ($feedback !== ''): ?>
            <p class="message message-<?= $feedbackType ?>"><?= htmlspecialchars($feedback) ?></p>
        <?php endif; ?>

        <section class="room-grid">
            <?php foreach ($riddles as $riddle): ?>
                <?php $solved = in_array($riddle['id'], $escape['answered_room_2'], true); ?>
                <article class="riddle-card riddle-card-jungle <?= $solved ? 'riddle-card-solved' : '' ?>">
                    <div class="riddle-card-top">
                        <span class="badge badge-jungle">Raadsel <?= htmlspecialchars((string) $riddle['id']) ?></span>
                        <?php if ($solved): ?>
                            <span class="status-pill"><?= isset($escape['correct_answers'][$riddle['id']]) ? 'Goed' : 'Opgeslagen' ?></span>
                        <?php endif; ?>
                    </div>
                    <h2><?= htmlspecialchars($riddle['riddle']) ?></h2>
                    <?php if ($solved): ?>
                        <p class="answer-result"><?= isset($escape['correct_answers'][$riddle['id']]) ? 'Dit antwoord telde als goed.' : 'Dit antwoord telde als fout.' ?></p>
                    <?php endif; ?>
                    <p class="hint">Hint: <?= htmlspecialchars($riddle['hint']) ?></p>
                    <?php if (!$solved): ?>
                        <form method="post" class="inline-form">
                            <input type="hidden" name="riddle_id" value="<?= $riddle['id'] ?>">
                            <input type="text" name="answer" placeholder="Typ je antwoord" required>
                            <button class="btn btn-primary btn-jungle" type="submit">Volgende</button>
                        </form>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>

        <div class="button-row button-row-center">
            <a class="btn btn-secondary" href="room_1.php">Terug naar kamer 1</a>
            <a class="btn btn-danger" href="../finish.php?status=lost&amp;reason=stopped">Spel stoppen</a>
        </div>
    </main>

    <footer class="site-footer site-footer-jungle">
        <p>Escape Island © 2026</p>
    </footer>

    <script src="../app.js?v=3"></script>
</body>
</html>
