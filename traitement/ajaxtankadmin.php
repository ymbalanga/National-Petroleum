<?php

session_start();
include '../db.php';

$station = $_SESSION['station'];
if (isset($_POST["intitule"]) and isset($_POST["typetank"])) {
    extract($_POST);

    $pumpadmin = $pdo->prepare('INSERT INTO tank (intitule,typetank,station)'
            . ' VALUES (:intitule,:typetank,:station)');
    if ($pumpadmin->execute(array(
                'intitule' => $intitule,
                'typetank' => $typetank,
                'station' => $station)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }
}


if (isset($_POST["intituleUP"]) and isset($_POST["typetankUP"])) {
    extract($_POST);

    $updatepump = $pdo->prepare('UPDATE  tank SET  intitule =:intituleUP, typetank =:typetankUP'
            . ' WHERE id =:id');
    if ($updatepump->execute(array(
                'intituleUP' => $intituleUP,
                'typetankUP' => $typetankUP,
                'id' => $id)
            )) {
        echo "<p class='alert alert-success'>Tank Information Updated !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }

    exit();
}
?>

