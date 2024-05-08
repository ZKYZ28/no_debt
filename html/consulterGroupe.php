<?php
require('../inc/headerMain.inc.php');
require '../php/db_groupe.inc.php';
require '../php/db_versement.inc.php';

use Groupe\GroupeManager;
use Depense\DepenseManager;
use Versement\Versement;
use Versement\VersementManager;

$idGroupe = $_GET['idGroupe'];
$idUser = $_SESSION['id'];
$nomGroupe = $_GET['nomGroupe'];

$groupeManager = new GroupeManager();
$depenseManager = new DepenseManager();
$user = new User\UserRepository();
$versementManager = new VersementManager();
$errorGroupe = false;

$messageErrorSold = '';

$members = $groupeManager->giveAllMemberOfOneGroup($idGroupe);
$depenses = $depenseManager->giveAllDepenseOfOneGroupe($idGroupe);

    /*GESTION DU SOLDER GROUPE*/
    if (isset($_POST['confirmerSolde'])) {
        $crediteurs = array();
        $debiteurs = array();
        $versement = new Versement();


        /*Création des créditeurs et débiteurs*/
        foreach ($members as $member) {
            $member['firstName'] = $user->getFirstNameOnId($member['iduser']);
            $member['montant'] = round($depenseManager->deviationAverage($member['iduser'], $idGroupe), 2);

            if ($member['montant'] < 0) {
                array_push($crediteurs, $member);
            } else {
                array_push($debiteurs, $member);
            }
        }

        do {
            $debiteurMax = array();
            $crediteurMax = array();
            $maxMontantDebiteur = 0;
            $maxMontantCrediteur = -1000000;
            $minEntreMontant = null;
            $isEnd = false;

            /*Check si un montant créditeur = un montant débiteur*/
            foreach ($crediteurs as &$crediteur) {
                $crediMontant = $crediteur['montant'];

                foreach ($debiteurs as &$debiteur) {
                    $debitMontant = $debiteur['montant'];

                    if (abs($crediMontant) == $debitMontant && abs($crediMontant) != 0 && $debitMontant != 0) {
                        $versement->dateHeure = date("Y-m-d H:i:s", time());
                        $versement->idCrediteur = $crediteur['iduser'];
                        $versement->idDebiteur = $debiteur['iduser'];
                        $versement->montant = $debiteur['montant'];
                        $versement->idGroupe = $idGroupe;
                        $versement->estConfirme = 0;

                        $versementManager->addVersement($versement);

                        $crediteur['montant'] = 0;
                        $debiteur['montant'] = 0;
                        break 2;
                    }
                }
            }


            /*Evaluation de si tous les DEBITEURS/CREDITEURS montant = 0*/
            $isEnd = $versementManager->checkIfAllZero($crediteurs, $debiteurs);

            /*Si tous les écarts à la moyenne ne sont pas nuls*/
            if (!$isEnd) {
                /*Recherche du plus grand montant CREDITEUR*/
                foreach ($crediteurs as &$crediteur) {
                    if ($crediteur['montant'] > $maxMontantCrediteur && $crediteur['montant'] != 0) {
                        $maxMontantCrediteur = $crediteur['montant'];
                        $crediteurMax = $crediteur;
                    }
                }

                /*Recherche du plus grand montant DEBITEUR*/
                foreach ($debiteurs as &$debiteur) {
                    if ($debiteur['montant'] > $maxMontantDebiteur && $debiteur['montant'] != 0) {
                        $maxMontantDebiteur = $debiteur['montant'];
                        $debiteurMax = $debiteur;
                    }
                }

                if (abs($maxMontantCrediteur) < $maxMontantDebiteur) {
                    $minEntreMontant = abs($maxMontantCrediteur);
                } else {
                    $minEntreMontant = $maxMontantDebiteur;
                }


                foreach ($crediteurs as &$crediteur) {
                    if ($crediteur['iduser'] == $crediteurMax['iduser']) {

                        foreach ($debiteurs as &$debiteur) {
                            if ($debiteur['iduser'] == $debiteurMax['iduser']) {
                                $versement->dateHeure = date("Y-m-d H:i:s", time());
                                $versement->idCrediteur = $crediteurMax['iduser'];
                                $versement->idDebiteur = $debiteurMax['iduser'];
                                $versement->montant = $minEntreMontant;
                                $versement->idGroupe = $idGroupe;
                                $versement->estConfirme = 0;

                                $versementManager->addVersement($versement);

                                $crediteur['montant'] = $crediteur['montant'] + $minEntreMontant;
                                $debiteur['montant'] = $debiteur['montant'] - $minEntreMontant;
                                break 2;
                            }
                        }
                    }
                }
            }
            /*Evaluation de si tous les DEBITEURS/CREDITEURS montant = 0*/
            $isEnd = $versementManager->checkIfAllZero($crediteurs, $debiteurs);
        } while (!$isEnd);


        /*Suppression de toutes les dépenses après le SOLD*/
        $allDepense = $depenseManager->giveAllDepenseOfOneGroupe($idGroupe);
        foreach ($allDepense as $thisDepense) {
            $depenseManager->deleteDepesne($thisDepense['iddepense'], $message);
        }

        header("Location:  consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
    }

    if (isset($_POST['refuserSolde']) || isset($_POST['refuserDelete'])) {
        header("Location:  consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
    }

    if (isset($_POST['versement-accepter'])) {
        $noError = $versementManager->updateVersement($_POST['hiddenVersement'], $message);
        header("Refresh: 1; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
    }

    if (isset($_POST['versement-refuser'])) {
        $noError = $versementManager->deleteVersement($_POST['hiddenVersement'], $message);
        header("Refresh: 1; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
    }

    if (isset($_POST['confirmerDelete'])) {
        $montant = $depenseManager->sumAllDepenseOfOnGroupe($idGroupe);
        $versements = $versementManager->giveAllVersementOnIdGroupe($idGroupe);
        $allInOne = true;

        foreach ($versements as $thisVersement){
            if($thisVersement['estconfirme'] != 1){
                $allInOne = false;
                $errorGroupe = true;
                $messageErrorSold .= 'Tous les versements doivent être confirmés';
                break;
            }
        }

        if($montant != 0){
            $errorGroupe = true;
            $messageErrorSold .= 'Vous ne pouvez pas supprimer un groupe non soldé et où les versements ne sont pas trous confirmés!';
        }

        if($montant == 0 && $allInOne == true){
            $noError = $groupeManager->deleteGroupe($idGroupe);
            header("Refresh: 1; groupes.php");
        }
    }
?>

<main>
    <h1 class="titre-Groupe"><?php echo $nomGroupe ?></h1>
    <a class="consulterGroupe-link-invitation"
       href="inviterParticipant.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>">Inviter un
        participant</a>
    <a class="consulterGroupe-link-invitation"
       href="gestionGroupes.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&instruction=edit">Modifier
        le groupe</a>
    <section class="groupe">
        <article class="article-consulter-groupe">
            <h2>Vue d'ensemble des dépenses</h2>
            <section class="section-consulter-groupe">

                <form class="solder-consulter-groupe"
                      action="consulterGroupe.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&sold=delete"
                      method="POST">
                    <input type="submit" name="delete" value="Supprimer le groupe">
                </form>
                <?php if ($_GET['sold'] == 'delete') { ?>

                    <form class="choice-invite" action="" method="POST">
                        <input id="accept-invite" type="submit" name="confirmerDelete" value="Confirmer">
                        <input id="decline-invite" type="submit" name="refuserDelete" value="Annuler">
                    </form>

                    <?php
                }

                if($errorGroupe){
                    ?>
                    <div class="alert-php space" ?>
                        <?php echo $messageErrorSold ?>
                    </div>
                    <?php }
                ?>

                <ul class="liste-ConsulterGroupe">
                    <li class="titre-tableau">Participant</li>
                    <li class="titre-tableau">Montant total dépense</li>
                    <li class="titre-tableau">Ecart à la moyenne</li>
                </ul>

                <?php foreach ($members as $member) { ?>
                    <ul class="liste-ConsulterGroupe">
                        <li><?php echo $user->getFirstNameOnId($member['iduser']) ?></li>
                        <li> <?php if ($depenseManager->giveSumDepenseOnIdUser($member['iduser'], $idGroupe) == null) {
                                echo 0;
                            } else {
                                echo $depenseManager->giveSumDepenseOnIdUser($member['iduser'], $idGroupe);
                            } ?> €
                        </li>
                        <li><?php if ($depenseManager->deviationAverage($member['iduser'], $idGroupe) == null) {
                                echo 0;
                            } else {
                                echo round($depenseManager->deviationAverage($member['iduser'], $idGroupe), 2);
                            } ?> €
                        </li>
                    </ul>
                <?php } ?>
            </section>
        </article>
    </section>


    <section class="groupe">
        <article class="article-consulter-groupe">
            <h2>Dépenses</h2>

            <ul class="liste-ConsulterGroupe">
                <li>
                    <form class="formulaire-consulter-groupe"
                          action="consulterGroupe.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&sold=nosold"
                          method="POST">
                        <input class="searchChamp" type="text" name="barre-de-recherche"
                               placeholder="Dépense à recherhcer ...">
                        <input type="submit" name="buttsub1" value="Rechercher">
                    </form>
                </li>
                <li><a class="consulterGroupe-link" href="rechercherDepense.php?idGroupe=<?php echo $idGroupe ?>">Recherche
                        avancée</a></li>
                <li><a class="consulterGroupe-link"
                       href="ajouterDepense.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&idUser=<?php echo $idUser ?>&instruction=create">Ajouter
                        une dépense</a></li>
            </ul>

            <ul class="liste-ConsulterGroupe">
                <li class="titre-tableau">Participant</li>
                <li class="titre-tableau">Montant</li>
                <li class="titre-tableau">Date</li>
                <li class="titre-tableau">Ajouter une facture</li>
                <li class="titre-tableau">Gestion facture</li>
                <li class="titre-tableau">Editer dépense</li>
                <li class="titre-tableau">Supprimer dépense</li>
            </ul>

            <?php if (isset($_POST['buttsub1'])) {
                $toFind = htmlentities($_POST['barre-de-recherche']);
                $depensesWithShearch = $depenseManager->findDepenseWithTagOrLibelle($idGroupe, $toFind, $message);
                if (empty($depensesWithShearch)) {
                    ?>
                    <div class="alert-php" ?>
                        <?php echo $message ?>
                    </div>
                <?php } else {
                    foreach ($depensesWithShearch as $depenseWithShearch) { ?>
                        <ul class="liste-ConsulterGroupe">
                            <li><?php echo $user->getFirstNameOnId($depenseWithShearch['iduser']) ?></li>
                            <li><?php echo $depenseWithShearch['montant'] ?> €</li>
                            <li><?php echo $depenseWithShearch['dateheure'] ?></li>
                            <li>
                                <a href="ajouterScan.php?idDepense=<?php echo $depenseManager->getIdDepense($depenseWithShearch['dateheure'], $depenseWithShearch['iduser']) ?>&idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>">Ajouter
                                    une facture</a></li>
                            <li>
                                <a href="gestionFacture.php?idDepense=<?php echo $depenseManager->getIdDepense($depenseWithShearch['dateheure'], $depenseWithShearch['iduser']) ?>">Gestion
                                    Facture</a></li>
                            <li>
                                <a href="ajouterDepense.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&idUser=<?php echo $idUser ?>&instruction=edit&idDepense=<?php echo $depenseManager->getIdDepense($depenseWithShearch['dateheure'], $depenseWithShearch['iduser']) ?>">Modifier
                                    dépense</a></li>
                            <li>
                                <a href="ajouterDepense.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&idUser=<?php echo $idUser ?>&instruction=delete&idDepense=<?php echo $depenseManager->getIdDepense($depenseWithShearch['dateheure'], $depenseWithShearch['iduser']) ?>&nomGroupe= <?php echo $nomGroupe ?>">Supprimer
                                    dépense</a></li>
                        </ul>
                    <?php }
                }

            } else {
                foreach ($depenses as $depense) { ?>
                    <ul class="liste-ConsulterGroupe">
                        <li><?php echo $user->getFirstNameOnId($depense['iduser']) ?></li>
                        <li><?php echo $depense['montant'] ?> €</li>
                        <li><?php echo $depense['dateheure'] ?></li>
                        <li>
                            <a href="ajouterScan.php?idDepense=<?php echo $depenseManager->getIdDepense($depense['dateheure'], $depense['iduser']) ?>&idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>">Ajouter
                                facture</a></li>
                        <li>
                            <a href="gestionFacture.php?idDepense=<?php echo $depenseManager->getIdDepense($depense['dateheure'], $depense['iduser']) ?>">Gestion
                                Facture</a></li>
                        <li>
                            <a href="ajouterDepense.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&idUser=<?php echo $idUser ?>&instruction=edit&idDepense=<?php echo $depenseManager->getIdDepense($depense['dateheure'], $depense['iduser']) ?>">Modifier
                                dépense</a></li>
                        <li>
                            <a href="ajouterDepense.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&idUser=<?php echo $idUser ?>&instruction=delete&idDepense=<?php echo $depenseManager->getIdDepense($depense['dateheure'], $depense['iduser']) ?>&nomGroupe= <?php echo $nomGroupe ?>">Supprimer
                                dépense</a></li>
                    </ul>
                <?php }
            } ?>
        </article>
    </section>

    <section class="groupe">
        <article class="article-consulter-groupe">
            <h2>Versements</h2>
            <form class="solder-consulter-groupe"
                  action="consulterGroupe.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&sold=sold"
                  method="POST">
                <input type="submit" name="solder" value="Solder">
            </form>
            <?php if ($_GET['sold'] == 'sold') { ?>

                <form class="choice-invite" action="" method="POST">
                    <input id="accept-invite" type="submit" name="confirmerSolde" value="Confirmer">
                    <input id="decline-invite" type="submit" name="refuserSolde" value="Annuler">
                </form>

                <?php
            }
            ?>

            <ul class="liste-ConsulterGroupe">
                <li class="titre-tableau">Donneur</li>
                <li class="titre-tableau">Montant</li>
                <li class="titre-tableau">Receveur</li>
                <li class="titre-tableau">Validation</li>
            </ul>

            <?php $allVersements = $versementManager->giveAllVersementOnIdGroupe($idGroupe);

            foreach ($allVersements as $thisVersement) {
                ?>
                <ul class="liste-ConsulterGroupe">
                    <li><?php echo $user->getFirstNameOnId($thisVersement['idcrediteur']) ?></li>
                    <li><?php echo $thisVersement['montant'] ?></li>
                    <li><?php echo $user->getFirstNameOnId($thisVersement['iddebiteur']) ?></li>
                    <li><?php if ($thisVersement['estconfirme'] == 1) {
                            echo 'Confirmé';
                        } else { ?>
                            <form class="choice-invite" action="consulterGroupe.php?idGroupe=<?php echo $idGroupe ?>&nomGroupe=<?php echo $nomGroupe ?>&sold=nosold" method="POST">
                                <input id="accept-invite" type="submit" name="versement-accepter" value="Accepter">
                                <input id="decline-invite" type="submit" name="versement-refuser" value="Refuser">
                                <input type="hidden" name="hiddenVersement" id="hiddenScan"
                                       value="<?php echo $thisVersement['idversement'] ?>">
                            </form>
                        <?php } ?></li>
                </ul>
                <?php
            } ?>
        </article>
    </section>
</main>

<?php require('../inc/footer.inc.php'); ?>