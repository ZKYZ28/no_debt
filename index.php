<?php
    require 'php/db_user.inc.php';
    use User\UserRepository as UserRepository;
    use User\User;

    /*LINK*/
    $cssLink = 'css/styles.css';
    $indexLink = 'index.php';
    $inscriptionLink = 'html/inscription.php';
    $contactLink = 'html/contact.php';
    require 'inc/header.inc.php';

    $message = '<ul>';
    $messageEnd = '</ul>';
    $noError = true;
    $userRepository = new UserRepository();

    session_start();
    $_SESSION['isLog'] = false;

?>
        <main>
            <section>
                <h1>Me connecter</h1>

                <?php if (isset($_POST['buttsub1'])){
                    if(empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
                        $message .= 'Adresse email vide ou incorrecte';
                    }else{
                        $email = htmlentities($_POST['email']);
                    }

                    if(empty($_POST['password'])){
                        $message .= 'Mot de passe vide';
                    } else{
                        $password = htmlentities($_POST['password']);
                    }

                    if($userRepository->logUser($email, $password, $message)){
                        $_SESSION['isLog'] = true;
                        $_SESSION['email'] = $email;
                        $_SESSION['name'] = $userRepository->getNameOnEmail($email);
                        $_SESSION['firstname'] = $userRepository->getFirstNameOnEmail($email);
                        $_SESSION['id'] = $userRepository->getIdOnEmail($email);


                        header("Location: html/groupes.php");
                    }else{
                        ?>
                        <div class="alert-php">
                            <?php echo $message?>
                        </div>
                        <?php
                    }
                }?>

                <form action="#" method="POST">
                    <label for="email">Adresse email <span class="obligatoire">*</span></label><input  id="email" name="email" type="email" required placeholder="exemple@gmail.com"><br>

                    <label for="password">Mot de passe <a href="html/motDePasseOublie.php">(Mot de passe oublié ?)</a><span class="obligatoire">*</span></label><input  id="password" name="password" type="password" required > <br>

                    <input class="soumettreFormulaire" type="submit" name="buttsub1" value="Se connecter">
                </form>
            </section>

            <footer class="footerMain">
                <ul>
                    <li>NoDebt 2021-2022 ©</li>
                </ul>
            </footer> 
        </main>
    </body>
</html>