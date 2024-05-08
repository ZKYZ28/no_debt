<?php
    require('../inc/headerMain.inc.php');
    require '../php/db_user.inc.php';
    use User\UserRepository as UserRepository;
    use User\User;

    $userRepository = new UserRepository();
    $message = '';

?>

        <main class="welcome">
            <section class="welcome-section"> 
                <h1 class="h1Second">Editer le profil : </h1>

                <?php if (isset($_POST['buttEditerProfil'])){
                    $user = new User();

                    if(!empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                        $user->email = htmlentities($_POST['email']);
                    }

                    if(!empty($_POST['confirmPassword'])){
                        $confirmPassword = htmlentities($_POST['confirmPassword']);
                    }

                    if(!empty($_POST['password'])){
                        $user->password = htmlentities($_POST['password']);
                    }

                    if(!empty($_POST['name'])){
                        $user->name = htmlentities($_POST['name']);
                    }

                    if(!empty($_POST['firstName'])){
                        $user->firstName = htmlentities($_POST['firstName']);
                    }


                    $noError =  $userRepository->isValid($user->password, $confirmPassword, $message);
                    $valid = $userRepository->isValidContent($user->name, $user->firstName, $message);
                    $exist = $userRepository->existsInDB($user->email, $message);

                    if($noError && $valid && $exist){
                        $succes = $userRepository->editProfile($user->name, $user->firstName, $user->email, $message);
                        ?>
                        <div class="success-php">
                            <?php echo $message?>
                            <?php echo "MARCHE" ?>
                        </div>
                        <?php
                    }else{
                        ?>
                        <div class="alert-php">
                            <?php echo $message?>
                        </div>
                        <?php
                    }
                }?>

                <form action="#" method="POST">
                    <label for="name">Nom : <span class="obligatoire">*</span></label> <input class="general" id="name" name="name" type="text" value="<?php echo $_SESSION['name']?>"><br>
                    <label for="firstName">Prénom : <span class="obligatoire">*</span></label> <input class="general" id="firstName" name="firstName" type="text" value="<?php echo $_SESSION['firstname']?>"><br>
                    <label for="email">Adresse email : <span class="obligatoire">*</span></label><input class="general" id="email" name="email" type="email" value="<?php echo $_SESSION['email']?>"> <br>
                    <label for="password">Mot de passe : <span class="obligatoire">*</span></label><input class="general" id="password" name="password" type="password" required><br>
                    <label for="confirmPassword">Confirmer mot de passe : <span class="obligatoire">*</span></label><input class="general" id="confirmPassword" name="confirmPassword" type="password" required><br>

                    <input class="soumettreFormulaire" type="submit" name="buttEditerProfil" value="Valide les modifications">
                </form>
            </section>
        </main>  
<?php require('../inc/footer.inc.php');?>
