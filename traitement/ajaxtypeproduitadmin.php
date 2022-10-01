<?php

session_start();
include '../db.php';

if (isset($_POST["intitule"]) and isset($_POST["unite"]) and isset($_POST["datetype"])) {
    extract($_POST);
    $pumpadmin = $pdo->prepare('INSERT INTO typeproduit (intitule,datetype,unite)'
            . ' VALUES (:intitule,:datetype,:unite)');
    if ($pumpadmin->execute(array(
                'intitule' => $intitule,
                'datetype' => $datetype,
                'unite' => $unite)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }
}


if (isset($_POST["intituleUP"]) and isset($_POST["datetypeUP"]) and isset($_POST["uniteUP"]) ) {
    extract($_POST);

    $updatepump = $pdo->prepare('UPDATE  typeproduit SET  intitule =:intituleUP,datetype=:datetypeUP, unite =:uniteUP'
            . ' WHERE id =:id');
    if ($updatepump->execute(array(
                'intituleUP' => $intituleUP,
                'datetypeUP' => $datetypeUP,
                'uniteUP' => $uniteUP,
                'id' => $id)
            )) {
        echo "<p class='alert alert-success'>Product type Information Updated !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
    }

    exit();
}
?>

