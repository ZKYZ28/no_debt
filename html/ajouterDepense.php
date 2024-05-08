 <?php
     require('../inc/headerMain.inc.php');
     require '../php/db_depense.inc.php';
     require('../php/db_user.inc.php');
     require('../php/db_tag.inc.php');
     require('../php/db_caracteriser.inc.php');
     use User\UserRepository as UserRepository;
     use Depense\Depense;
     use Depense\DepenseManager as DepenseManager;
     use Tag\Tag;
     use Tag\TagManager;
     use Caracteriser\Caracteriser;
     use Caracteriser\CaracteriserManager;

     $currentDate = date("Y-m-d H:i:s", time());
     $message = '';
     $instruction = $_GET['instruction'];
     $idGroupe = $_GET['idGroupe'];
     $nomGroupe = $_GET['nomGroupe'];
     $idUser = $_GET['idUser'];

     if($instruction == 'edit' ||$instruction == 'delete'){
         $idDepense = $_GET['idDepense'];
     }

     $userRepository = new UserRepository();
     $depenseManager = new DepenseManager();
     $tagManager = new TagManager();
     $caracteriserManager = new CaracteriserManager();

     if($instruction == 'edit' || $instruction == 'delete'){
         $infoDepense = $depenseManager->giveDepenseOnId($_GET['idDepense'], $message);
     }
 ?>
        <main class="welcome">
            <section class="welcome-section">
                <h1><?php
                    if($instruction == 'create'){
                        echo 'Créer dépense';
                    }else if($instruction == 'edit') {
                        echo 'Modifier dépense';
                    }else{
                        echo 'Supprimer la dépense';
                    }
                    ?></h1>

                <?php if(isset($_POST['buttDepense'])){
                     $tag = htmlentities($_POST['tag-ajout-depense']);
                     $participant = htmlentities($_POST['particiapnt-ajout-depense']);

                     $depense = new Depense();

                    if(empty($_POST['date-ajout-depense'])){
                        $message .= 'Date vide';
                    }else{
                        $depense->dateHeure = htmlentities($_POST['date-ajout-depense']);
                    }

                    if(empty($_POST['montant-ajout-depense'])){
                        $message .= 'Montant vide';
                    }else{
                        $depense->montant = htmlentities($_POST['montant-ajout-depense']);
                    }

                     $depense->libelle = htmlentities($_POST['libelle-ajout-depense']);
                     $depense->idGroupe = $_GET['idGroupe'];
                     $depense->idUser = $userRepository->getIdOnFirstName($participant);

                     if($instruction == 'create'){
                         if($depenseManager->addDepense($depense, $message)){
                                     $tag = new Tag();
                                     $tag->tag = htmlentities($_POST['tag-ajout-depense']);
                                     $tag->idGroupe = $_GET['idGroupe'];
                                     $tagManager->addTag($tag);

                                     $caracteriser = new Caracteriser();
                                     $caracteriser->idDepense = $depenseManager->getIdDepense($depense->dateHeure, $depense->idUser);
                                     $caracteriser->idTag = $tagManager->getTagId($tag->tag);
                                     $caracteriserManager->addCaracteriser($caracteriser);
                               ?>
                                     <div class="success-php" >
                                    <?php echo $message ?>
                                     </div>

                             <?php
                             header("Refresh: 1.5; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                                }
                     } else if ($instruction == 'edit'){
                         if($depenseManager->editDepense($idDepense, $depense, $message)){
                             $tag = new Tag();
                             $tag->tag = htmlentities($_POST['tag-ajout-depense']);
                             $tag->idGroupe = $_GET['idGroupe'];
                             $tagManager->addTag($tag);

                             $caracteriser = new Caracteriser();
                             $caracteriser->idDepense = $depenseManager->getIdDepense($depense->dateHeure, $depense->idUser);
                             $caracteriser->idTag = $tagManager->getTagId($tag->tag);
                             $caracteriserManager->addCaracteriser($caracteriser);
                             ?>
                             <div class="success-php" >
                                 <?php echo $message ?>
                             </div>
                             <?php
                             header("Refresh: 1.5; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                         }
                     }
                 }?>


                <?php if(isset($_POST['valideDelete'])){
                    if($depenseManager->deleteDepesne($_GET['idDepense'], $message)){
                        ?>
                        <div class="success-php">
                            <?php echo $message ?>
                        </div>
                    <?php } else{
                        ?>
                        <div class="alert-php">
                            <?php echo $message ?>
                        </div>
                        <?php
                    }
                        header("Refresh: 1.5; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                }else if(isset($_POST['noDelete'])){
                    header("Location: consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                }
                ?>


                <?php if ($instruction == 'delete'){ ?>
                    <form class="formConfirm" action="#" method="POST" >
                        <input class="soumettreFormulaire" type="submit" name="valideDelete" value="Supprimer la dépense">
                        <input class="soumettreFormulaire" type="submit" name="noDelete" value="Annuler la suppression">
                    </form>
                <?php } ?>


                    <form action="#" method="POST">
                    <label for="date-ajout-depense">Date : <span class="obligatoire">*</span></label><input class="general" id="date-ajout-depense" name="date-ajout-depense" type="text" value="<?php echo $currentDate?>"><br>
                    <label for="montant-ajout-depense">Montant : <span class="obligatoire">*</span></label><input class="general" id="montant-ajout-depense" name="montant-ajout-depense" type="number" value="<?php if($instruction == 'edit' || $instruction == 'delete'){
                                                                                                                                                                                                                            echo $infoDepense['montant'];
                                                                                                                                                                                                                        }
                                                                                                                                                                                                                        ?>"><br>
                    <label for="libelle-ajout-depense">Libellé : </label><input class="general" id="libelle-ajout-depense" name="libelle-ajout-depense" type="text" value="<?php if($instruction == 'edit' || $instruction == 'delete'){
                                                                                                                                                                                                                echo $infoDepense['libelle'];
                                                                                                                                                                                                            }
                                                                                                                                                                                                            ?>"><br>
                    <label for="tag-ajout-depense">Tag : </label><input class="general" id="tag-ajout-depense" name="tag-ajout-depense" type="text" value="<?php if($instruction == 'edit' || $instruction == 'delete'){
                                                                                                                                                                                                        echo $tagManager->findTagOnIdDepense($_GET['idDepense']);
                                                                                                                                                                                                    }
                                                                                                                                                                                                    ?>""><br>
                    <label for="particiapnt-ajout-depense">Participant : <span class="obligatoire">*</span></label><input class="general" id="particiapnt-ajout-depense" name="particiapnt-ajout-depense" type="text" value="<?php echo $_SESSION['firstname']?>"><br>

                    <?php if($instruction == 'create' || $instruction == 'edit'){ ?>
                    <input class="soumettreFormulaire" type="submit" name="buttDepense" value="<?php
                                                                                                if($instruction == 'create'){
                                                                                                    echo 'Valider création';
                                                                                                }else {
                                                                                                    echo 'Valider modification';
                                                                                                }
                                                                                                ?>">
                    <?php } ?>
                </form>
            </section>
        </main> 

 <?php require('../inc/footer.inc.php');?>