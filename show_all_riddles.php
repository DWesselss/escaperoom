<?php
require_once 'dbcon.php';

$riddles = [];
try {
    $riddles = $pdo->query('SELECT id, riddle, answer, hint, roomId FROM riddles ORDER BY roomId ASC, id ASC')->fetchAll();
} catch (PDOException $e) {
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle raadsels</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1>Admin · Alle raadsels</h1>
        <p>Overzicht van alle escape room raadsels per kamer.</p>
    </header>

    <main class="page-shell page-shell-start">
        <section class="content-card admin-card">
            <div class="admin-topbar">
                <a class="btn btn-primary" href="add_riddle.php">Nieuw raadsel</a>
                <a class="btn btn-secondary" href="show_all_teams.php">Teams</a>
            </div>

            <h2>Raadseloverzicht</h2>

            <?php if (count($riddles) === 0): ?>
                <p class="empty-state">Er zijn nog geen raadsels toegevoegd.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Kamer</th>
                                <th>Raadsel</th>
                                <th>Antwoord</th>
                                <th>Hint</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($riddles as $riddle): ?>
                                <tr>
                                    <td><?= htmlspecialchars((string) $riddle['id']) ?></td>
                                    <td><?= htmlspecialchars((string) $riddle['roomId']) ?></td>
                                    <td><?= htmlspecialchars($riddle['riddle']) ?></td>
                                    <td><?= htmlspecialchars($riddle['answer']) ?></td>
                                    <td><?= htmlspecialchars($riddle['hint']) ?></td>
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
