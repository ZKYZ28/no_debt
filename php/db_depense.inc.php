<?php
 namespace Depense;
 use DB\DBLink;

 /**
  * Class Depense : gestion des dépenses du site
  */
 class Depense {
     public $dateHeure;
     public $montant;
     public $libelle;
     public $idGroupe;
     public $idUser;
 }

 /**
  * Class DepenseManager : gestion des dépenses du site
  */
 class DepenseManager{
        const TABLE_NAME = 'depense';
        const TABLE_PARTICIPER = 'participer';

        public function addDepense($depense, &$message){
            $noError = false;
            $bdd   = null;

            try {
                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (dateHeure, montant, libelle, idGroupe, idUser) VALUES (:dateHeure, :montant, :libelle, :idGroupe, :idUser)");
                $stmt->bindValue(':dateHeure', $depense->dateHeure);
                $stmt->bindValue(':montant', $depense->montant);
                $stmt->bindValue(':libelle', $depense->libelle);
                $stmt->bindValue(':idGroupe', $depense->idGroupe);
                $stmt->bindValue(':idUser', $depense->idUser);
                if ($stmt->execute()){
                    $message .= 'Depense créé avec succès !' ;
                    $noError = true;
                } else {
                    $message .= 'Erreur lors de la création de la depense.';
                }
                $stmt = null;
            } catch (Exception $e) {
                $message .= $e->getMessage().'<br>';
            }
            DBLink::disconnect($bdd);
            return $noError;
        }

     public function giveAllDepenseOfOneGroupe($idGroupe){
         $bdd = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE idGroupe= :idGroupe");
             $stmt->bindValue(':idGroupe', $idGroupe);
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

     public function giveSumDepenseOnIdUser($idUser, $idGroupe){
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT SUM(montant) FROM ".self::TABLE_NAME." WHERE idUser= :idUser AND idGroupe= :idGroupe");
             $stmt->bindValue(':idUser', $idUser);
             $stmt->bindValue(':idGroupe', $idGroupe);
             if ($stmt->execute()){
                 $result = $stmt->fetch();
                 $sum = $result['sum(montant)'];
             } else {
                 $message .= 'Erreur lors de la création de la depense.';
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $sum;
     }

     public function sumAllDepenseOfOnGroupe($idGroupe){
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT SUM(montant) FROM ".self::TABLE_NAME." WHERE idGroupe= :idGroupe");
             $stmt->bindValue(':idGroupe', $idGroupe);
             if ($stmt->execute()){
                 $result = $stmt->fetch();
                 $sum = $result['sum(montant)'];
             } else {
                 $message .= 'Erreur lors de la création de la depense.';
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $sum;
     }

     public function getIdDepense($dateHeure, $idUser){
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT idDepense FROM ".self::TABLE_NAME." WHERE dateHeure= :dateHeure AND idUser= :idUser");
             $stmt->bindValue(':dateHeure', $dateHeure);
             $stmt->bindValue(':idUser', $idUser);
             if ($stmt->execute()){
                 $result = $stmt->fetch();
                 $id = $result['iddepense'];
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $id;
     }


     public function deviationAverage($idUser, $idGroupe){
         $total = DepenseManager::sumAllDepenseOfOnGroupe($idGroupe);
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT COUNT(idUser) FROM ".self::TABLE_PARTICIPER." WHERE idGroupe= :idGroupe AND estConfirme= :estConfirme");
             $stmt->bindValue(':idGroupe', $idGroupe);
             $stmt->bindValue(':estConfirme', 1);
             if ($stmt->execute()){
                 $result = $stmt->fetch();
                 $nbrMemeber = $result['count(iduser)'];
                 if($nbrMemeber!= 0) {
                     $moy = $total / $nbrMemeber;
                     $ecart = -($moy - DepenseManager::giveSumDepenseOnIdUser($idUser, $idGroupe));
                 }else{
                     $ecart = 0;
                 }
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $ecart;
     }


     public function giveLastDepense($idGroupe){
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT montant FROM ".self::TABLE_NAME." WHERE idGroupe= :idGroupe ORDER BY idDepense limit 3");
             $stmt->bindValue(':idGroupe', $idGroupe);
             if ($stmt->execute()){
                 $lastDepense = $stmt->fetchAll();
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $lastDepense;
     }


     public function findDepenseOnParam($idGroupe, $dateDebut, $dateFin, $montantMin, $montantMax, $libelle, $tag, &$message){
         $bdd = null;
         $result = null;
         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." de JOIN caracteriser cart ON cart.idDepense = de.idDepense JOIN tag t ON t.idTag = cart.idTag WHERE dateHeure BETWEEN :dateDebut AND :dateFin AND montant BETWEEN :montantMin AND :montantMax AND libelle= :libelle AND t.tag= :tag AND de.idGroupe= :idGroupe");
             $stmt->bindValue(':idGroupe', $idGroupe);
             $stmt->bindValue(':dateDebut', $dateDebut);
             $stmt->bindValue(':dateFin', $dateFin);
             $stmt->bindValue(':montantMin', $montantMin);
             $stmt->bindValue(':montantMax', $montantMax);
             $stmt->bindValue(':libelle', $libelle);
             $stmt->bindValue(':tag', $tag);
             if ($stmt->execute()){
                 $result = $stmt->fetchAll();
                 if(empty($result)){
                     $message = 'Auncune dépense ne correspond à votre recherche';
                 }
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $result;
     }

     public function findDepenseWithTagOrLibelle($idGroupe, $toFind, &$message){
         $bdd = null;
         $result = null;
         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." de JOIN caracteriser cart ON cart.idDepense = de.idDepense JOIN tag t ON t.idTag = cart.idTag WHERE libelle= :toFind OR t.tag= :toFind AND de.idGroupe= :idGroupe");
             $stmt->bindValue(':idGroupe', $idGroupe);
             $stmt->bindValue(':toFind', $toFind);
             if ($stmt->execute()){
                 $result = $stmt->fetchAll();
                 if(empty($result)){
                     $message = 'Auncune dépense ne correspond à votre recherche';
                 }
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $result;
     }

     public function editDepense($idDepense, $depense, &$message){
         $change = false;
         if(DepenseManager::deleteCaract($idDepense)){
             try{
                 $bdd  = DBLink::connect2db(MYDB, $message);
                 $stmt = $bdd->prepare("UPDATE ".self::TABLE_NAME." SET dateHeure= :dateHeure, montant= :montant, libelle= :libelle, idGroupe= :idGroupe, idUser= :idUser WHERE idDepense= :idDepense");
                 $stmt->bindValue(':dateHeure', $depense->dateHeure);
                 $stmt->bindValue(':montant', $depense->montant);
                 $stmt->bindValue(':libelle', $depense->libelle);
                 $stmt->bindValue(':idGroupe', $depense->idGroupe);
                 $stmt->bindValue(':idUser', $depense->idUser);
                 $stmt->bindValue(':idDepense', $idDepense);
                 if ($stmt->execute()){
                     $change = true;
                     $message .= 'Modification de la dépense effectuée.<br>';
                 }else{
                     $message .= 'Erreur lors de la modification de la dépense<br>';
                 }
             }
             catch (Exception $e) {
                 $message .= $e->getMessage().'<br>';
             }
             DBLink::disconnect($bdd);
             return $change;
         }
     }

     public function giveDepenseOnId($idDepense, &$message){
         $bdd = null;
         $result = null;
         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE idDepense= :idDepense");
             $stmt->bindValue(':idDepense', $idDepense);
             if ($stmt->execute()){
                 $result = $stmt->fetch();
                 if(empty($result)){
                     $message = 'Erreur lors de la récupération des inforamtions';
                 }
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $result;
     }

     public function deleteDepesne($idDepense, &$message){
         $bdd = null;
         $isDelete = false;

         if(DepenseManager::deleteCaract($idDepense) && DepenseManager::deleteFac($idDepense)){
             try {
                 $bdd  = DBLink::connect2db(MYDB, $message);
                 $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_NAME." WHERE idDepense= :idDepense");
                 $stmt->bindValue(':idDepense', $idDepense);
                 if ($stmt->execute()){
                     $message .= 'Dépense supprimée avec succès.';
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

     function deleteCaract($idDepense){
         $bdd = null;
         $delete = false;
         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("DELETE FROM caracteriser WHERE idDepense= :idDepense");
             $stmt->bindValue(':idDepense', $idDepense);
             if ($stmt->execute()){
                 $delete = true;
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $delete;
     }

     function deleteFac($idDepense){
         $bdd = null;
         $delete = false;
         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("DELETE FROM facture WHERE idDepense= :idDepense");
             $stmt->bindValue(':idDepense', $idDepense);
             if ($stmt->execute()){
                 $delete = true;
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $delete;
     }

     public function giveAllSumDepenseForOneGroup($idGroupe){
         $bdd   = null;

         try {
             $bdd  = DBLink::connect2db(MYDB, $message);
             $stmt = $bdd->prepare("SELECT idDepense FROM ".self::TABLE_NAME." WHERE idGroupe= :idGroupe GROUP BY idUser");
             $stmt->bindValue(':idGroupe', $idGroupe);
             if ($stmt->execute()){
                 $result = $stmt->fetchAll();
             } else {
                 $message .= 'Erreur lors de la création de la depense.';
             }
             $stmt = null;
         } catch (Exception $e) {
             $message .= $e->getMessage().'<br>';
         }
         DBLink::disconnect($bdd);
         return $result;
     }
 }
