<?php
require_once 'dbcon.php';

$message = '';
$messageClass = 'message-success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $riddle = trim($_POST['riddle'] ?? '');
    $answer = trim($_POST['answer'] ?? '');
    $hint = trim($_POST['hint'] ?? '');
    $roomId = (int) ($_POST['roomId'] ?? 0);

    if ($riddle === '' || $answer === '' || $hint === '' || !in_array($roomId, [1, 2], true)) {
        $message = 'Vul alle velden correct in.';
        $messageClass = 'message-error';
    } else {
        try {
            $statement = $pdo->prepare('INSERT INTO riddles (riddle, answer, hint, roomId) VALUES (:riddle, :answer, :hint, :roomId)');
            $statement->execute([
                'riddle' => $riddle,
                'answer' => $answer,
                'hint' => $hint,
                'roomId' => $roomId,
            ]);
            $message = 'Het raadsel is toegevoegd.';
        } catch (PDOException $e) {
            $message = 'Het toevoegen van het raadsel is mislukt.';
            $messageClass = 'message-error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raadsel toevoegen</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1>Admin · Raadsel toevoegen</h1>
        <p>Voeg nieuwe escape room raadsels toe en koppel ze aan een kamer.</p>
    </header>

    <main class="page-shell">
        <section class="content-card form-card">
            <div class="admin-topbar">
                <a class="btn btn-secondary" href="show_all_riddles.php">Alle raadsels</a>
                <a class="btn btn-secondary" href="show_all_teams.php">Teams</a>
            </div>

            <h2>Nieuw raadsel</h2>

            <?php if ($message !== ''): ?>
                <p class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <label class="full-width">
                    <span>Raadsel</span>
                    <textarea name="riddle" rows="4" placeholder="Typ hier het raadsel" required></textarea>
                </label>
                <label>
                    <span>Antwoord</span>
                    <input type="text" name="answer" placeholder="Juiste antwoord" required>
                </label>
                <label>
                    <span>Kamer</span>
                    <select name="roomId" required>
                        <option value="">Kies een kamer</option>
                        <option value="1">Kamer 1</option>
                        <option value="2">Kamer 2</option>
                    </select>
                </label>
                <label class="full-width">
                    <span>Hint</span>
                    <input type="text" name="hint" placeholder="Handige hint voor de speler" required>
                </label>
                <button class="btn btn-primary" type="submit">Raadsel opslaan</button>
            </form>
        </section>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
