<?php
session_start();
require_once 'dbcon.php';

$status = $_GET['status'] ?? 'lost';
$reason = $_GET['reason'] ?? '';
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
$totalRiddles = 0;

try {
    $totalRiddles = (int) $pdo->query('SELECT COUNT(*) FROM riddles')->fetchColumn();
} catch (PDOException $e) {
    $totalRiddles = 6;
}

$timeSpent = max(0, time() - ($escape['start_time'] ?? time()));
$minutes = floor($timeSpent / 60);
$seconds = $timeSpent % 60;

if (empty($_SESSION['escape']['result_saved'])) {
    try {
        if (!empty($escape['team_id'])) {
            $statement = $pdo->prepare('
                UPDATE teams
                SET score = :score,
                    `escaped` = :escaped,
                    end_time_seconds = :end_time_seconds,
                    finished_at = NOW()
                WHERE id = :id
            ');
            $statement->execute([
                'score' => $correctCount,
                'escaped' => $won ? 1 : 0,
                'end_time_seconds' => $timeSpent,
                'id' => $escape['team_id'],
            ]);
        } else {
            $statement = $pdo->prepare('
                UPDATE teams
                SET score = :score,
                    `escaped` = :escaped,
                    end_time_seconds = :end_time_seconds,
                    finished_at = NOW()
                WHERE team_name = :team_name
                ORDER BY id DESC
                LIMIT 1
            ');
            $statement->execute([
                'score' => $correctCount,
                'escaped' => $won ? 1 : 0,
                'end_time_seconds' => $timeSpent,
                'team_name' => $escape['team_name'],
            ]);
        }

        $_SESSION['escape']['result_saved'] = true;
        $_SESSION['escape']['finished'] = true;
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

$subtitle = $won
    ? 'Jullie hebben minstens 4 van de ' . $totalRiddles . ' raadsels goed opgelost en zijn ontsnapt.'
    : 'Jullie run is afgelopen.';

$lead = $won
    ? 'Sterk gespeeld. Jullie haalden de winvoorwaarde van minimaal 4 goede antwoorden en bereikten de boot op tijd.'
    : 'Deze run telt als verlies. Probeer opnieuw en pak minstens 4 goede antwoorden zonder 3 fouten te maken.';

$summaryTitle = $won ? 'Boot bereikt' : 'Escape mislukt';
$summaryText = $won
    ? 'Minimaal 4 antwoorden waren goed. Jullie eindtijd is opgeslagen in het teamoverzicht.'
    : 'Ook dit resultaat is opgeslagen in het teamoverzicht.';

if (!$won && $reason === 'time') {
    $subtitle = 'De timer is afgelopen voordat jullie konden ontsnappen.';
    $lead = 'De tijd was op, dus het spel is automatisch gestopt.';
    $summaryTitle = 'Tijd is op';
    $summaryText = 'De run is automatisch beëindigd omdat de countdown op 00:00 kwam.';
}

if (!$won && $reason === 'mistakes') {
    $subtitle = 'Jullie hebben 3 fouten gemaakt en daardoor verloren.';
    $lead = 'Na de derde fout stopte het spel direct.';
    $summaryTitle = 'Te veel fouten';
    $summaryText = 'Voor winst mag je niet op 3 fouten uitkomen.';
}

if (!$won && $reason === 'score') {
    $subtitle = 'Alle raadsels zijn ingevuld, maar jullie haalden niet genoeg goede antwoorden.';
    $lead = 'Jullie hebben het einde gehaald, maar minder dan 4 antwoorden waren goed.';
    $summaryTitle = 'Te weinig goede antwoorden';
    $summaryText = 'Voor winst heb je minimaal 4 goede antwoorden nodig.';
}

if (!$won && $reason === 'stopped') {
    $subtitle = 'Het spel is handmatig gestopt.';
    $lead = 'De run is beëindigd voordat alle raadsels waren afgerond.';
    $summaryTitle = 'Spel gestopt';
    $summaryText = 'Je kunt meteen opnieuw starten vanaf de homepagina.';
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
        <p><?= htmlspecialchars($subtitle) ?></p>
    </header>

    <main class="page-shell">
        <section class="content-card finish-card <?= $won ? 'finish-card-win' : 'finish-card-loss' ?>">
            <div class="finish-badge <?= $won ? 'finish-badge-win' : 'finish-badge-loss' ?>">
                <?= $won ? 'Ontsnapt' : 'Niet gehaald' ?>
            </div>
            <h2><?= htmlspecialchars($escape['team_name']) ?></h2>
            <p class="finish-lead"><?= htmlspecialchars($lead) ?></p>

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
                    <strong><?= $wrongCount ?> / 3</strong>
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
                <strong><?= htmlspecialchars($summaryTitle) ?></strong>
                <p><?= htmlspecialchars($summaryText) ?></p>
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
