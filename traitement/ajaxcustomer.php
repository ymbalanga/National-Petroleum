<?php
include '../db.php';

if (!empty($_POST['nom']) and ! empty($_POST['resp'])) {
    extract($_POST);

     $search = $pdo->prepare('INSERT INTO customer (nom,etat,partieresponsable) '
            . 'VALUES (:nom,:etat,:partieresponsable)');
    if ($search->execute(array(
                'nom' => $nom,
                'etat' => 0,
                'partieresponsable' => $resp)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
    }
}
?>