<?php
    namespace Participer;
    require 'db_user.inc.php';
    use User\UserRepository;
    use DB\DBLink;


/**
 * Class Participer : Gestion Participer
 */
class Participer {
        public $idUser;
        public $idGroupe;
}


/**
* Class Participer : Gestion Participer
*/
class ParticiperManager{
    const TABLE_NAME = 'participer';

    /** Ajoute une ligne dans la table participer
     * @param $idUser l'id de l'utilisateur
     * @param $idGroupe l'id du groupe auquel l'utilisateur participe
     * @return bool True si tout c'est bien passé, sinon false
     */
    function addParticiperFounder($participer){
        $noError = false;
        $bdd   = null;
        $estConfirme = true;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (idUser, idGroupe, estConfirme) VALUES (:idUser, :idGroupe, :estConfirme)");
            $stmt->bindValue(':idUser', $participer->idUser);
            $stmt->bindValue(':idGroupe', $participer->idGroupe);
            $stmt->bindValue(':estConfirme', $estConfirme);
            if ($stmt->execute()){
                $noError = true;
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    /**Invite quelqu'un dans un groupe sur base de son email
     * @param $email l'email de la personne à inviter
     * @param $idGroupe l'id du groupe dans lequel la personne est invitée
     * @param $message un message contenant l'ensemble des informaitons à communiquer à l'utilisateur
     * @return bool un boolean qui indique si il y a eu des erreurs ou pas
     */
    public function inviteSomeone($email, $idGroupe, &$message){
        $bdd = null;
        $noError = false;
        $userRepository = new UserRepository();
        $estConfirme = 0;

        if($userRepository->existsInDB($email, $message)){
            $idUser = $userRepository->getIdOnEmail($email);
        }else{
            $message = 'Cette email n\'est associée a aucun utilisateur';
            return ;
        }
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (idUser, idGroupe, estConfirme) VALUES (:idUser, :idGroupe, :estConfirme)");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':idGroupe', $idGroupe);
            $stmt->bindValue(':estConfirme', $estConfirme);
            if ($stmt->execute()){
                $noError = true;
                $message = 'Invitation correctement envoyée';
            }else{
                $message = 'Erreur lors de l\'envoie de l\'email';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    /** Permet à un utilisateur d'accepter une invitation dans un groupe
     * @param $idUser l'id de l'utilisateur
     * @param $idGroupe l'id du groupe dans lequel il est invité
     * @param $message l'ensemble des messages à retourner à l'utilisateur
     * @return void
     */
    public function acceptInvitation($idUser , $idGroupe, &$message){
        $bdd = null;
        $noError = false;
        $estConfirme = 1;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE participer SET estConfirme= :estConfirme WHERE idUSer= :idUser AND idGroupe= :idGroupe");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':idGroupe', $idGroupe);
            $stmt->bindValue(':estConfirme', $estConfirme);
            if ($stmt->execute()){
                $noError = true;
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    /** Permet à un utilisateur de refuser une invitation dans un groupe
     * @param $idUser l'id de l'utilisateur
     * @param $idGroupe l'id du groupe dans lequel il est invité
     * @param $message l'ensemble des messages à retourner à l'utilisateur
     * @return void
     */
    public function declineInvitation($idUser , $idGroupe, &$message){
        $bdd = null;
        $noError = false;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME. " WHERE idUser= :idUser AND idGroupe= :idGroupe");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':idGroupe', $idGroupe);
            if ($stmt->execute()){
                $noError = true;
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    public function checkPresence($email, $idGroupe, &$message){
        $bdd = null;
        $presence = false;
        $user = new UserRepository();
        $idUser = $user->getIdOnEmail($email);

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE idUser= :idUser AND idGroupe= :idGroupe");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':idGroupe', $idGroupe);
            if ($stmt->execute()){
                if($stmt->fetch()){
                    $presence = true;
                    $message = 'Cette personne est déjà dans le groupe ou à déjà reçue une invitation';
                }
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $presence;
    }

}