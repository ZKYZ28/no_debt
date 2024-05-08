 <?php
    require('../inc/headerMain.inc.php');
     require '../php/db_groupe.inc.php';
     use Groupe\GroupeManager;
     use Groupe\Groupe;

     $message = '';
     $groupeManager = new GroupeManager();
     $create = null;
     $update = null;
     if($_GET['instruction'] != 'create'){
         $idGroupe = $_GET['idGroupe'];
         $nomGroupe = $_GET['nomGroupe'];
     }
 ?>

        <main class="welcome">
            <section class="welcome-section">
                <h1><?php
                    if($_GET['instruction'] == 'create'){
                        echo 'Création du groupe';
                    }else {
                        echo 'Modification du groupe';
                    }
                    ?> </h1>

                     <?php if(isset($_POST['buttEditerGroupe'])){
                        $groupe = new Groupe();

                         if(empty($_POST['nomGroupe'])){
                             $message .= 'Nom du groupe vide';
                         }else{
                             $groupe->nom = htmlentities($_POST['nomGroupe']);
                         }

                         if(empty($_POST['devise'])){
                             $message .= 'Devise vide';
                         }else{
                             $groupe->devise = htmlentities($_POST['devise']);
                         }

                        $groupe->idFounder = $_SESSION['id'];

                        if($_GET['instruction'] == 'create'){
                            $create = $groupeManager->storeGroupe($groupe, $message);
                        } else {
                            $update = $groupeManager->editGroupe($_GET['idGroupe'], $groupe->nom, $groupe->devise,$message);
                        }

                        if($create || $update){
                            ?>
                            <div class="success-php">
                                <?php echo $message?>
                            </div>
                            <?php
                        }else{
                            ?>
                            <div class="alert-php">
                                <?php echo $message?>
                            </div>
                            <?php
                        }
                         header("Refresh: 1.5; groupes.php");
                     }?>

                <form action="#" method="POST">
                    <label for="nomGroupe">Nom du groupe : <span class="obligatoire">*</span></label><input class="general" id="nomGroupe" name="nomGroupe" type="text" value = " <?php if($_GET['instruction'] == 'edit'){
                                                                                                                                                                                        echo $_GET['nomGroupe'];
                                                                                                                                                                                    }?>">
                    <label for="devise">Devise : <span class="obligatoire">*</span></label><input class="general" id="devise" name="devise" type="text" list="devises" value="€"><br>

                    <datalist id="devises">
                        <option value="€">EURO</option>
                        <option value="$">DOLLAR</option>
                    </datalist>
                    <input class="soumettreFormulaire" type="submit" name="buttEditerGroupe" value="<?php if($_GET['instruction'] == 'create'){
                                                                                                            echo 'Valider création';
                                                                                                        }else {
                                                                                                            echo 'Valider modification';
                                                                                                        } ?>">
                </form>

            </section>
        </main>

 <?php require('../inc/footer.inc.php');?>