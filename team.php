<?php
session_start();
require_once 'dbcon.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $playerOne = trim($_POST['player_one'] ?? '');
    $playerTwo = trim($_POST['player_two'] ?? '');

    if ($teamName === '' || $playerOne === '' || $playerTwo === '') {
        $error = 'Vul alle velden in.';
    } else {
        $_SESSION['escape'] = [
            'team_name' => $teamName,
            'player_one' => $playerOne,
            'player_two' => $playerTwo,
            'start_time' => time(),
            'duration' => 900,
            'solved_room_1' => [],
            'solved_room_2' => [],
            'finished' => false,
        ];

        try {
            $statement = $pdo->prepare('INSERT INTO teams (team_name, player_one, player_two, score) VALUES (:team_name, :player_one, :player_two, 0)');
            $statement->execute([
                'team_name' => $teamName,
                'player_one' => $playerOne,
                'player_two' => $playerTwo,
            ]);
        } catch (PDOException $e) {
        }

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
<body>
    <header class="site-header">
        <h1>Team aanmaken</h1>
        <p>Maak je team klaar voor de escape.</p>
    </header>

    <main class="content-card form-card">
        <h2>Vul jullie gegevens in</h2>

        <?php if ($error !== ''): ?>
            <p class="message message-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="post" class="form-grid">
            <label>
                <span>Teamnaam</span>
                <input type="text" name="team_name" placeholder="Bijvoorbeeld: Island Runners" required>
            </label>
            <label>
                <span>Teamlid 1</span>
                <input type="text" name="player_one" placeholder="Naam van speler 1" required>
            </label>
            <label>
                <span>Teamlid 2</span>
                <input type="text" name="player_two" placeholder="Naam van speler 2" required>
            </label>
            <button class="btn btn-primary" type="submit">Ga naar kamer 1</button>
        </form>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
