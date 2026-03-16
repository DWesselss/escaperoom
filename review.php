<?php
session_start();
require_once 'dbcon.php';

$message = '';
$messageClass = 'message-success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teamName = trim($_POST['team_name'] ?? '');
    $rating = (int) ($_POST['rating'] ?? 0);
    $difficulty = trim($_POST['difficulty'] ?? '');
    $reviewText = trim($_POST['review_text'] ?? '');

    if ($teamName === '' || $rating < 1 || $rating > 5 || $difficulty === '' || $reviewText === '') {
        $message = 'Vul alle velden correct in.';
        $messageClass = 'message-error';
    } else {
        try {
            $statement = $pdo->prepare('INSERT INTO reviews (team_name, rating, difficulty, review_text) VALUES (:team_name, :rating, :difficulty, :review_text)');
            $statement->execute([
                'team_name' => $teamName,
                'rating' => $rating,
                'difficulty' => $difficulty,
                'review_text' => $reviewText,
            ]);
            $message = 'Je review is opgeslagen.';
        } catch (PDOException $e) {
            $message = 'De pagina werkt, maar maak eerst de reviews-tabel aan in phpMyAdmin.';
            $messageClass = 'message-error';
        }
    }
}

$reviews = [];
try {
    $reviews = $pdo->query('SELECT team_name, rating, difficulty, review_text, created_at FROM reviews ORDER BY created_at DESC')->fetchAll();
} catch (PDOException $e) {
}

$prefillTeam = $_SESSION['escape']['team_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reviewpagina</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="site-header">
        <h1>Reviewpagina</h1>
        <p>Laat weten hoe jullie Escape Island vonden.</p>
    </header>

    <main class="content-card review-layout">
        <section class="form-panel">
            <h2>Plaats een review</h2>

            <?php if ($message !== ''): ?>
                <p class="message <?= $messageClass ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <label>
                    <span>Teamnaam</span>
                    <input type="text" name="team_name" value="<?= htmlspecialchars($prefillTeam) ?>" required>
                </label>
                <label>
                    <span>Rating</span>
                    <select name="rating" required>
                        <option value="">Kies een cijfer</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </label>
                <label>
                    <span>Moeilijkheid</span>
                    <select name="difficulty" required>
                        <option value="">Kies een niveau</option>
                        <option value="Makkelijk">Makkelijk</option>
                        <option value="Gemiddeld">Gemiddeld</option>
                        <option value="Moeilijk">Moeilijk</option>
                    </select>
                </label>
                <label class="full-width">
                    <span>Review</span>
                    <textarea name="review_text" rows="5" placeholder="Wat vonden jullie van de escape room?" required></textarea>
                </label>
                <button class="btn btn-primary" type="submit">Review opslaan</button>
            </form>
        </section>

        <section class="reviews-panel">
            <h2>Laatste reviews</h2>
            <?php if (count($reviews) === 0): ?>
                <p class="empty-state">Er zijn nog geen reviews zichtbaar.</p>
            <?php else: ?>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <article class="review-card">
                            <div class="review-card-top">
                                <strong><?= htmlspecialchars($review['team_name']) ?></strong>
                                <span><?= htmlspecialchars((string) $review['rating']) ?>/5 · <?= htmlspecialchars($review['difficulty']) ?></span>
                            </div>
                            <p><?= htmlspecialchars($review['review_text']) ?></p>
                            <small><?= htmlspecialchars((string) $review['created_at']) ?></small>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
