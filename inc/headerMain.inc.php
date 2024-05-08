<!DOCTYPE html>
<?php session_start();
    if($_SESSION['isLog'] == false){
        header('Location: ../index.php');
    }
?>
<html lang="fr">
    <head>
        <title>Groupes</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@600&family=Libre+Franklin:ital,wght@1,900&family=Merriweather+Sans:ital,wght@1,500&family=Press+Start+2P&display=swap" rel="stylesheet">
    </head>

    <body>
        <header class="headerMain">
            <nav>
                <h2>NODEBT</h2>
                <ul>
                    <li><a href="groupes.php">Mes groupes</a></li>
                    <li><a href="gestionGroupes.php?instruction=create">Créer un groupe</a></li>
                    <li><a href="contact.php?isLog=true">Contact</a></li>
                    <div class="gestion-utilisateur">
                        <li class="login"><a href="editerProfil.php"><?php echo $_SESSION['firstname'] . ' ' . $_SESSION['name']?></a></li>
                        <li class="deconnexion"><a href="../index.php">Se déconnecter</a></li>
                    </div>
                </ul>
            </nav>       
        </header>