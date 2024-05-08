<?php
namespace User;
    require 'db_link.inc.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/Exception.php';

    use DB\DBLink;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;


/**
 * Class User : gestion des utilisateurs du site
 */
class User {
    public $email;
    public $password;
    public $name;
    public $firstName;

    public function __get($prop){
        return $this->$prop;
    }
}

/**
 * Class UserRepository : gestion des utilisateurs du site
 */
class UserRepository{
    const TABLE_NAME = 'users';

    /**
     * Vérifie si une adresse email existe déjà en BD
     * @var string $email adresse email à vérifier
     * @var string $message ensemble des messages à retourner à l'utilisateur, séparés par un saut de ligne
     * @return boolean true si adresse existante, false sinon
     */
    public function existsInDB($email, &$message){
        $result = false;
        $bdd    = null;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE email = :email");
            $stmt->bindValue(':email', $email);
            if ($stmt->execute()){
                if($stmt->fetch() !== false){
                    $result = true;
                    $message .= ' <li> Un compte lié à cet email existe déjà. </li>';
                }
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $result;
    }

    /**
     * Enregistre le membre en base de données, l'email ne doit pas exister en base de données
     * @var Member $member le membre à ajouter
     * @var string $message ensemble des messages à retourner à l'utilisateur, séparés par un saut de ligne
     * @return boolean true si opération réalisée sans erreur, false sinon
     */
    public function storeMember($user, &$message){
        $noError = false;
        $bdd   = null;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (email, password, name, firstName) VALUES (:email, :password, :name, :firstName)");
            $stmt->bindValue(':email', $user->email);
            $stmt->bindValue(':password', $user->password);
            $stmt->bindValue(':name', $user->name);
            $stmt->bindValue(':firstName', $user->firstName);
            if ($stmt->execute()){
                $message .= " <li> Compte créé avec succès !</li>>" ;
                $noError = true;
            } else {
                $message .= " <li> Erreur lors de la création du compte.</li>";
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }


    /** Permet à un utilisateur de se connecter si il a un compte créé avec l'email et le mdp utilisé
     * @param $email l'email de l'utilisateur
     * @param $password le mdp de l'utilisateur
     * @param $message  les messages à renvoyer à l'utilisateur
     * @return bool Return true si il peut se log, sinon false
     */
    public function logUser($email, $password, &$message){
        $noError = false;
        $bdd   = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE email = :email");
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $userInfo = $stmt->fetch();

            if($userInfo !== false){
                if(password_verify($password, $userInfo['password']) && $this->existsInDB($email, $message)){
                    $noError = true;
                }else{
                    $message = 'Adresse email ou mot de passe incorrecte. Veuillez réessayer';
                }
            }else{
                $message = 'Il semblerait ne pas y avoir de compte lié à cette adresse email';
            }
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    /**
     * Vérifie que les deux MDP sont identitques
     * @var string $password Le MDP de base
     * @var string $confirmPassword Encodage de confirmation du MDP
     * @var string $message ensemble des messages à retourner à l'utilisateur, séparés par un saut de ligne
     * @return boolean true si le MDP valide, false sinon
     */
   public function isValid($password, $confirmPassword,  &$message) {
       $valid = true;
      if($password != $confirmPassword) {
            $message .= ' <li> Les deux mots de passe doivent être identiques.</li>';
            $valid = false;
        }
      return $valid;
   }


    /** Vérifie le nom et prénom de l'utilisateur
     * @param $name son nom
     * @param $firstName son prénom
     * @param $message le message d'erreur éventuel
     * @return bool true si le nom et prénom sont valides, false sinon
     */
       public function isValidContent($name, $firstName,  &$message) {
           $valid = true;
           $firstName = trim($firstName);
           $name = trim($name);

           if(!preg_match('/^[a-zA-Z ]+$/', $name)){
               $valid = false;
               var_dump($name);
               $message .= '<li> Nom incorrect </li>';
           }

           if(!preg_match('/^[a-zA-Z-ç]+$/', $firstName)){
               $valid = false;
               $message .= '<li> Prénom incorrect </li>';
           }

           return $valid;
       }

    /** Envoie un email à l'administrateur
     * @param $email l'email de la personne qui envoie le mail
     * @param $subject le sujet du mail
     * @param $content le contenu du mail
     * @param $adminEmail l'email de l'administrateur qui va recevoir le mail
     * @param $message /
     * @return bool send True si le mail a été envoyé, sinon false
     */
    public function contactAdmin($email, $subject, $message, &$error) {
        $send = false;
        $mail = new PHPMailer(true);

        $mail->CharSet = 'UTF-8';
        $mail->setFrom($email);
        $mail->addAddress('francois.mahy28@gmail.com');
        $mail->isHTML(false);
        $mail->Subject = $subject;
        $mail->Body = $message;
        $mail->addCC($email);

        try {
            if(UserRepository::existsInDB($email, $m)){
                if($mail->send()){
                    ?>
                    <div class="success-php">
                        <?php echo "Email envoyé avec succès à francois.mahy28@gmail.com"?>
                    </div>
                    <?php
                    $send = true;
                }else{
                    ?>
                    <div class="alert-php">
                        <?php echo 'Impossible de contacter l\'administrateur, réessayer plus tard';?>
                    </div>
                    <?php
                }
            }else{
                ?>
                <div class="alert-php">
                    <?php echo 'Aucun compte lié à cette adresse email';?>
                </div>
                <?php
            }
        }catch (Exception $e) {
                $error .= $e->getMessage().'<br>';
            }
        return $send;
    }

    /**
     * @param $email l'email de la personne a qui envoyé le mot de passe
     * @param $newPassword le nouveau mot de passe a envoyé
     * @return bool true si le mail est bien envoyé, sinon false
     * @throws Exception
     */
    function sendNewPassword($email, $newPassword, &$message){
        $send = false;
        $mail = new PHPMailer(true);

        $mail->CharSet = 'UTF-8';
        $mail->setFrom('francois.mahy28@gmail.com');
        $mail->addAddress($email);
        $mail->isHTML(false);
        $mail->Subject = 'Reset password';
        $mail->Body = "Votre nouveau mot de passe est $newPassword";

        try {
            if($mail->send()){
                   $message = "Email envoyé avec succès à $email";
                    $send = true;
            }else{
                    $message =  'Echec lors de l\'envoie de l\'email.';
            }

        }catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        return $send;
    }

    /** Reset son mot de passe, le nouveau sera envoyé par email
     * @param $email l'email de la personne qui veut reset son mot de passe
     * @param $message Liste des messages à retourner à l'utilisateur
     * @return bool True si le mot de passe a été changé, sinon false
     */
    public function restPassword($email, &$message) {
        $change = false;
        try{
                $newPassword = UserRepository::generatePassword();
                $encryptPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME." SET password= :encryptPassword WHERE email= :email");
                $stmt->bindValue(':email', $email);
                $stmt->bindValue(':encryptPassword', $encryptPassword);
                if ($stmt->execute()){
                        $change = true;
                        UserRepository::sendNewPassword($email, $newPassword, $message);
                }else{
                    $message .= 'Erreur lors de la modification du mot de passe <br>';
                }
            }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $change;
    }

    /**Génere un mot de passe de 10 caractère composé de lettres et de chiffres
     * @return string un mot de passe composé de 10 caractères
     */
    function generatePassword(){
        $alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $password = array();
        $lenght = strlen($alpha) -1;

        for ($i = 0 ; $i < 10; $i++){
            $car = rand(0, $lenght);
            $password[$i] = $alpha[$car];
        }
        return implode($password);
    }

    /**
     * @param $name
     * @param $firstName
     * @param $email
     * @param $password
     * @param $confirmPassword
     * @param $message
     * @return void
     */
    public function editProfile($name, $firstName, $email, &$message){
        $succes = false;
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME." SET name= :name, firstName= :firstName WHERE email= :email");
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':firstName', $firstName);
            if ($stmt->execute()){
                $message = 'Vos informations ont correctement été mises à jour.';
                $succes = true;
            }else{
                $message = 'Echec lors de la modification de vos inforamtions.';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $succes;
    }


    public function getNameOnEmail($email){
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT name FROM ".self::TABLE_NAME." WHERE email= :email");
            $stmt->bindValue(':email', $email);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $name = $result['name'];
            }else{
                $name = 'ERROR';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $name;
    }


    public function getFirstNameOnEmail($email){
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT firstName FROM ".self::TABLE_NAME." WHERE email= :email");
            $stmt->bindValue(':email', $email);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                var_dump($result);
                $firstName = $result['firstname'];
            }else{
                $firstName = 'ERROR';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $firstName;
    }

    public function getFirstNameOnId($id){
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT firstName FROM ".self::TABLE_NAME." WHERE id= :id");
            $stmt->bindValue(':id', $id);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $firstName = $result['firstname'];
            }else{
                $firstName = 'ERROR';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $firstName;
    }

    public function getIdOnEmail($email){
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT id FROM ".self::TABLE_NAME." WHERE email= :email");
            $stmt->bindValue(':email', $email);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $id = $result['id'];
            }else{
                $id = '0';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $id;
    }

    public function getIdOnFirstName($firstname){
        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT id FROM ".self::TABLE_NAME." WHERE firstname= :firstname");
            $stmt->bindValue(':firstname', $firstname);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $id = $result['id'];
            }else{
                $firstName = '0';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $id;
    }
}


