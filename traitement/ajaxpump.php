<?php
include '../db.php';

function getLastFinalIndexPump($pdo,$pump){
    
    $sql = "SELECT indexfinal FROM indexpompe "
            . " WHERE idpompe =:pump "
            . " ORDER BY dateindex DESC LIMIT 1 ";

    $req = $pdo->prepare($sql);
    $req->execute(array('pump' => $pump));
    $result =  $req->fetch(PDO::FETCH_OBJ) ;
    
    return ($result) ? $result->indexfinal : null;
}

if(isset($_POST['action']) and !empty($_POST['action'])){
    

    extract($_POST);
    switch($action){
        
        case "finalIndex":{
            try{
                $lastFinalIndex = getLastFinalIndexPump($pdo,$pump);
                $reponse = ($lastFinalIndex == null)? -1 : $lastFinalIndex;
                echo json_encode(array("type"=>1,"reponse"=>$reponse), JSON_FORCE_OBJECT);
            } catch (Exception $ex) {
                echo json_encode(array("type"=>2,"reponse"=>$ex->getMessage()), JSON_FORCE_OBJECT); 
            }
            
        };break;
        case "update" :{
            
            $search = $pdo->prepare('UPDATE indexpompe SET indexinitial =:indexinitial,'
            . ' indexfinal =:indexfinal,dateindex=:indexdate,idpompe=:idpompe '
            . ' WHERE id =:indexpump ');
            if ($search->execute(array(
                        'indexpump'=>$indexpump,
                        'indexinitial' => $initial,
                        'indexfinal' => $pfinal,
                        'indexdate' => $indexdate,
                        'idpompe' => $pump)
                    )) {
                echo "<p class='alert alert-success'>Update Succeded !!!</p>";
            }else {
                echo "<p class='alert alert-success'>Update Failed !!!</p>";
            }
            
        } ;break;
    }

    exit();
}



if (!empty($_POST['initial']) and ! empty($_POST['pfinal'])) {
    $initial = $_POST['initial'];
    $final = $_POST['pfinal'];
    $pump = $_POST['pump'];
    $indexdate = $_POST['indexdate'];
    
    if(existIndexPump($pdo, $pump, $indexdate)){
        echo "<p class='alert alert-warning'> An Index For This Pump is Already Saved For this Date !</p>";
        exit();
    }
    $search = $pdo->prepare('INSERT INTO indexpompe(indexinitial,indexfinal,dateindex,idpompe) '
                        . 'VALUES (:indexinitial,:indexfinal,:indexdate,:idpompe)');
    if ($search->execute(array(
                'indexinitial' => $initial,
                'indexfinal' => $final,
                'indexdate' => $indexdate,
                'idpompe' => $pump)
            )) {
        echo "<p class='alert alert-success'>Information Recorded !!!</p>";
        echo "<script type='text/javascript'>window.location.reload();</script>";
    } else {
        echo "<p class='alert alert-danger'>Recording Failed !!!</p>";
    }
}

function existIndexPump($pdo,$pump,$dateindex){
    $sql = "SELECT * FROM indexpompe WHERE idpompe =:pump AND dateindex =:dateindex ";
    $req = $pdo->prepare($sql);
    $req->execute(array("pump"=>$pump,"dateindex"=>$dateindex));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result) ? true : false;
}


