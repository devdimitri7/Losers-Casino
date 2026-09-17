<html>
    <head> 
        <title>Roulette</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="rou5.css">
    </head>

    <body>
        <h1>Bienvenue sur roulette</h1>
        <p>Misez sur les numéros de votre choix, et faite le jackpot. Les numéros sont de 1 à 60.</p>
        <form method="post" action="roulette.php">
            <label for="argent">Faite un dépot avant de jouer :</label>
            <input type="number" id="argent" name="argent"><br>
            <label for="mise"> Entrez le montant que vous voulez miser :</label>
            <input type="number" id="mise" name="mise"> <br>
            <label for="n1">Entrez le premier nombre : </label>
            <input type="number" id="n1" name="n1">
            <label for="n2">Entrez le deuxième nombre :</label>
            <input type="number" id="n2" name="n2">
            <label for="n3">Entrez le troisième nombre :</label>
            <input type="number" id="n3" name="n3"><br>
            <input type="submit" value="Jouer">
        </form>
        <?php
        session_start();
        if(!isset($_SESSION['argent'])){
            $_SESSION['argent'] = $_POST['argent'];
            

        }
        echo " Balance  " . $_SESSION['argent'] . " gourdes <br><br>";
        
        if(isset($_POST['mise']) && isset($_POST['n1']) && isset($_POST['n2']) && isset($_POST['n3'])){
            $r1 = rand(1, 60);
            $r2 = rand(1, 60);
            $r3 = rand(1, 60);

            if($_POST['n1'] == $r1 && $_POST['n2'] == $r2 && $_POST['n3'] == $r3){
                $_SESSION['argent'] += ($_POST['mise'] * 2);
                echo " Vous avez gagner ! <br> 
                 Votre balance actuelle est de  " . $_SESSION['argent'] . " Gourdes <br> 
                 Numéros joués " . $_POST['n1'] . " " . $_POST['n2'] . $_POST['n3'] . " <br> 
                 Numéros tirés " . $r1. " " . $r2. " " . $r3;
            } else{
                $_SESSION['argent'] -= $_POST['mise'];
                echo " Vous avez perdu ! <br> 
                Numéros joués " . $_POST['n1'] . " " . $_POST['n2'] . " " . $_POST['n3'] . " <br> 
                Numéros tirés " . $r1. " " . $r2. " " . $r3 . " <br> 
                Votre balance actuelle est de  " . $_SESSION['argent'] . " Gourdes ";

                if($_SESSION <= 0){
                    echo "Fin de la partie. Vous n'avez plus d'argent.";
                    session_destroy();
                }
            }
        } else{
            echo " Vous devez jouer !";
        }
       

        ?>
    </body>
    <footer>
        <p>Interdit aux -18 ans | Dimitri Mathieu</p>
    </footer>
</html>