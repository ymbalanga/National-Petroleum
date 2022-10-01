<?php

session_start();
include '../db.php';
require './fonctions.php';

$station = $_SESSION['station'];

if (isset($_POST["intitule"]) and isset($_POST["typeproduit"])  and isset($_POST["prix"])) {
    extract($_POST);

    try{
    $pdo->beginTransaction();  
    
    $pumpadmin = $pdo->prepare('INSERT INTO produit (intitule,typeproduit,station)'
            . ' VALUES (:intitule,:typeproduit,:station)');
    if ($pumpadmin->execute(array(
                'intitule' => $intitule,
                'typeproduit' => $typeproduit,
                'station' => $station)
            )) {
        
        
        $idproduit = $pdo->lastInsertId();
        $insertPrixProtuit = $pdo->prepare('INSERT INTO prixproduit (prix,dateinitiale,produit)'
            . ' VALUES (:prix,NOW(),:idproduit)');
        
        $insertPrixProtuit->execute(array("idproduit"=>$idproduit,"prix"=>$prix));
        
        $pdo->commit();
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Insertion Failed !!!</p>";
    }
    
    }catch (Exception $ex){
        $pdo->rollback();
        echo "<p class='alert alert-danger'>Erreur  !!! <br/> $ex->getMessage()</p>";
    }
    exit();
}


if (isset($_POST["intituleUP"]) and isset($_POST["typeproduitUP"])  and isset($_POST["prixUP"])) {
    extract($_POST);

    try{
        $pdo->beginTransaction();
        
        //Update du produit
        //Mis à jour du prix  si le prix est différent du prix courrent
        
        $updateproduit = $pdo->prepare('UPDATE  produit SET  intitule =:intituleUP, '
            . 'typeproduit =:typeproduitUP,station=:stationUP '
            . ' WHERE id =:id');
        $updateproduit->execute(array(
                    'intituleUP' => $intituleUP,
                    'typeproduitUP' => $typeproduitUP,
                    'stationUP' => $station,
                    'id' => $id)
                );
            
            $prixproduitcourrent = getPrixProduit($pdo, $id);

            
            if($prixproduitcourrent && ($prixUP != $prixproduitcourrent)){
                
                $updateprixproduit = $pdo->prepare('UPDATE  prixproduit SET  datefinale = NOW() '
                . ' WHERE produit =:idproduit AND datefinale is NULL ');
                $updateprixproduit->execute(array("idproduit"=>$id));

                $insertPrixProtuit = $pdo->prepare('INSERT INTO prixproduit (prix,dateinitiale,produit)'
                    . ' VALUES (:prix,NOW(),:idproduit)');

                $insertPrixProtuit->execute(array("idproduit"=>$id,"prix"=>$prixUP));
                
            }
            
            echo "<p class='alert alert-success'>Product Information Updated !!!</p>";
            echo "<script type='text/javascript'>window.location.reload();</script>";
        
       
        $pdo->commit();
    } catch (Exception $ex) {
        
        $pdo->rollback();
        echo "<p class='alert alert-danger'>Erreur  !!! <br/> ".$ex->getMessage()."</p>";
    }
    
    

    exit();
}
?>

