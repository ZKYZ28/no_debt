<?php
namespace Facture;
require 'db_link.inc.php';
use DB\DBLink;

/**
 * Class Facture : gestion des factures du site
 */
class Facture{
    public $scan;
    public $idDepense;
}

/**
 * Class FactureManager : gestions des factures du site
 */
class FactureManager{
    const TABLE_NAME = 'facture';

    public function addFacture($facture, &$message){
        $noError = false;
        $bdd   = null;
        $last_id = -1;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (scan, idDepense) VALUES (:scan, :idDepense)");
            $stmt->bindValue(':scan', $facture->scan);
            $stmt->bindValue(':idDepense', $facture->idDepense);
            if ($stmt->execute()){
                $message .= 'Facture ajoutée avec succès !' ;
                $noError = true;
                $last_id = $bdd->lastInsertId();
            } else {
                $message .= 'Erreur lors de l\'ajout de la facture .';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $last_id;
    }

    public function giveAllFactureOnIdDepense($idDepense, &$message){
        $bdd   = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE idDepense= :idDepense");
            $stmt->bindValue(':idDepense', $idDepense);
            if ($stmt->execute()){
                $result = $stmt->fetchAll();
                if(empty($result)){
                    $message .= 'Erreur lors de la récupération des factures';
                }
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $result;
    }

    public function deleteFacture($idFacture, &$message){
        $bdd   = null;
        $isDelete = false;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("DELETE FROM ". self::TABLE_NAME." WHERE idFacture= :idFacture");
            $stmt->bindValue(':idFacture', $idFacture);
            if ($stmt->execute()){
                $isDelete = true;
                $message = 'Facture supprimée avec succès';
            }else{
                $message = 'Erreur lors de la suppression de la facture';
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $isDelete;
    }
}