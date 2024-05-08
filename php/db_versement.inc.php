<?php
namespace Versement;
use DB\DBLink;

/**
 * Class Versement : Versement
 */
Class Versement{
    public $dateHeure;
    public $montant;
    public $estConfirme;
    public $idCrediteur;
    public $idDebiteur;
    public $idGroupe;
}

/**
 * Class Versement : Versement
 */
Class VersementManager{
    const TABLE_VERSEMENT = 'versement';

    public function addVersement($versement){
        $noError = false;
        $bdd   = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_VERSEMENT." (dateHeure, montant, estConfirme, idCrediteur, idDebiteur, idGroupe) VALUES (:dateHeure, :montant, :estConfirme, :idCrediteur, :idDebiteur,:idGroupe)");
            $stmt->bindValue(':dateHeure', $versement->dateHeure);
            $stmt->bindValue(':montant', $versement->montant);
            $stmt->bindValue(':estConfirme', $versement->estConfirme);
            $stmt->bindValue(':idCrediteur', $versement->idCrediteur);
            $stmt->bindValue(':idDebiteur', $versement->idDebiteur);
            $stmt->bindValue(':idGroupe', $versement->idGroupe);
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

    public function updateVersement($idVersement, &$message){
        $noError = false;
        $bdd   = null;
        $confirmation = 1;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("UPDATE ".self::TABLE_VERSEMENT." SET estConfirme= :confirmation WHERE idVersement= :idVersement");
            $stmt->bindValue(':confirmation', $confirmation);
            $stmt->bindValue(':idVersement', $idVersement);
            if ($stmt->execute()){
                $noError = true;
            }else{
                $message = 'Erreur lors de la confirmation';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    public function deleteVersement($idVersement, &$message){
        $noError = false;
        $bdd   = null;
        $confirmation = 1;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ".self::TABLE_VERSEMENT. " WHERE idVersement= :idVersement");
            $stmt->bindValue(':idVersement', $idVersement);
            if ($stmt->execute()){
                $noError = true;
            }else{
                $message = 'Erreur lors de la déclinaison';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $noError;
    }

    public function giveAllVersementOnIdGroupe($idGroupe){
        $bdd   = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_VERSEMENT." WHERE idGroupe= :idGroupe");
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


    public function checkIfAllZero($crediteurs, $debiteurs){
        $allCrediteurZero = false;
        $allDebiteurZero = false;

        foreach ($crediteurs as $crediteur){
            if($crediteur['montant'] == 0 || round($crediteur['montant'], 2) == 0.01 || round($crediteur['montant'], 2) == -0.01){
                $allCrediteurZero = true;
            }else {
                $allCrediteurZero = false;
                break;
            }
        }

        foreach ($debiteurs as $debiteur){
            if($debiteur['montant'] == 0 || round($debiteur['montant'], 2) == 0.01 || round($debiteur['montant'], 2) == -0.01){
                $allDebiteurZero = true;
            }else{
                $allDebiteurZero = false;
                break;
            }
        }

        if($allCrediteurZero && $allDebiteurZero){
            return true;
        }else{
            return false;
        }
    }

}