<?php
require_once __DIR__ . '/includes/db.php';
session_start();

$db = getDb();
$topWins = $db->query(
    'SELECT u.username, b.gain, b.mise, b.created_at
     FROM bets b JOIN users u ON u.id = b.user_id
     ORDER BY b.gain DESC LIMIT 3'
)->fetchAll();

$estKonekte = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="ht">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="casino.css" />
    <title>Loser Casino</title>
</head>
<body>
<div id="bloc_de_page">
    <header>
        <div id="titre">
            <div id="logo">
                <img src="casino12.jpg" alt="Logo Loser Casino" />
                <h1>Loser Casino</h1>
            </div>
            <h2>Kazino pèdan yo</h2>
        </div>
        <nav>
            <ul>
                <li><a href="#">Poker</a></li>
                <li><a href="#">Jeu de kat</a></li>
                <li><a href="roulette.php">Roulèt</a></li>
                <?php if ($estKonekte): ?>
                    <li><a href="logout.php">Dekonekte</a></li>
                <?php else: ?>
                    <li><a href="login.php">Konekte</a></li>
                    <li><a href="register.php">Enskri w</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div id="barnierre1">
        <div id="descrip">
            Genyen lajan !
            <a href="roulette.php" class="loto">Wè plis...</a>
        </div>
    </div>

    <section>
        <aside>
            <img src="bol.jpeg" alt="" class="bol" />
            <p>Dènye gwo genyen yo</p>
            <table>
                <caption>Klasman jeneral</caption>
                <thead>
                    <tr><th>Jwè</th><th>Genyen</th><th>Mize</th></tr>
                </thead>
                <tbody>
                    <?php if ($topWins): ?>
                        <?php foreach ($topWins as $w): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($w['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo number_format($w['gain'], 2); ?> G</td>
                                <td><?php echo number_format($w['mise'], 2); ?> G</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3">Poko gen genyen anrejistre. Vin jwe premye !</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </aside>

        <article>
            <img src="bonus.jpeg" class="bonus" alt="Bonus" />
            <h2>Ankese pwen viktwa ou yo.</h2>
            <p>
                Kounye a sou Loser Casino. Si w fè twa genyen, siperyè a 10,000,000 gourdes,
                w ap resevwa yon pwofi <a href="#">wè plis...</a>
            </p>
        </article>
    </section>

    <?php if (!$estKonekte): ?>
        <p>Konekte oswa enskri w kounye a pou jwe.</p>
    <?php endif; ?>
</div>

<footer>
    <div id="bat">
        <div id="enfo">
            <p>
                (©) copyright - tout dwa rezève | Dimitri Mathieu<br />
                <a href="#">Kontakte m !</a><br />
                Telefòn (509) 4043-0402
            </p>
        </div>
        <div id="je">
            <p>Jwèt sa a entèdi pou moun ki gen mwens pase 18 ane</p>
        </div>
    </div>
</footer>
</body>
</html>
