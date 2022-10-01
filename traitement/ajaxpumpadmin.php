<?php

session_start();
include '../db.php';

if (isset($_POST["tankad"]) and isset($_POST["pumpad"])) {
    extract($_POST);

    $pumpadmin = $pdo->prepare('INSERT INTO pompe (intitule,tank)'
            . ' VALUES (:intitule,:tank)');
    if ($pumpadmin->execute(array(
                'intitule' => $pumpad,
                'tank' => $tankad)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        echo "<script type='text/javascript'>window.location.reload();</script>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }
}

if (isset($_POST["tank"]) and isset($_POST["pump"]) and isset($_POST["id"])) {
    extract($_POST);

    $updatepump = $pdo->prepare('UPDATE  pompe SET  intitule =:intitule, tank =:tank'
            . ' WHERE id =:id');
    if ($updatepump->execute(array(
                'intitule' => $pump,
                'tank' => $tank,
                'id' => $id)
            )) {
        echo "<p class='alert alert-success'>Pump Information Updated !!!</p>";
        echo "<script type='text/javascript'>window.location.reload();</script>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }

    exit();
}
?>

