<?php

session_start();
include '../db.php';



function getProduit($pdo,$idproduit){
    
    $sqlproduit = "SELECT * FROM produit "
                    . " WHERE id =:idproduit ";
    $reqproduit = $pdo->prepare($sqlproduit);
    $reqproduit->execute(array("idproduit"=>$idproduit));
    $resultproduit  = $reqproduit->fetch(PDO::FETCH_OBJ);
    
    return ($resultproduit)? $resultproduit : null;
    
}

function getCredit($pdo,$idcredit){
    
    $sql = "SELECT * FROM credit "
                    . " WHERE id =:idcredit ";
    $req = $pdo->prepare($sql);
    $req->execute(array("idcredit"=>$idcredit));
    $result  = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result : null;
    
}

function updateQuantity($pdo,$produit,$newquantity){

    $sqlproduitupdate = "UPDATE produit SET qtestock =:qtestock"
            . " WHERE id =:produit ";
    $reqproduitupdate = $pdo->prepare($sqlproduitupdate);
    $reqproduitupdate->execute(array("produit"=>$produit,"qtestock"=>$newquantity));
          
}

if (isset($_POST["customer"]) and isset($_POST["produit"]) and isset($_POST["qtecredit"]) and isset($_POST["creditdate"]) and isset($_POST['prix'])) {
    extract($_POST);
    $taux = $_SESSION['taux'];
    
    try {
        $pdo->beginTransaction();

        $search = $pdo->prepare('INSERT INTO credit (quantite,datecredit,idcustomer,produit,etat,taux,prix) '
                . 'VALUES (:quantite,:datecredit,:idcustomer,:produit,:etat,:taux,:prix)');
        if ($search->execute(array(
                    'quantite' => $qtecredit,
                    'datecredit' => $creditdate,
                    'idcustomer' => $customer,
                    'produit' => $produit,
                    'etat' => 0,
                    'taux' => $taux,
                    'prix' => $prix)
                )) {
            
            $produitupdate = getProduit($pdo, $produit);
            $qte = $produitupdate->qtestock;
            $newquantity = $qte - $qtecredit;
            updateQuantity($pdo, $produit, $newquantity);

            
            $pdo->commit();
            echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        } else {
            echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
        }
    } catch (Exception $ex) {
         $pdo->rollback();

        echo "<p class='alert alert-warning'>" . $ex->getMessage() . "</p>";
    }

    exit();
}

if (!empty($_POST['produit'])) {
    extract($_POST);
    $sqlSale = "SELECT prix FROM prixproduit"
            . " WHERE datefinale is NULL and produit =:produit";

    $reqSale = $pdo->prepare($sqlSale);
    $reqSale->execute(array('produit' => $produit));
    if ($resuSales = $reqSale->fetch(PDO::FETCH_OBJ)) {
        echo $resuSales->prix;
    } else {
        echo '';
    }
    exit();
}

if (isset($_POST['idCreditUP']) and isset($_POST["customerUP"]) and isset($_POST["produitUP"]) and isset($_POST["qtecreditUP"]) and isset($_POST["creditdateUP"]) and isset($_POST['prixUP'])) {
    extract($_POST);
    $taux = $_SESSION['taux'];

    try {
        $pdo->beginTransaction();
        
        $creditupdate = getCredit($pdo,$idCreditUP);
        $search = $pdo->prepare('UPDATE credit SET quantite =:quantiteUP,datecredit =:datecreditUP,idcustomer =:idcustomerUP,produit =:produitUP,etat =:etatUP,taux =:tauxUP,prix =:prixUP '
                . ' WHERE id =:id');
        if ($search->execute(array(
                    'quantiteUP' => $qtecreditUP,
                    'datecreditUP' => $creditdateUP,
                    'idcustomerUP' => $customerUP,
                    'produitUP' => $produitUP,
                    'etatUP' => 0,
                    'tauxUP' => $taux,
                    'prixUP' => $prixUP,
                    'id' => $idCreditUP)
                )) {
            
            
            $produitupdate = getProduit($pdo, $produitUP);
            
            $oldquantite  = $creditupdate->quantite;
            $qteproduit = $produitupdate->qtestock;
            $newquantity = ($qteproduit + $oldquantite ) - $qtecreditUP;

            updateQuantity($pdo, $produitUP, $newquantity);
            
            $pdo->commit();
            echo "<p class='alert alert-success'>Credit Updated !!!</p>";
            echo "<script type='text/javascript'>window.location.reload();</script>";
        } else {
            echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
        }
    } catch (Exception $ex) {
        
        $pdo->rollback();
        echo "<p class='alert alert-danger'>" . $ex->getMessage() . "</p>";
    }
    exit();
}
