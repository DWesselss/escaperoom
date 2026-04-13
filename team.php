<?php
session_start();
require_once 'dbcon.php';

$error = '';
$teamName = trim($_POST['team_name'] ?? '');
$playerCount = max(1, min(4, (int) ($_POST['player_count'] ?? 2)));
$players = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    for ($i = 1; $i <= $playerCount; $i++) {
        $players[] = trim($_POST['player_' . $i] ?? '');
    }

    $hasEmptyName = in_array('', $players, true);

    if ($teamName === '' || $hasEmptyName) {
        $error = 'Vul alle velden in.';
    } else {
        $teamId = null;
        $playerOne = $players[0] ?? '-';
        $playerTwo = count($players) > 1 ? implode(', ', array_slice($players, 1)) : '-';

        try {
            $statement = $pdo->prepare('INSERT INTO teams (team_name, player_one, player_two, score) VALUES (:team_name, :player_one, :player_two, 0)');
            $statement->execute([
                'team_name' => $teamName,
                'player_one' => $playerOne,
                'player_two' => $playerTwo,
            ]);
            $teamId = (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
        }

        $_SESSION['escape'] = [
            'team_id' => $teamId,
            'team_name' => $teamName,
            'players' => $players,
            'player_count' => $playerCount,
            'player_one' => $playerOne,
            'player_two' => $playerTwo,
            'start_time' => time(),
            'answered_room_1' => [],
            'answered_room_2' => [],
            'correct_answers' => [],
            'wrong_answers' => [],
            'finished' => false,
            'result_saved' => false,
        ];

        header('Location: rooms/room_1.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team aanmaken</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1>Team aanmaken</h1>
        <p>Maak je team klaar voor de escape.</p>
    </header>

    <main class="page-shell">
        <section class="content-card form-card">
            <h2>Vul jullie gegevens in</h2>

            <?php if ($error !== ''): ?>
                <p class="message message-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" class="form-grid" id="team-form">
                <label>
                    <span>Teamnaam</span>
                    <input type="text" name="team_name" placeholder="Bijvoorbeeld: Island Runners" value="<?= htmlspecialchars($teamName) ?>" required>
                </label>

                <label>
                    <span>Aantal spelers</span>
                    <select name="player_count" id="player-count" required>
                        <option value="1" <?= $playerCount === 1 ? 'selected' : '' ?>>1 speler</option>
                        <option value="2" <?= $playerCount === 2 ? 'selected' : '' ?>>2 spelers</option>
                        <option value="3" <?= $playerCount === 3 ? 'selected' : '' ?>>3 spelers</option>
                        <option value="4" <?= $playerCount === 4 ? 'selected' : '' ?>>4 spelers</option>
                    </select>
                </label>

                <div class="player-fields" id="player-fields">
                    <?php for ($i = 1; $i <= 4; $i++): ?>
                        <?php
                        $fieldValue = $_POST['player_' . $i] ?? '';
                        $isVisible = $i <= $playerCount;
                        ?>
                        <label class="player-field<?= !$isVisible ? ' is-hidden' : '' ?>" data-player-field="<?= $i ?>" style="display: <?= $isVisible ? 'grid' : 'none' ?>;">
                            <span>Teamlid <?= $i ?></span>
                            <input type="text" name="player_<?= $i ?>" placeholder="Naam van speler <?= $i ?>" value="<?= htmlspecialchars($fieldValue) ?>" <?= $isVisible ? 'required' : '' ?>>
                        </label>
                    <?php endfor; ?>
                </div>

                <button class="btn btn-primary" type="submit">Ga naar kamer 1</button>
            </form>
        </section>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>

    <script src="app.js?v=3"></script>
</body>
</html>
