<?php
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';
$resultat = '';
$soldeDefini = isset($_SESSION['argent']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenValide = isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

    if (!$tokenValide) {
        $erreur = "Sesyon ekspire oswa fòm nan te soumèt de fwa. Tanpri eseye ankò.";
    } else {
        // Nouvo token pou chak soumisyon aksepte, sa anpeche moun repete menm requête a (rafrechi paj)
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        if (!$soldeDefini) {
            if (isset($_POST['argent']) && is_numeric($_POST['argent']) && $_POST['argent'] > 0) {
                $_SESSION['argent'] = (float) $_POST['argent'];
                $soldeDefini = true;
            } else {
                $erreur = "Antre yon depo valid (yon nonm pi gran pase 0) anvan ou jwe.";
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
            } elseif ($mise > $_SESSION['argent']) {
                $erreur = "Ou pa gen ase lajan pou fè mize sa a.";
            } else {
                $mise = (float) $mise;
                $n1 = (int) $n1;
                $n2 = (int) $n2;
                $n3 = (int) $n3;

                $r1 = rand(1, 60);
                $r2 = rand(1, 60);
                $r3 = rand(1, 60);

                if ($n1 === $r1 && $n2 === $r2 && $n3 === $r3) {
                    $_SESSION['argent'] += $mise * 2;
                    $resultat = "Ou genyen ! <br>"
                        . "Nimewo jwe : " . $n1 . " " . $n2 . " " . $n3 . "<br>"
                        . "Nimewo tire : " . $r1 . " " . $r2 . " " . $r3;
                } else {
                    $_SESSION['argent'] -= $mise;
                    $resultat = "Ou pèdi ! <br>"
                        . "Nimewo jwe : " . $n1 . " " . $n2 . " " . $n3 . "<br>"
                        . "Nimewo tire : " . $r1 . " " . $r2 . " " . $r3;

                    if ($_SESSION['argent'] <= 0) {
                        $resultat .= "<br>Fen de pati. Ou pa gen plis lajan.";
                        session_destroy();
                        $soldeDefini = false;
                    }
                }
            }
        }
    }
}
?>
<html>
    <head>
        <title>Roulette</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="casino.css">
    </head>

    <body>
        <h1>Bienvenue sur roulette</h1>
        <p>Misez sur les numéros de votre choix, et faites le jackpot. Les numéros sont de 1 à 60.</p>

        <?php if ($erreur): ?>
            <p style="color:red;"><?php echo htmlspecialchars($erreur); ?></p>
        <?php endif; ?>

        <form method="post" action="roulette.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <?php if (!$soldeDefini): ?>
                <label for="argent">Faites un dépôt avant de jouer :</label>
                <input type="number" id="argent" name="argent" min="1" step="any" required><br>
            <?php else: ?>
                <label for="mise">Entrez le montant que vous voulez miser :</label>
                <input type="number" id="mise" name="mise" min="1" step="any" required> <br>
                <label for="n1">Entrez le premier nombre : </label>
                <input type="number" id="n1" name="n1" min="1" max="60" required>
                <label for="n2">Entrez le deuxième nombre :</label>
                <input type="number" id="n2" name="n2" min="1" max="60" required>
                <label for="n3">Entrez le troisième nombre :</label>
                <input type="number" id="n3" name="n3" min="1" max="60" required><br>
            <?php endif; ?>
            <input type="submit" value="Jouer">
        </form>

        <?php if ($soldeDefini): ?>
            <p>Balance : <?php echo htmlspecialchars($_SESSION['argent']); ?> gourdes</p>
        <?php endif; ?>

        <?php if ($resultat): ?>
            <p><?php echo $resultat; ?></p>
        <?php elseif (!$erreur): ?>
            <p>Vous devez jouer !</p>
        <?php endif; ?>
    </body>
    <footer>
        <p>Interdit aux -18 ans | Dimitri Mathieu</p>
    </footer>
</html>
