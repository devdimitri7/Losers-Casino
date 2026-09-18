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

        $db = getDb();
        $stmt = $db->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Menm mesaj erè pou idantifyan pa egziste OSWA modpas mal,
        // pou moun pa ka devine ki idantifyan ki egziste (anti-enumeration).
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $erreur = "Idantifyan oswa modpas pa kòrèk.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $username;
            header('Location: roulette.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ht">
<head>
    <meta charset="utf-8">
    <title>Koneksyon - Loser Casino</title>
    <link rel="stylesheet" href="casino.css">
</head>
<body>
<div id="bloc_de_page">
    <header>
        <div id="titre"><h1>Loser Casino</h1><h2>Koneksyon</h2></div>
    </header>
    <main id="konekte">
        <?php if ($erreur): ?>
            <p class="msg-erreur"><?php echo htmlspecialchars($erreur, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        <form method="post" action="login.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            <label for="username">Idantifyan</label> :
            <input type="text" id="username" name="username" required><br>
            <label for="password">Modpas</label> :
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Konekte">
        </form>
        <p>Ou pa gen kont ? <a href="register.php">Enskri w</a></p>
    </main>
</div>
</body>
</html>
