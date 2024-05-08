<?php
namespace Tag;
use DB\DBLink;

/**
 * Class Tag
 */
class Tag{
    public $tag;
    public $idGroupe;
}

/**
 * Class Tag
 */
class TagManager{
    const TABLE_NAME = 'tag';

    public function addTag($tag){
        if(!(TagManager::tagExist($tag))){
            $noError = false;
            $bdd   = null;
            try {
                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("INSERT INTO ".self::TABLE_NAME." (tag, idGroupe) VALUES (:tag, :idGroupe)");
                $stmt->bindValue(':tag', $tag->tag);
                $stmt->bindValue(':idGroupe', $tag->idGroupe);
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


    public function tagExist($tag){
        $exist = false;
        $bdd    = null;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT * FROM ".self::TABLE_NAME." WHERE tag= :tag");
            $stmt->bindValue(':tag', $tag->tag);
            if ($stmt->execute()){
                if($stmt->fetch() !== false){
                    $exist = true;
                }
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $exist;
    }

    public function getTagId($tag){
        $bdd    = null;
            try {
                $bdd  = DBLink::connect2db(MYDB, $message);
                $stmt = $bdd->prepare("SELECT idTag FROM ".self::TABLE_NAME." WHERE tag= :tag");
                $stmt->bindValue(':tag', $tag);
                if ($stmt->execute()){
                        $result = $stmt->fetch();
                        $id = $result['idtag'];
                }
                $stmt = null;
            } catch (Exception $e) {
                $message .= $e->getMessage().'<br>';
            }
            DBLink::disconnect($bdd);
            return $id;
    }

    public function findTagOnIdDepense($idDepense){
        $bdd    = null;
        try {
            $bdd  = DBLink::connect2db(MYDB, $message);
            $stmt = $bdd->prepare("SELECT tag FROM ".self::TABLE_NAME." t JOIN caracteriser car ON car.idTag = t.idTag WHERE car.idDepense= :idDepense");
            $stmt->bindValue(':idDepense', $idDepense);
            if ($stmt->execute()){
                $result = $stmt->fetch();
                $tag = $result['tag'];
            }
            $stmt = null;
        } catch (Exception $e) {
            $message .= $e->getMessage().'<br>';
        }
        DBLink::disconnect($bdd);
        return $tag;
    }
}