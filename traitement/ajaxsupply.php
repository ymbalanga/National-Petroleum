<?php

session_start();
include '../db.php';
require './fonctions.php';

if (isset($_POST["action"])) {
    extract($_POST);
    switch ($action) {
        case 1 : {
            
                try {
                    $pdo->beginTransaction();
                    
                    addAppro($pdo,$qteappro,$produit,$dateappro,$prixachat);
            
                    $produitupdate = getProduit($pdo, $produit);
                    $qte = $produitupdate->qtestock;
                    $newquantity = $qte + $qteappro;
                    updateQuantity($pdo, $produit, $newquantity);
                    
                    $pdo->commit();
                    
                    echo "<p class='alert alert-success'>Information Recorded !!!</p>";
                    echo "<script type='text/javascript'>window.location.reload();</script>";
                    
                } catch (Exception $ex) {
                    $pdo->rollback();
                    
                    echo "<p class='alert alert-danger'>" . $ex->getMessage() . "</p>";
                } 
        
        }
            break;
        case 2 : {
                
            try {
                $pdo->beginTransaction();
                
                $approupdate = getAppro($pdo, $idappro);
                
                
                
                $produitupdate = getProduit($pdo, $approupdate->produit);
            
                $oldqteappro  = $approupdate->qteappro;
                $qteproduit = $produitupdate->qtestock;
                $newquantity = ($qteproduit - $oldqteappro ) + $qteapproupdate;
                updateAppro($pdo,$idappro,$dateapproupdate,$qteapproupdate,$prixachatupdate);
                updateQuantity($pdo, $approupdate->produit, $newquantity);
                
                $pdo->commit();
                
                echo "<p class='alert alert-success'>Update Successfull !!!</p>";
               echo "<script type='text/javascript'>window.location.reload();</script>";
                
            } catch (Exception $ex) {
                
                $pdo->rollback();
                echo "<p class='alert alert-danger'>" . $ex->getMessage() . "</p>";
            } 
        
        }
            break;
        case 3 : getListPoduitByStation($pdo,$idstation);
            break;
    }
}



function getListPoduitByStation($pdo,$idstation){
    
    
    $req = $pdo->prepare('SELECT * FROM produit WHERE station =:idstation');
    $req->execute(array("idstation"=>$idstation));
    $produitsStation = $req->fetchAll(PDO::FETCH_OBJ);
    
    foreach ($produitsStation as $pd){
        echo "<option value='$pd->id'>$pd->intitule</option>";
    }
    
}

function addAppro($pdo,$qteappro,$produit,$dateappro,$prixachat) {

    if (!empty($qteappro) && !empty($dateappro) && !empty($prixachat)) {

        $appro = $pdo->prepare('INSERT INTO approvisionnement(qteappro,produit,dateappro,prixachat) '
                . ' VALUES (:qteappro,:produit,:dateappro,:prixachat)');
       $appro->execute(array(
                    'qteappro' => $qteappro,'prixachat'=>$prixachat,'produit' => $produit,'dateappro' => $dateappro)) ;
    }
}

function updateAppro($pdo,$idappro,$dateapproupdate,$qteapproupdate,$prixachatupdate) {

    if (!empty($dateapproupdate) && !empty($qteapproupdate) && !empty($prixachatupdate)) {

        $station = $pdo->prepare('UPDATE  approvisionnement '
                . ' SET dateappro =:dateapproupdate, qteappro =:qteapproupdate,prixachat =:prixachatupdate '
                . ' WHERE id =:idappro ');
        $station->execute(array(
                    "idappro"=>$idappro,
                    "dateapproupdate" => $dateapproupdate,
                    "qteapproupdate"=>$qteapproupdate,
                    "prixachatupdate"=>$prixachatupdate));
        
    }
}