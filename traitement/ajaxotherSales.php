<?php

session_start();
include '../db.php';


if (!empty($_POST['produitID'])) {
    extract($_POST);
    $sqlSale = "SELECT qtestock FROM produit"
            . " WHERE id =:produitid";

    $reqSale = $pdo->prepare($sqlSale);
    $reqSale->execute(array('produitid' => $produitID));
    if ($resuSales = $reqSale->fetch(PDO::FETCH_OBJ)) {
        echo $resuSales->qtestock;
    } else {
        echo '';
    }
}

?>

