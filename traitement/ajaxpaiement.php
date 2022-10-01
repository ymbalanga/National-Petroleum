<?php

session_start();
include '../db.php';

if (isset($_POST["idcredit"]) and isset($_POST["amountpay"])) {
    extract($_POST);

    $search = $pdo->prepare('INSERT INTO paiementcredit (montant,datepay,credit) '
            . 'VALUES (:amountpay,NOW(),:idcredit)');
    if ($search->execute(array(
                'idcredit' => $idcredit,
                'amountpay' => $amountpay)
            )) {
        
  
        $updateCredit = $pdo->prepare('UPDATE credit SET etat =:etat WHERE id =:idcredit ');
        $updateCredit->execute(array("etat"=>$changerEtatCredit,"idcredit"=>$idcredit));
        

        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
    }
}
