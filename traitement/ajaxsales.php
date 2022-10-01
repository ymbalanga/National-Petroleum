<?php

session_start();
include '../db.php';

//update
if (isset($_POST["idventeUP"]) && !empty($_POST["qtevenduUP"])) {
    try {
        $taux = $_SESSION['taux'];
        extract($_POST);
        
        $pdo->beginTransaction();
        
        
        $venteupdate = getVente($pdo,$idventeUP);
        
        
        $produitupdate = getProduit($pdo, $produitUP);
        
        $oldqte  = $venteupdate->qtevendu;
        $qte = $produitupdate->qtestock;
        $newquantity = ($qte + $oldqte ) - $qtevenduUP;
        
        updateVente($pdo,$idventeUP, $produitUP, $qtevenduUP, $dateventeUP, $prixventeUP,$taux);
        updateQuantity($pdo, $produitUP, $newquantity);
        
        $pdo->commit();
        
        echo "<p class='alert alert-success'>Information Update Successfull !!!</p>";
    } catch (Exception $ex) {
        
        $pdo->rollback();

        echo "<p class='alert alert-warning'>" . $ex->getMessage() . "</p>";
    }
    exit();
}



if (!empty($_POST['produit']) and ! empty($_POST['qtevendu']) and ! empty($_POST['datevente']) and ! empty($_POST['prixvente'])) {
    extract($_POST);

    $taux = $_SESSION['taux'];
    
    try {
        $pdo->beginTransaction();
        
        $search = $pdo->prepare('INSERT INTO vente (produit,datevente,qtevendu,prixvente,taux) '
            . 'VALUES (:produit,:datevente,:qtevendu,:prixvente,:taux)');
    
        if ($search->execute(array(
                'produit' => $produit,
                'datevente' => $datevente,
                'qtevendu' => $qtevendu,
                'prixvente' => $prixvente,
                'taux' => $taux)
            )) {
            
            $produitupdate = getProduit($pdo, $produit);
            $qte = $produitupdate->qtestock;
            $newquantity = $qte - $qtevendu;
            updateQuantity($pdo, $produit, $newquantity);
            
            $pdo->commit();
            echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        } else {
            echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
        }
        
    } catch (Exception $ex) {
        $pdo->rollback();
        echo "<p class='alert alert-danger'>".$ex->getMessage()."</p>";
    }
    
}

function getProduit($pdo,$idproduit){
    
    $sqlproduit = "SELECT * FROM produit "
                    . " WHERE id =:idproduit ";
    $reqproduit = $pdo->prepare($sqlproduit);
    $reqproduit->execute(array("idproduit"=>$idproduit));
    $resultproduit  = $reqproduit->fetch(PDO::FETCH_OBJ);
    
    return ($resultproduit)? $resultproduit : null;
    
}

function getVente($pdo,$idvente){
    
    $sql = "SELECT * FROM vente "
                    . " WHERE id =:idvente ";
    $req = $pdo->prepare($sql);
    $req->execute(array("idvente"=>$idvente));
    $result  = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result : null;
    
}

function updateQuantity($pdo,$produit,$newquantity){

    $sqlproduitupdate = "UPDATE produit SET qtestock =:qtestock"
            . " WHERE id =:produit ";
    $reqproduitupdate = $pdo->prepare($sqlproduitupdate);
    $reqproduitupdate->execute(array("produit"=>$produit,"qtestock"=>$newquantity));
          
}

if (!empty($_POST['produitID'])) {
    extract($_POST);
    $sqlSale = "SELECT prix FROM prixproduit"
            . " WHERE datefinale is NULL and produit =:produitid";

    $reqSale = $pdo->prepare($sqlSale);
    $reqSale->execute(array('produitid' => $produitID));
    if ($resuSales = $reqSale->fetch(PDO::FETCH_OBJ)) {
        echo $resuSales->prix;
    } else {
        echo '';
    }
}

function updateVente($db,$idventeUP, $produitUP, $qtevenduUP, $dateventeUP, $prixventeUP,$taux) {

    $q = $db->prepare("UPDATE vente SET produit =:produit,"
            . "datevente =:datevente,"
            . "qtevendu =:qtevendu,"
            . "prixvente =:prixvente,"
            . "taux =:taux"
            . " WHERE id =:id");

    $q->execute(array(
        "produit" => $produitUP,
        "datevente" => $dateventeUP,
        "qtevendu" => $qtevenduUP,
        "prixvente" => $prixventeUP,
        "id"=>$idventeUP,
        "taux" => $taux));
}
?>

