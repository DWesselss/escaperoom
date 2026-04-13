<?php
require_once 'dbcon.php';

$teams = [];
$hasExtendedColumns = true;

try {
    $teams = $pdo->query('SELECT team_name, player_one, player_two, score, escaped, end_time_seconds, finished_at FROM teams ORDER BY id DESC')->fetchAll();
} catch (PDOException $e) {
    $hasExtendedColumns = false;
    try {
        $teams = $pdo->query('SELECT team_name, player_one, player_two, score FROM teams ORDER BY id DESC')->fetchAll();
    } catch (PDOException $e) {
    }
}

function formatSeconds(?int $seconds): string
{
    if ($seconds === null || $seconds <= 0) {
        return '-';
    }

    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    return str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad((string) $remainingSeconds, 2, '0', STR_PAD_LEFT);
}

function getTeamStatus(array $team, bool $hasExtendedColumns): string
{
    if (!$hasExtendedColumns) {
        return '-';
    }

    if ((int) ($team['escaped'] ?? 0) === 1) {
        return 'Ontsnapt';
    }

    if (!empty($team['finished_at'])) {
        return 'Niet ontsnapt';
    }

    return 'Nog bezig';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teamoverzicht</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1>Admin · Teamoverzicht</h1>
        <p>Bekijk teams, score en eindtijd na afloop van het spel.</p>
    </header>

    <main class="page-shell page-shell-start">
        <section class="content-card admin-card">
            <div class="admin-topbar">
                <a class="btn btn-secondary" href="show_all_riddles.php">Alle raadsels</a>
                <a class="btn btn-primary" href="add_riddle.php">Nieuw raadsel</a>
            </div>

            <h2>Teams</h2>

            <?php if (!$hasExtendedColumns): ?>
                <p class="message message-error">Importeer de nieuwe escape-room.sql om ook ontsnapt, eindtijd en finished_at op te slaan.</p>
            <?php endif; ?>

            <?php if (count($teams) === 0): ?>
                <p class="empty-state">Er zijn nog geen teams opgeslagen.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Team</th>
                                <th>Speler 1</th>
                                <th>Overige spelers</th>
                                <th>Score</th>
                                <th>Status</th>
                                <th>Eindtijd</th>
                                <th>Afgerond op</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($teams as $team): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) ($team['team_name'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($team['player_one'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($team['player_two'] ?? '-')) ?></td>
                                    <td><?= htmlspecialchars((string) ($team['score'] ?? 0)) ?></td>
                                    <td><?= htmlspecialchars(getTeamStatus($team, $hasExtendedColumns)) ?></td>
                                    <td><?= $hasExtendedColumns ? htmlspecialchars(formatSeconds(isset($team['end_time_seconds']) ? (int) $team['end_time_seconds'] : null)) : '-' ?></td>
                                    <td><?= $hasExtendedColumns ? htmlspecialchars((string) ($team['finished_at'] ?? '-')) : '-' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
