<?php
    require '../php/db_user.inc.php';

    use User\UserRepository as UserRepository;

    /*LINK*/
    $message = '';
    $cssLink = '../css/styles.css';
    $indexLink = '../index.php';
    $inscriptionLink = 'inscription.php';
    $contactLink = 'contact.php';
    if($_GET['isLog'] == 'true'){
        require('../inc/headerMain.inc.php');
    }else{
        require('../inc/header.inc.php');
    }



    $userRepository = new UserRepository();
    $error = '';

?>
        
                <main class="welcome">
                        <section class="welcome-section">    
                                <h1>Contact : </h1>

                            <?php if (isset($_POST['buttContact'])){

                                if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                                    $message .= 'Adresse email vide ou incorrecte';
                                }else{
                                    $email = htmlentities($_POST['email']);
                                }

                                if(empty($_POST['subject'])){
                                    $message .= 'Sujet vide';
                                }else{
                                    $subject = htmlentities($_POST['subject']);
                                }

                                if(empty($_POST['message'])){
                                    $message .= 'Message vide';
                                }else{
                                    $message = htmlentities($_POST['message']);
                                }


                                if(!($userRepository->contactAdmin($email, $subject, $message, $message))){
                                    ?>
                                    <div class="success-php">
                                        <?php echo $message?>
                                    </div>
                                }<?php
                                    if($_GET['isLog'] == 'true'){
                                        header("Refresh: 1.5; groupes.php?");
                                    }
                                }
                            }?>
                                <form action="#" method="POST">
                                        <label for="email">Adresse email : <span class="obligatoire">*</span></label><input class="general" id="email" name="email" type="email" required value="<?php if($_GET['isLog'] == 'true') echo $_SESSION['email']?>"><br>
                                        <label for="subject">Sujet : <span class="obligatoire">*</span></label><input class="general" id="subject" name="subject" type="text"  placeholder="Sujet" required><br>
                                        <label for="message">Message : <span class="obligatoire">*</span></label><textarea class="general" id="message" name="message" rows="10" cols="50" placeholder="" required></textarea> <br>

                                        <input class="soumettreFormulaire" type="submit" name="buttContact" value="Envoyer">
                                </form>
                        </section>
                </main>
<?php require('../inc/footer.inc.php');?>