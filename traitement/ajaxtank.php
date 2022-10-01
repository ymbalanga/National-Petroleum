<?php

require '../db.php';
require './fonctions.php';

function getLastOpeningForTank($pdo, $tank) {

    $sql = "SELECT dip FROM indextank "
            . " WHERE tank =:tank "
            . " ORDER BY datetank DESC LIMIT 1 ";

    $req = $pdo->prepare($sql);
    $req->execute(array("tank" => $tank));
    $result = $req->fetch(PDO::FETCH_OBJ);

    return ($result) ? $result->dip : null;
}

function existIndexTank($pdo, $tank, $dateindex) {
    $sql = "SELECT * FROM indextank WHERE tank =:tank AND datetank =:dateindex ";
    $req = $pdo->prepare($sql);
    $req->execute(array("tank" => $tank, "dateindex" => $dateindex));
    $result = $req->fetch(PDO::FETCH_OBJ);

    return ($result) ? true : false;
}

if (isset($_POST["action"]) && !empty($_POST["action"])) {
    $action = $_POST["action"];
    switch ($action) {

        case "lastOpenStock": {
                try {
                    extract($_POST);

                    $lastOpenStock = getLastOpeningForTank($pdo, $tank);
                    $reponse = ($lastOpenStock == null) ? -1 : $lastOpenStock;
                    echo json_encode(array("type" => 1, "reponse" => $reponse), JSON_FORCE_OBJECT);
                } catch (Exception $ex) {

                    echo json_encode(array("type" => 2, "reponse" => $ex->getMessage()), JSON_FORCE_OBJECT);
                }
            };
            break;

        case "addTankIndex" : {
                extract($_POST);

                try {

                    if (existIndexTank($pdo, $tank, $datetank)) {
                        echo "<p class='alert alert-warning'> An Index For This Tank is Already Saved For this Date !</p>";
                       
                    }
                    

                    addTankIndex($pdo, $tank, $openStock, $tests, $dip, $datetank);



                    echo "<p class='alert alert-success'>Information Recorded !!!</p>";
                    echo "<script type='text/javascript'>window.location.reload();</script>";
                } catch (Exception $ex) {

                    echo "<p class='alert alert-warning'>" . $ex->getMessage() . "</p>";
                }
                exit();
            };
            break;
        case "update": {
                extract($_POST);

                try {

                    updateIndexTank($pdo, $indexTank, $tankUpdate, $openStockUpdate, $testsUpdate, $dipUpdate, $datetankUpdate);
                    echo "<p class='alert alert-success'>Updated successfull !!!</p>";
                } catch (Exception $ex) {

                    echo "<p class='alert alert-warning'>Update Failed ! <br/>Erreur : " . $ex->getMessage() . "</p>";
                }
                exit();
            };
            break;
    }
}

function addTankIndex($pdo, $tank, $openStock, $tests, $dip, $date) {

    $q = $pdo->prepare("INSERT INTO indextank SET tank =:idtank,"
            . "openstock =:openStock,"
            . "tests =:tests,"
            . "dip =:dip,"
            . "datetank =:datetank ");

    $q->execute(array("idtank" => $tank,
        "openStock" => $openStock,
        "tests" => $tests,
        "dip" => $dip,
        "datetank" => $date));
}

function updateIndexTank($pdo, $indexTank, $tank, $openStock, $tests, $dip, $date) {

    $q = $pdo->prepare("UPDATE indextank SET tank =:idtank,"
            . "openstock =:openStock,"
            . "tests =:tests,"
            . "dip =:dip,"
            . "datetank =:datetank "
            . " WHERE id =:indexTank");

    $q->execute(array(
        "indexTank" => $indexTank,
        "idtank" => $tank,
        "openStock" => $openStock,
        "tests" => $tests,
        "dip" => $dip,
        "datetank" => $date));
}



