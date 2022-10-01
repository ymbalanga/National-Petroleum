<?php

session_start();
include '../db.php';

if (isset($_POST["action"])) {
    extract($_POST);
    switch ($action) {
        case 1 : addStation($pdo,$intitule);
            break;
        case 2 : updateStation($pdo,$idstation, $intituleupdate);
            break;
    }
}

function addStation($pdo,$intitule) {

    if (!empty($intitule)) {

        $station = $pdo->prepare('INSERT INTO station(intitule) VALUES (:intitule)');
        if ($station->execute(array(
                    'intitule' => $intitule)
                )) {
            echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        } else {
            echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
        }
    }
}

function updateStation($pdo,$idstation,$intitule) {

    if (!empty($intitule)) {

        $station = $pdo->prepare('UPDATE  station SET intitule =:intitule WHERE id =:idstation');
        if ($station->execute(array(
                    'intitule' => $intitule,"idstation"=>$idstation)
                )) {
            echo "<p class='alert alert-success'>Updated !!!</p>";
        } else {
            echo "<p class='alert alert-danger'>Update Failed !!!</p>";
        }
    }
}