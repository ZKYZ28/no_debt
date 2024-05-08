<?php
        require('../inc/headerMain.inc.php');
        require '../php/db_depense.inc.php';
        require('../php/db_user.inc.php');
        use User\UserRepository as UserRepository;
        use Depense\DepenseManager as DepenseManager;

        $userRepository = new UserRepository();

        $idGroupe = $_GET['idGroupe'];
        $depenseManager = new DepenseManager();
        $correspondDepense = null;
        $message = '';

?>
     
        <main class="welcome">
            <section class="welcome-section">
                <h1>Rechercher une dépense : </h1>

                <?php if(isset($_POST['buttRechercheAvancee'])){

                    if(empty($_PSOT['date-debut'])){
                        $dateDebut = date("Y-m-d H:i:s", strtotime($_POST['date-debut']));
                    }else{
                        $dateDebut = $_POST['date-debut'];
                    }

                    if(empty($_PSOT['date-fin'])){
                        $dateFin = date("Y-m-d H:i:s", strtotime("2050-01-01 00:00:00"));
                    }else{
                        $dateFin = date("Y-m-d H:i:s", strtotime($_POST['date-fin']));
                    }

                    if(empty($_PSOT['montant-minimum'])){
                        $montantMin = 0;
                    }else{
                        $montantMin = htmlentities($_POST['montant-minimum']);
                    }

                    if(empty($_PSOT['montant-maximum'])){
                        $montantMax = 1000000;
                    }else{
                        $montantMax = htmlentities($_POST['montant-maximum']);
                    }

                    $libelle = htmlentities($_POST['libelle-ajout-depense']);
                    $tag = htmlentities($_POST['tag-ajout-depense']);

                    $correspondDepense = $depenseManager->findDepenseOnParam($idGroupe, $dateDebut, $dateFin, $montantMin, $montantMax, $libelle, $tag, $message);
                    if(empty($correspondDepense)){ ?>
                        <div class="alert-php" >
                            <?php echo $message?>
                        </div>
                        <?php
                    }
                }?>

                <form action="#" method="POST">
                    <label for="date-debut">Date début: </label><input class="general" id="date-debut" name="date-debut" type="date"> <br>
                    <label for="date-fin">Date fin: </label><input class="general" id="date-fin" name="date-fin" type="date"> <br>
                    <label for="montant-minimum">Montant minimum: </label><input class="general" id="montant-minimum" name="montant-ajout-depense" type="number"><br>
                    <label for="montant-maximum">Montant maximum: </label><input class="general" id="montant-maximum" name="montant-maximum" type="number"><br>
                    <label for="libelle-ajout-depense">Libellé : </label><input class="general" id="libelle-ajout-depense" name="libelle-ajout-depense" type="text" > <br>
                    <label for="tag-ajout-depense">Tag : </label><input class="general" id="tag-ajout-depense" name="tag-ajout-depense" type="text" > <br>
                    <input class="soumettreFormulaire" type="submit" name="buttRechercheAvancee" value="Valider">
                </form>
            </section>


            <?php if($correspondDepense != null){ ?>
                <h2>Voici les dépenses basées sur vos critères :</h2>
                <section class="groupe">
                    <article class="article-consulter-groupe">
                        <ul class="liste-ConsulterGroupe">
                            <li class="titre-tableau">Participant</li>
                            <li class="titre-tableau">Montant</li>
                            <li class="titre-tableau">Date</li>
                            <li class="titre-tableau">Ajouter une facture</li>
                            <li class="titre-tableau">Editer facture</li>
                        </ul>

                        <?php foreach ($correspondDepense as $thisDepense){?>
                            <ul class="liste-ConsulterGroupe">
                                <li><?php echo $userRepository->getFirstNameOnId($thisDepense['iduser'])?></li>
                                <li><?php echo $thisDepense['montant']?> €</li>
                                <li><?php echo $thisDepense['dateheure']?></li>
                                <li><a href="ajouterScan.php">Ajouter une facture</a></li>
                                <li>Modifier</li>
                            </ul>
                        <?php } ?>
                    </article>
                </section>
            <?php } ?>
        </main>   
<?php require('../inc/footer.inc.php');?>
