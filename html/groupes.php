<?php

    require('../inc/headerMain.inc.php');
     require '../php/db_groupe.inc.php';
     use Groupe\GroupeManager;
     use Depense\DepenseManager;
     use Participer\ParticiperManager;

     $participerManager = new ParticiperManager();
     $groupeManager = new GroupeManager();
     $depesneManager = new DepenseManager();

    $allGroupe = $groupeManager->giveAllGroupsOfSomeone($_SESSION['id']);
    $allInvitation = $groupeManager->giveAllInvitationOfSomeone($_SESSION['id']);

    if (isset($_POST['groupe-accepter'])) {
        $noError = $participerManager->acceptInvitation($_SESSION['id'], $_POST['hiddenInvitation'], $message);
        header('Location: groupes.php');
    }

    if (isset($_POST['groupes-refuser'])) {
        $noError = $participerManager->declineInvitation($_SESSION['id'], $_POST['hiddenInvitation'], $message);
        header('Location: groupes.php');
    } ?>
        <main>
            <h2>Liste des groupes</h2>
            <section class="groupe">

                <?php if($allGroupe){?>
                    <?php foreach ($allGroupe as $groupe) {?>
                    <a class="link-groupes" href="consulterGroupe.php?idGroupe=<?php echo $groupe['id']?>&nomGroupe=<?php echo $groupe['nom']?>&sold=nosold">
                        <article >

                            <div id="article-images">
                                <span></span>
                            </div>

                            <div id="article-content">
                                <h3><?php echo $groupe['nom']?></h3>

                                <ul class="liste-groupe-info">
                                    <li><h4>Créateur du groupe</h4></li>
                                    <li class="info-groupes"><?php echo $groupeManager->giveFounderName($groupe['idfounder'])?></li>
                                </ul>

                                <ul class="liste-groupe-info">
                                    <li><h4>Montant total dépensé</h4></li>
                                    <li class="info-groupes"><?php
                                        if($depesneManager->sumAllDepenseOfOnGroupe($groupe['idgroupe']) == null){
                                            echo 0;
                                        } else{
                                            echo $depesneManager->sumAllDepenseOfOnGroupe($groupe['idgroupe']);
                                        }?> €</li>
                                </ul>

                                <h4>Dernières dépenses</h4>
                                <ul class="footer-groupes">
                                    <?php
                                    $lastDepense = $depesneManager->giveLastDepense($groupe['id']);
                                    foreach ($lastDepense as $thisDense){
                                    ?> <li><?php echo $thisDense['montant']?> €</li><?php
                                    }
                                    ?>
                                </ul>
                            </div>
                        </article>
                    </a>
                <?php } ?>
             <?php } else{
                    ?> <div class="alert-php">
                        <?php echo 'Vous n\'avez pas encore de groupes. Peut-être dans vos invitaions ?'; ?>
                       </div>
                    <?php
                    } ?>

            </section>


            <h2>Invitation(s) :</h2>
            <section class="groupe">

                <?php if($allInvitation){?>
                    <?php foreach ($allInvitation as $invitation);{?>

                        <article class="groupes-invitation">
                            <div id="article-images">
                                <span></span>
                            </div>

                            <div id="article-content">
                                <h3><?php echo $invitation['nom']?></h3>

                                <ul class="liste-groupe-info">
                                    <li><h4>Créateur du groupe</h4></li>
                                    <li class="info-groupes"><?php echo $groupeManager->giveFounderName($invitation['idfounder'])?></li>
                                </ul>

                                <ul class="liste-groupe-info">
                                    <li><h4>Montant total dépensé</h4></li>
                                    <li class="info-groupes"><?php
                                        if($depesneManager->sumAllDepenseOfOnGroupe($invitation['idgroupe']) == null){
                                            echo 0;
                                        } else{
                                            echo $depesneManager->sumAllDepenseOfOnGroupe($invitation['idgroupe']);
                                        }?> €</li>
                                </ul>

                                <h4>Dernières dépenses</h4>
                                <ul class="footer-groupes">
                                    <?php
                                    $lastDepense = $depesneManager->giveLastDepense($invitation['id']);
                                    foreach ($lastDepense as $thisDense){
                                        ?> <li><?php echo $thisDense['montant']?> €</li><?php
                                    }
                                    ?>
                                </ul>
                                <footer>
                                    <form class="choice-invite" action="#" method="POST">
                                        <input id="accept-invite" type="submit" name="groupe-accepter" value= "Accepter">
                                        <input id="decline-invite" type="submit" name="groupes-refuser" value="Refuser">
                                        <input type="hidden" name="hiddenInvitation" id="hiddenInvitation" value="<?php echo $invitation['id']  ?>">
                                    </form>
                                </footer>
                            </div>
                        </article>
                    <?php }?>
                <?php }

                else{
                    ?> <div class="alert-php">
                        <?php echo 'Vous n\'avez pas encore d\'invitation.'; ?>
                    </div>
                    <?php
                } ?>

            </section> 
        </main>  

 <?php require('../inc/footer.inc.php');?>

