<?php
require_once __DIR__ . '/includes/db.php';
session_start();
requireLogin();

$db = getDb();
$userId = $_SESSION['user_id'];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';
$resultat = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenValide = isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

    if (!$tokenValide) {
        $erreur = "Sesyon ekspire oswa fòm nan te soumèt de fwa. Tanpri eseye ankò.";
    } else {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if (isset($_POST['argent'])) {
            // NB: sa a se yon DEPO SIMILE pou demo a. Nan yon vrè pwodwi ak lajan
            // reyèl, etap sa a dwe ranplase pa yon kòmfimasyon ki soti nan yon
            // founisè peman ki gen lisans (webhook/API), jamè yon valè jwè a bay
            // dirèkteman nan yon fòm HTML.
            if (is_numeric($_POST['argent']) && $_POST['argent'] > 0) {
                $stmt = $db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                $stmt->execute([(float) $_POST['argent'], $userId]);

                $stmt = $db->prepare('INSERT INTO transactions (user_id, type, amount) VALUES (?, "depo", ?)');
                $stmt->execute([$userId, (float) $_POST['argent']]);
            } else {
                $erreur = "Antre yon depo valid (yon nonm pi gran pase 0).";
            }
        } elseif (isset($_POST['mise'], $_POST['n1'], $_POST['n2'], $_POST['n3'])) {
            $mise = $_POST['mise'];
            $n1 = $_POST['n1'];
            $n2 = $_POST['n2'];
            $n3 = $_POST['n3'];

            $valide = is_numeric($mise) && $mise > 0
                && is_numeric($n1) && $n1 >= 1 && $n1 <= 60
                && is_numeric($n2) && $n2 >= 1 && $n2 <= 60
                && is_numeric($n3) && $n3 >= 1 && $n3 <= 60;

            if (!$valide) {
                $erreur = "Antre yon mize valid ak twa nimewo ant 1 ak 60.";
            } else {
                $mise = (float) $mise;
                $n1 = (int) $n1;
                $n2 = (int) $n2;
                $n3 = (int) $n3;

                // Tranzaksyon DB + SELECT ... FOR UPDATE: anpeche yon jwè fè
                // de mize anmenmtan pou "double depanse" menm balans lan.
                $db->beginTransaction();
                try {
                    $stmt = $db->prepare('SELECT balance FROM users WHERE id = ? FOR UPDATE');
                    $stmt->execute([$userId]);
                    $balance = (float) $stmt->fetchColumn();

                    if ($mise > $balance) {
                        $erreur = "Ou pa gen ase lajan pou fè mize sa a.";
                        $db->rollBack();
                    } else {
                        $r1 = random_int(1, 60);
                        $r2 = random_int(1, 60);
                        $r3 = random_int(1, 60);
                        $genyen = ($n1 === $r1 && $n2 === $r2 && $n3 === $r3);
                        $gainNet = $genyen ? $mise * 2 : -$mise;

                        $stmt = $db->prepare('UPDATE users SET balance = balance + ? WHERE id = ?');
                        $stmt->execute([$gainNet, $userId]);

                        $stmt = $db->prepare(
                            'INSERT INTO bets (user_id, mise, n1, n2, n3, r1, r2, r3, gain) VALUES (?,?,?,?,?,?,?,?,?)'
                        );
                        $stmt->execute([$userId, $mise, $n1, $n2, $n3, $r1, $r2, $r3, $genyen ? $mise * 2 : 0]);

                        $stmt = $db->prepare('INSERT INTO transactions (user_id, type, amount) VALUES (?, ?, ?)');
                        $stmt->execute([$userId, $genyen ? 'genyen' : 'pèdi', abs($gainNet)]);

                        $db->commit();

                        $resultat = ($genyen ? "Ou genyen ! <br>" : "Ou pèdi ! <br>")
                            . "Nimewo jwe : $n1 $n2 $n3<br>"
                            . "Nimewo tire : $r1 $r2 $r3";
                    }
                } catch (Exception $e) {
                    $db->rollBack();
                    $erreur = "Yon erè rive. Tanpri eseye ankò.";
                }
            }
        }
    }
}

$stmt = $db->prepare('SELECT balance FROM users WHERE id = ?');
$stmt->execute([$userId]);
$balance = (float) $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="ht">
<head>
    <title>Loser Casino - Roulèt</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="casino.css">
</head>
<body>
<div id="bloc_de_page">
    <header>
        <div id="titre">
            <div id="logo"><h1>Loser Casino</h1></div>
            <h2>Roulèt</h2>
        </div>
        <nav>
            <ul>
                <li><a href="casino.php">Akèy</a></li>
                <li><a href="roulette.php">Roulèt</a></li>
                <li><a href="logout.php">Dekonekte (<?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <p>Mize sou nimewo w chwazi yo, epi fè jackpot la. Nimewo yo ale de 1 a 60.</p>

        <?php if ($erreur): ?>
            <p class="msg-erreur"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>

        <form method="post" action="roulette.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <?php if ($balance <= 0): ?>
                <label for="argent">Fè yon depo pou kontinye jwe :</label>
                <input type="number" id="argent" name="argent" min="1" step="any" required><br>
                <input type="submit" value="Depoze">
            <?php else: ?>
                <label for="mise">Antre kantite lajan ou vle mize :</label>
                <input type="number" id="mise" name="mise" min="1" max="<?php echo (int) $balance; ?>" step="any" required><br>
                <label for="n1">Premye nimewo :</label>
                <input type="number" id="n1" name="n1" min="1" max="60" required>
                <label for="n2">Dezyèm nimewo :</label>
                <input type="number" id="n2" name="n2" min="1" max="60" required>
                <label for="n3">Twazyèm nimewo :</label>
                <input type="number" id="n3" name="n3" min="1" max="60" required><br>
                <input type="submit" value="Jwe">
            <?php endif; ?>
        </form>

        <p>Balans : <?php echo number_format($balance, 2); ?> gourdes</p>

        <?php if ($resultat): ?>
            <p><?php echo $resultat; ?></p>
        <?php endif; ?>
    </main>
</div>
</body>
<footer>
    <p>Entèdi pou moun ki gen mwens pase 18 ane | Dimitri Mathieu</p>
</footer>
</html>
