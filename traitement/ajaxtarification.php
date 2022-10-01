<?php

session_start();
include '../db.php';
require './fonctions.php';

$station = $_SESSION['station'];



if (isset($_POST["idprixproduit"]) and isset($_POST["nouveauprix"])) {
    extract($_POST);

    try{
        $pdo->beginTransaction();
        
        //Update du prix 
        //Mis à jour du prix  si le prix est différent du prix courrent
        $sql = "SELECT * FROM prixproduit WHERE id =:idprixproduit";
        $req = $pdo->prepare($sql);
        $req->execute(array("idprixproduit"=>$idprixproduit));
        $result = $req->fetch(PDO::FETCH_OBJ);
        
        $prixproduitcourrent = $result->prix;
        $idproduit = $result->produit;
 
        if($prixproduitcourrent && ($nouveauprix != $prixproduitcourrent)){

            $updateprixproduit = $pdo->prepare('UPDATE  prixproduit SET  datefinale = NOW() '
            . ' WHERE id =:idprixproduit AND datefinale is NULL ');
            $updateprixproduit->execute(array("idprixproduit"=>$idprixproduit));

            $insertPrixProtuit = $pdo->prepare('INSERT INTO prixproduit (prix,dateinitiale,produit)'
                . ' VALUES (:prix,NOW(),:idproduit)');

            $insertPrixProtuit->execute(array("idproduit"=>$idproduit,"prix"=>$nouveauprix));
            
        }
       
        $pdo->commit();
        $dateinitiale = date("Y-m-d");
        $reponse = array("type"=>1,"dateinitiale"=>$dateinitiale,"message"=>"Prize Update Successfull !");
        echo json_encode($reponse, JSON_FORCE_OBJECT);
            
    } catch (Exception $ex) {
        
        $pdo->rollback();
        $reponse = array("type"=>2,"message"=>"ERREUR : ".$ex->getMessage());
        echo json_encode($reponse, JSON_FORCE_OBJECT);
    }

    exit();
}


?>

