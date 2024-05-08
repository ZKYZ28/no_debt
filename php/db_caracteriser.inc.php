<?php
namespace Caracteriser;
use DB\DBLink;

/**
 * Class Caracteriser : Caracteriser une dépense
 */
class Caracteriser{
    public $idDepense;
    public $idTag;
}

/**
 * Class CaracteriserManager : Gestion de Caracteriser
 */
class CaracteriserManager{
    const TABLE_NAME = 'caracteriser';

    public function addCaracteriser($caracteriser){
        $noError = false;
        $bdd   = null;

        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (idDepense, idTag) VALUES (:idDepense, :idTag)");
            $stmt->bindValue(':idDepense', $caracteriser->idDepense);
            $stmt->bindValue(':idTag', $caracteriser->idTag);
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
}
