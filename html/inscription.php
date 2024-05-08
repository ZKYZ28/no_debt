<?php
    require '../php/db_user.inc.php';
    use User\UserRepository as UserRepository;
    use User\User;

    /*LINK*/
    $cssLink = '../css/styles.css';
    $indexLink = '../index.php';
    $inscriptionLink = 'inscription.php';
    $contactLink = 'contact.php';
    require '../inc/header.inc.php';

    $message = '<ul>';
    $messageEnd = '</ul>';
    $noError = true;
    $exist = false;
    $userRepository = new UserRepository();

?>
    <main class="welcome">
        <section class="welcome-section">
        <h1>S'inscrire : </h1>

            <?php if (isset($_POST['buttInscription'])) {
                $user = new User();

                if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                    $message .= 'Adresse email vide ou incorrecte';
                }else{
                    $user->email = htmlentities($_POST['email']);
                }

                if(empty($_POST['confirmPassword'])){
                    $message .= 'Confirmer mot de passe vide';
                }else{
                    $confirmPassword = htmlentities($_POST['confirmPassword']);
                }

                if(empty($_POST['password'])){
                    $message .= 'Mot de passe vide';
                }else{
                    $user->password = htmlentities($_POST['password']);
                }

                if(empty($_POST['name'])){
                    $message .= 'Nom vide';
                }else{
                    $user->name = htmlentities($_POST['name']);
                }

                if(empty($_POST['firstName'])){
                    $message .= 'Prénom vide';
                }else{
                    $user->firstName = htmlentities($_POST['firstName']);
                }

                $noError =  $userRepository->isValid($user->password, $confirmPassword, $message);
                $exist = $userRepository->existsInDB($user->email, $message);
                $valid = $userRepository->isValidContent($user->name, $user->firstName, $message);

                if($noError && !$exist && $valid){
                $user->password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $noError = $userRepository->storeMember($user, $message);

                session_start();
                $_SESSION['isLog'] = true;
                $_SESSION['email'] = $user->email;
                $_SESSION['name'] = $user->name;
                $_SESSION['firstname'] =  $user->firstName;
                $_SESSION['id'] = $userRepository->getIdOnEmail($user->email);

                header("Location: groupes.php");
                }else{
                ?>
                <div class="alert-php">
                    <?php echo$message?>
                </div>
                <?php
                }
            }
            ?>

        <form action="#" method="POST">
                    <label for="email">Adresse email : <span class="obligatoire">*</span></label><input class="general" id="email" name="email" type="email" required> <br>

                    <label for="password">Mot de passe : <span class="obligatoire">*</span></label><input class="general" id="password" name="password" type="password" required><br>

                    <label for="confirmPassword">Confirmer mot de passe : <span class="obligatoire">*</span></label><input class="general" id="confirmPassword" name="confirmPassword" type="password" required><br>

                    <label for="name">Nom : <span class="obligatoire">*</span></label><input class="general" id="name" name="name" type="text" required><br>

                    <label for="firstName">Prénom : <span class="obligatoire">*</span></label><input class="general" id="firstName" name="firstName" type="text" required><br>

                    <input class="soumettreFormulaire" type="submit" name="buttInscription" value="S'inscrire">
                </form> 
            </section>
        </main>
<?php include('../inc/footer.inc.php');?>
