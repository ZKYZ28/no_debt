<?php
    require('../inc/headerMain.inc.php');
    require '../php/db_participer.inc.php';
    use Participer\ParticiperManager;

    $nomGroupe = $_GET['nomGroupe'];
    $idGroupe = $_GET['idGroupe'];
    $participerManager = new ParticiperManager();
    $message = '';
    $noError = false;

?>

        <main class="welcome">
            <section class="welcome-section">
                <h1><?php echo $nomGroupe?></h1>

                <?php if(isset($_POST['buttsub1'])){

                    if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                        $message .= 'Adresse email vide ou incorrecte';
                    }else{
                        $email = htmlentities($_POST['email']);
                    }

                    if(!$participerManager->checkPresence($email, $idGroupe, $message)){
                        if($participerManager->inviteSomeone($email, $idGroupe, $message)){ ?>
                            <div class="success-php">
                                <?php echo $message?>
                            </div>
                        <?php
                            header("Refresh: 1.5; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                        }else {
                            ?>
                            <div class="alert-php">
                                <?php echo $message?>
                            </div>
                            <?php
                        }
                    }else{
                        ?>
                        <div class="alert-php">
                            <?php echo $message?>
                        </div>
                        <?php
                    }
                }?>

                <form action="#" method="POST">
                    <label for="email">Adresse email de la personne à inviter : <span class="obligatoire">*</span></label><input class="general" id="email" name="email" type="email" > <br>

                    <input class="soumettreFormulaire" type="submit" name="buttsub1" value="Envoyer l'invitation">
                </form>
            </section>
        </main>
<?php require('../inc/footer.inc.php');?>


