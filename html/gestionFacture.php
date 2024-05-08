<?php
    require('../inc/headerMain.inc.php');
    require '../php/db_facture.inc.php';
    use Facture\FactureManager;
    $factureManager = new FactureManager();
    $idDepense = $_GET['idDepense'];
    $message = '';


    $allFacture = $factureManager->giveAllFactureOnIdDepense($idDepense, $message);
?>

    <main>
        <h2>Gestion des factures</h2>
        <section class="groupe">

            <?php if(isset($_POST['deleteScan'])){
                if($factureManager->deleteFacture($_POST['hiddenScan'], $message)){
                    var_dump(unlink('../uploads/'.$_POST['hiddenScan'].'.jpg'));
                    header("Location: gestionFacture.php?idDepense=$idDepense");
                }
            }?>


            <?php if($allFacture){?>
                <?php foreach ($allFacture as $facture){
                    $destination = '../uploads/'.$facture['idfacture'].'.jpg';
                    if(!file_exists($destination)){
                        $destination = '../uploads/'.$facture['idfacture'].'.png';
                    }
                    ?>

                    <article class="gestion-facture">
                        <h3><?php echo $facture['scan']?></h3>

                        <img src="<?php echo $destination?>" alt="Image not FOUD">

                        <form class="formConfirm" action="" method="POST">
                            <input class="soumettreFormulaire" type="submit" name="deleteScan" value="Supprimer la facture">
                            <input type="hidden" name="hiddenScan" id="hiddenScan" value="<?php echo $facture['idfacture']?>">
                        </form>
                    </article>
                <?php } ?>
            <?php } else{
                ?> <div class="alert-php">
                    <?php echo 'Aucune facture liée à cette dépense' ?>
                </div>
                <?php
            } ?>
        </section>
    </main>
<?php require('../inc/footer.inc.php');?>