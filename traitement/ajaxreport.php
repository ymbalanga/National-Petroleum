<?php

session_start();
include '../db.php';
require './fonctions.php';

if (isset($_POST["action"])) {
    $action = $_POST["action"];
    switch ($action) {
        case 1 : {
               include '../cashFlowSummary.php';
        };
            break;
        case 2 : {
                
                try {
                    extract($_POST);
                    
                    if(existReportFor($pdo,$data[0],$data[3])){
                        echo "<p class='alert alert-warning uppercase'>Report for this date is already archived! </p>";
                        exit();
                    }
                    archiveCashFlowSummaray($pdo,$data);
                    echo "<p class='alert alert-success'>Report archived ! </p>";
                    
                } catch (Exception $ex) {
                    echo "<p class='alert alert-danger'>Error : <br/>".$ex->getMessage()."</p>";
                }
                
                
        };
           
    }
}


function archiveCashFlowSummaray($pdo,$data) {

    $sqlreport = "INSERT INTO dailyreport (dateactivity,taux,station,totalcashsales,expenses,"
            . "totalcreditsales,totalcreditpayment,actualcashbank,coefficientmult) "
            . "VALUES (:dateactivity,:taux,:station,:totalcashsales,:expenses,"
            . ":totalcreditsales,:totalcreditpayment,:actualcashbank,:coefficientmult)";
    
    $reqreport = $pdo->prepare($sqlreport);
    $reqreport->execute(array(
            "station"=>$data[0],
            "taux"=>$data[1],
            "coefficientmult"=>$data[2],
            "dateactivity"=>$data[3],
            "totalcashsales"=>$data[4],
            "expenses"=>$data[5],
            "totalcreditsales"=>$data[6],
            "totalcreditpayment"=>$data[7],
            "actualcashbank"=>$data[8]
            
    ));
    $reqreport->closeCursor();
    
}