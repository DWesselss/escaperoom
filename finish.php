<?php
session_start();
require_once 'dbcon.php';

$status = $_GET['status'] ?? 'lost';
$escape = $_SESSION['escape'] ?? null;

if ($escape === null) {
    header('Location: index.php');
    exit;
}

$players = $escape['players'] ?? array_values(array_filter([$escape['player_one'] ?? '', $escape['player_two'] ?? '']));
$correctCount = count($escape['correct_answers'] ?? []);
$wrongCount = count($escape['wrong_answers'] ?? []);
$answeredCount = count($escape['answered_room_1'] ?? []) + count($escape['answered_room_2'] ?? []);
$won = $status === 'won';
if (!isset($_GET['status'])) {
    $won = $correctCount >= 4 && $wrongCount < 3;
}
$totalSolved = $correctCount;
$totalRiddles = 0;

try {
    $totalRiddles = (int) $pdo->query('SELECT COUNT(*) FROM riddles')->fetchColumn();
} catch (PDOException $e) {
    $totalRiddles = 6;
}

$timeSpent = max(0, time() - ($escape['start_time'] ?? time()));
$minutes = floor($timeSpent / 60);
$seconds = $timeSpent % 60;

if (empty($_SESSION['escape']['result_saved']) && !empty($escape['team_id'])) {
    try {
        $statement = $pdo->prepare('UPDATE teams SET score = :score, escaped = :escaped, end_time_seconds = :end_time_seconds, finished_at = NOW() WHERE id = :id');
        $statement->execute([
            'score' => $correctCount,
            'escaped' => $won ? 1 : 0,
            'end_time_seconds' => $timeSpent,
            'id' => $escape['team_id'],
        ]);
        $_SESSION['escape']['result_saved'] = true;
        $_SESSION['escape']['finished'] = true;
    } catch (PDOException $e) {
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $won ? 'Victory' : 'Verlies' ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1><?= $won ? 'Victory' : 'Verlies' ?></h1>
        <p><?= $won ? 'Jullie hebben minstens 4 van de 6 raadsels goed opgelost en zijn ontsnapt.' : 'Jullie hebben 3 fouten gemaakt of minder dan 4 goede antwoorden gehaald.' ?></p>
    </header>

    <main class="page-shell">
        <section class="content-card finish-card <?= $won ? 'finish-card-win' : 'finish-card-loss' ?>">
            <div class="finish-badge <?= $won ? 'finish-badge-win' : 'finish-badge-loss' ?>">
                <?= $won ? 'Ontsnapt' : 'Niet gehaald' ?>
            </div>
            <h2><?= htmlspecialchars($escape['team_name']) ?></h2>
            <p class="finish-lead">
                <?= $won ? 'Sterk gespeeld. Jullie haalden de winvoorwaarde van minimaal 4 goede antwoorden en bereikten de boot op tijd.' : 'Deze run telt als verlies. Probeer opnieuw en pak minstens 4 van de 6 raadsels goed zonder 3 fouten te maken.' ?>
            </p>

            <div class="stats-grid">
                <article class="stat-box">
                    <span>Spelers</span>
                    <strong><?= htmlspecialchars(implode(', ', $players)) ?></strong>
                </article>
                <article class="stat-box">
                    <span>Goede antwoorden</span>
                    <strong><?= $correctCount ?> / <?= $totalRiddles ?></strong>
                </article>
                <article class="stat-box">
                    <span>Totale tijd</span>
                    <strong><?= str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) ?>:<?= str_pad((string) $seconds, 2, '0', STR_PAD_LEFT) ?></strong>
                </article>
                <article class="stat-box">
                    <span>Foute antwoorden</span>
                    <strong><?= $wrongCount ?> / <?= $totalRiddles ?></strong>
                </article>
                <article class="stat-box">
                    <span>Ingevuld</span>
                    <strong><?= $answeredCount ?> / <?= $totalRiddles ?></strong>
                </article>
                <article class="stat-box">
                    <span>Resultaat</span>
                    <strong><?= $won ? 'Victory' : 'Verlies' ?></strong>
                </article>
            </div>

            <div class="finish-summary <?= $won ? 'finish-summary-win' : 'finish-summary-loss' ?>">
                <strong><?= $won ? 'Boot bereikt' : 'Escape mislukt' ?></strong>
                <p><?= $won ? 'Minimaal 4 van de 6 antwoorden waren goed. Jullie eindtijd is opgeslagen in het teamoverzicht.' : 'Voor winst heb je minimaal 4 goede antwoorden nodig. Ook dit resultaat is opgeslagen in het teamoverzicht.' ?></p>
            </div>

            <div class="button-row button-row-center">
                <a class="btn btn-primary" href="review.php">Review achterlaten</a>
                <a class="btn btn-secondary" href="show_all_teams.php">Teamoverzicht</a>
                <a class="btn btn-secondary" href="index.php">Opnieuw spelen</a>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
