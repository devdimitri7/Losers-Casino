<?php
require_once __DIR__ . '/includes/db.php';
session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tokenValide = isset($_POST['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);

    if (!$tokenValide) {
        $erreur = "Sesyon ekspire. Tanpri eseye ankò.";
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!preg_match('/^[a-zA-Z0-9_]{3,30}$/', $username)) {
            $erreur = "Idantifyan dwe gen 3 a 30 karaktè (lèt, chif, tiret ba sèlman).";
        } elseif (strlen($password) < 8) {
            $erreur = "Modpas la dwe gen omwen 8 karaktè.";
        } else {
            $db = getDb();
            $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
            $stmt->execute([$username]);

            if ($stmt->fetch()) {
                $erreur = "Idantifyan sa a deja itilize.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare('INSERT INTO users (username, password_hash, balance) VALUES (?, ?, 0)');
                $stmt->execute([$username, $hash]);

                $_SESSION['user_id'] = (int) $db->lastInsertId();
                $_SESSION['username'] = $username;
                header('Location: roulette.php');
                exit;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ht">
<head>
    <meta charset="utf-8">
    <title>Enskripsyon - Loser Casino</title>
    <link rel="stylesheet" href="casino.css">
</head>
<body>
<div id="bloc_de_page">
    <header>
        <div id="titre"><h1>Loser Casino</h1><h2>Enskripsyon</h2></div>
    </header>
    <main id="konekte">
        <?php if ($erreur): ?>
            <p class="msg-erreur"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="post" action="register.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="username">Idantifyan</label> :
            <input type="text" id="username" name="username" required><br>
            <label for="password">Modpas</label> :
            <input type="password" id="password" name="password" required minlength="8"><br>
            <input type="submit" value="Kreye kont">
        </form>
        <p>Ou gen deja yon kont ? <a href="login.php">Konekte</a></p>
    </main>
</div>
</body>
</html>
