<?php
session_start();
$status = $_GET['status'] ?? 'lost';
$escape = $_SESSION['escape'] ?? null;

if ($escape === null) {
    header('Location: index.php');
    exit;
}

$won = $status === 'won';
$totalSolved = count($escape['solved_room_1']) + count($escape['solved_room_2']);
$timeSpent = time() - $escape['start_time'];
$minutes = floor($timeSpent / 60);
$seconds = $timeSpent % 60;
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eindscherm</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <h1><?= $won ? 'Ontsnapt' : 'Tijd verlopen' ?></h1>
        <p><?= $won ? 'Jullie hebben de boot op tijd bereikt.' : 'De tijd is op voordat jullie konden ontsnappen.' ?></p>
    </header>

    <main class="content-card finish-card <?= $won ? 'finish-card-win' : 'finish-card-loss' ?>">
        <h2><?= htmlspecialchars($escape['team_name']) ?></h2>
        <div class="stats-grid">
            <article class="stat-box">
                <span>Spelers</span>
                <strong><?= htmlspecialchars($escape['player_one']) ?> & <?= htmlspecialchars($escape['player_two']) ?></strong>
            </article>
            <article class="stat-box">
                <span>Opgeloste raadsels</span>
                <strong><?= $totalSolved ?> / 6</strong>
            </article>
            <article class="stat-box">
                <span>Speeltijd</span>
                <strong><?= str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) ?>:<?= str_pad((string) $seconds, 2, '0', STR_PAD_LEFT) ?></strong>
            </article>
        </div>
        <div class="button-row button-row-center">
            <a class="btn btn-primary" href="review.php">Review achterlaten</a>
            <a class="btn btn-secondary" href="index.php">Opnieuw spelen</a>
        </div>
    </main>
</body>
</html>
