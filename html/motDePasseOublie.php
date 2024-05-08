<?php
    require '../php/db_user.inc.php';
    use User\UserRepository as UserRepository;

    /*LINK*/
    $cssLink = '../css/styles.css';
    $indexLink = '../index.php';
    $inscriptionLink = 'inscription.php';
    $contactLink = 'contact.php';
    require '../inc/header.inc.php';

    $message = '';
    $exist= false;
    $userRepository = new UserRepository();

?>
      <?php
      if(isset($_POST['forgetPassword'])){

          if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
              $message .= 'Adresse email vide ou incorrecte';
          }else{
              $email = htmlentities($_POST['email']);
          }

        $exist = $userRepository->existsInDB($email, $message);

        if($exist){
            if($userRepository->restPassword($email,$message)){?>
            <div class="success-php">
                <?php echo $message ?>
            </div>
           <?php
            }

        }else{?>
            <div class="alert-php">
            <?php echo 'Aucun compte lié à cette adresse email' ?>
            </div>
        <?php
        }
    }?>

        <main class="welcome">
            <section class="welcome-section">
                <h1>Récupération du mot de passe</h1>
                <form action="#" method="POST">
                        <label for="email">Adresse email : <span class="obligatoire">*</span></label><input class="general" id="email" name="email" type="email" required placeholder="exemple@gmail.com"> <br>
                
                        <input class="soumettreFormulaire" type="submit" name="forgetPassword" value="Envoyer un email">
                </form>
            </section>
        </main>    
<?php require('../inc/footer.inc.php');?>
