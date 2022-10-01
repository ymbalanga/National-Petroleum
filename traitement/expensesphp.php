<?php

session_start();
include '../db.php';



if (isset($_POST["action"]) && !empty($_POST["action"])) {
    try {
        extract($_POST);
        
        switch ($action) {
            case "update" : {

                    $expenses = $pdo->prepare('UPDATE depenses SET intitule =:intitule,montant =:montant,'
                            . ' datedepense =:datedepense '
                            . ' WHERE id =:idexpense');
                    if ($expenses->execute(array(
                                'intitule' => $reason,
                                'montant' => $amount,
                                'datedepense' => $expensedate,
                                'idexpense' => $idexpense)
                            )) {
                        echo "<p class='alert alert-success'>Update Successful!!!</p>";
                    } else {
                        echo "<p class='alert alert-danger'>Update Failed !!!</p>";
                    }
                };
                break;
        }
    } catch (Exception $ex) {
        echo "<p class='alert alert-danger'>Erreur !!!<br/> " . $ex->getMessage() . "</p>";
    }
    
    exit();
}

if (!empty($_POST['reason']) and ! empty($_POST['amount']) and ! empty($_POST['expensedate'])) {
    
    extract($_POST);
    $station = $_SESSION['station'];

    $expenses = $pdo->prepare('INSERT INTO depenses(intitule,montant,datedepense,station) '
            . 'VALUES (:intitule,:montant,:datedepense,:station)');
    if ($expenses->execute(array(
                'intitule' => $reason,
                'montant' => $amount,
                'datedepense' => $expensedate,
                'station' => $station)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
    } else {
        echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
    }
}

