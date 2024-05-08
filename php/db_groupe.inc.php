<?php
    namespace Groupe;
    require 'db_depense.inc.php';
    require 'db_participer.inc.php';

    use Depense\DepenseManager;
    use Participer\Participer;
    use Participer\ParticiperManager;
    use DB\DBLink;

/**
 * Class Groupe : gestion des groupes du site
 */
class Groupe {
    public $nom;
    public $devise;
    public $idFounder;
}


/**
 * Class GroupeManager : gestion des groupes du site
 */
class GroupeManager{
    const TABLE_NAME = 'groupe';
    const TABLE_PARTICIPER = 'participer';
    const TABLE_USERS = 'users';
    const TABLE_VERSEMENT = 'versement';
    const TABLE_TAG = 'tag';

    /**
     * Enregistre le groupe en base de données
     * @var Groupe $groupe le groupe à ajouter
     * @var string $message ensemble des messages à retourner à l'utilisateur, séparés par un saut de ligne
     * @return boolean true si opération réalisée sans erreur, false sinon
     */
    public function storeGroupe($groupe, &$message){
        $noError = false;
        $bdd   = null;
        $pariciper = new Participer();
        $participerManager = new ParticiperManager();

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (nom, devise, idFounder) VALUES (:nom, :devise, :idFounder)");
            $stmt->bindValue(':nom', $groupe->nom);
            $stmt->bindValue(':devise', $groupe->devise);
            $stmt->bindValue(':idFounder', $groupe->idFounder);
            if ($stmt->execute()){
                $message .= "Groupe créé avec succès !" ;
                $noError = true;

                $groupeId = GroupeManager::findGroupeIdOnIdFounderEndName($groupe->idFounder, $groupe->nom);

                $pariciper->idGroupe = $groupeId;
                $pariciper->idUser = $groupe->idFounder;

                $participerManager->addParticiperFounder($pariciper);
            } else {
                $message .= "Erreur lors de la création du groupe.";
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    /** Trouve l'id d'un groupe sur base de ID de son fondateur et de son nom
     * @param $idFounder l'id du fondateur
     * @param $nom le nom du groupe
     * @return $idGroupe l'id du groupe
     */
    function findGroupeIdOnIdFounderEndName($idFounder, $nom){
        $idGroupe = null;
        $bdd = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT id FROM ".self::TABLE_NAME." WHERE idFounder= :idFounder AND nom= :nom");
            $stmt->bindValue(':idFounder', $idFounder);
            $stmt->bindValue(':nom', $nom);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $idGroupe = $result['id'];
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $idGroupe;
    }

    /**
     * Donne tous les groupes d'un utilisateur en spécifiant son ID
     * @param $idUser l'id de l'utilisateur
     * @return $result un tbaleau contenant les infos des groupes dans lesquels l'utilisateur est.
     */
    public function giveAllGroupsOfSomeone($idUser){
        $bdd = null;
        $estConfirme = true;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." gr JOIN participer part ON part.idGroupe = gr.id WHERE part.idUser= :idUser AND part.estConfirme= :estConfirme");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':estConfirme', $estConfirme);
            if ($stmt->execute()){
                $result = $stmt->fetchAll();
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $result;
    }

    /** Donne le nom et le prénom d'un fondateur d'un groupe sur base de son ID
     * @param $idFounder l'id du fondateur du groupe
     * @return string une chaîne composée du nom et du prénom du fondateur.
     */
    public function giveFounderName($idFounder){
        $bdd = null;
        $founder = '';

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT firstName, name FROM ".self::TABLE_USERS." us JOIN groupe gr ON gr.idFounder = us.id WHERE gr.idFounder= :idFounder");
            $stmt->bindValue(':idFounder', $idFounder);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                    $founder = $result['firstname'] . ' ' .  $result['name'];
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $founder;
    }

    /** Donne toutes les invitations d'un utilisateur sur base de son id
     * @param $idUser l'id de l'utilisateur
     * @return array un tableau contenant toutes les invitations de la personne.
     */
    public function giveAllInvitationOfSomeone($idUser){
        $bdd = null;
        $estConfirme = 0;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." gr JOIN participer part ON part.idGroupe = gr.id WHERE part.idUser= :idUser AND part.estConfirme= :estConfirme");
            $stmt->bindValue(':idUser', $idUser);
            $stmt->bindValue(':estConfirme', $estConfirme);
            if ($stmt->execute()){
                $result = $stmt->fetchAll();
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $result;
    }


    /** Récupère tous les membres d'un groupe sur base de son ID
     * @param $idGroupe l'id du groupe du quel on veut récupérer tous les memebres
     * @return array un tableau qui contient les id de tous les membres
     */
    public function giveAllMemberOfOneGroup($idGroupe){
        $bdd = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT pr.idUser FROM ".self::TABLE_PARTICIPER." pr JOIN groupe gr ON gr.id = pr.idGroupe WHERE gr.id= :idGroupe AND pr.estConfirme= :estConfirme");
            $stmt->bindValue(':idGroupe', $idGroupe);
            $stmt->bindValue(':estConfirme', 1);
            if ($stmt->execute()){
                $result = $stmt->fetchAll();
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $result;
    }


    public function editGroupe($id, $nom, $devise, &$message){
        $edit = false;

        try{
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE groupe SET nom= :nom, devise= :devise WHERE id= :id");
            $stmt->bindValue(':nom', $nom);
            $stmt->bindValue(':devise', $devise);
            $stmt->bindValue(':id', $id);
            if ($stmt->execute()){
                $edit = true;
                $message .= 'Modification du groupe réussie';
            }else{
                $message .= 'Erreur lors de la modification du groupe <br>';
            }
        }
        catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $edit;
    }

    public function deleteGroupe($idGroupe){
        $bdd = null;
        $isDelete = false;

        if(GroupeManager::deleteParticiper($idGroupe) && GroupeManager::deleteVersement($idGroupe) && GroupeManager::deleteTag($idGroupe)){
            try {
                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME." WHERE id= :id");
                $stmt->bindValue(':id', $idGroupe);
                if ($stmt->execute()){
                    $isDelete = true;
                }
                $stmt = null;
            } catch (Exception $e) {
                $message .= $e->getMessage().'<br>';
            }
            DBLink::disconnect($bdd);
            return $isDelete;
        }
    }

    public function deleteParticiper($idGroupe){
        $bdd = null;
        $isDelete = false;
            try {
                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_PARTICIPER." WHERE idGroupe= :idGroupe");
                $stmt->bindValue(':idGroupe', $idGroupe);
                if ($stmt->execute()){
                    $isDelete = true;
                }
                $stmt = null;
            } catch (Exception $e) {
                $message .= $e->getMessage().'<br>';
            }
            DBLink::disconnect($bdd);
            return $isDelete;
        }

    public function deleteVersement($idGroupe){
        $bdd = null;
        $isDelete = false;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_VERSEMENT." WHERE idGroupe= :idGroupe");
            $stmt->bindValue(':idGroupe', $idGroupe);
            if ($stmt->execute()){
                $isDelete = true;
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $isDelete;
    }

    public function deleteTag($idGroupe){
        $bdd = null;
        $isDelete = false;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_TAG." WHERE idGroupe= :idGroupe");
            $stmt->bindValue(':idGroupe', $idGroupe);
            if ($stmt->execute()){
                $isDelete = true;
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $isDelete;
    }
}