<?php
session_start();
unset($_SESSION['escape']);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escape Island</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="theme-home">
    <header class="site-header">
        <h1>🏝 Escape Island</h1>
        <p>Kun jij ontsnappen van het tropische privé-eiland?</p>
    </header>

    <main class="hero-card hero-card--wide">
        <div class="hero-content">
            <span class="eyebrow">Survival Escape</span>
            <h2>Het verhaal</h2>
            <p>
                Je bent gestrand op een tropisch privé-eiland midden op zee. De eigenaar en zijn medewerkers jagen op je.
                Los de raadsels op, vind je route en ontsnap voordat de tijd op is.
            </p>
            <div class="button-row">
                <a class="btn btn-primary" href="team.php">Start de escape</a>
                <a class="btn btn-secondary" href="review.php">Reviews bekijken</a>
            </div>
        </div>
    </main>

    <footer class="site-footer">
        <p>Escape Island © 2026</p>
    </footer>
</body>
</html>
