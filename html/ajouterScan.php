<?php
    require('../inc/headerMain.inc.php');
    require '../php/db_facture.inc.php';
    use Facture\Facture;
    use Facture\FactureManager;

    $message = '';
    $idGroupe = $_GET['idGroupe'];
    $nomGroupe = $_GET['nomGroupe'];
?>

        <main class="welcome">
            <section class="welcome-section">
                <h1>Ajouter une facture</h1>
                <?php if(isset($_POST['buttScan'])){
                    $facture = new Facture();

                    if(empty($_POST['nomDepense'])){
                        $message .= 'Nom de la facture vide';
                    }else{
                        $facture->scan = htmlentities($_POST['nomDepense']);
                    }
                   
                    $facture->idDepense = $_GET['idDepense'];

                    $factureManager = new FactureManager();

                    if($_FILES['facture']['error'] == 0){
                        $idForFacture = $factureManager->addFacture($facture, $message);

                        if( $idForFacture > 0){
                            $image = $_FILES['facture']['name'];
                            $ext = $ext = pathinfo($image, PATHINFO_EXTENSION);
                            $destination = '../uploads/'. $idForFacture . '.' . $ext;
                            move_uploaded_file($_FILES['facture']['tmp_name'], $destination);

                            ?>
                            <div class="success-php">
                                <?php echo $message?>
                            </div>
                            <?php
                            header("Refresh: 1.5; consulterGroupe.php?idGroupe=$idGroupe&nomGroupe=$nomGroupe&sold=nosold");
                        } else { ?>
                            <div class="alert-php">
                                <?php echo $message?>
                            </div>
                            <?php
                        }
                    }else{
                        ?>
                        <div class="alert-php">
                            <?php echo 'Erreur avec l\'image. Veuillez réessayer.' ?>
                        </div>
                        <?php
                    }
                }
                ?>

                <form action="#" method="POST" enctype="multipart/form-data">
                    <label for="nomDepense">Nom de la facture : <span class="obligatoire">*</span></label><input class="general" id="nomDepense" name="nomDepense" type="text" required>
                    <label for="facture">Ajout d'une facture <span class="obligatoire">*</span></label><input id="facture" type="file" name="facture" accept="image/*, .jpg, .png"> <br>
                    <input class="soumettreFormulaire" type="submit" name="buttScan" value="Valider">
                </form>
            </section>
        </main>  
        
<?php require('../inc/footer.inc.php');?>
