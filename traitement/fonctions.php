<?php


function getUnitesByTypeproduit($pdo){
    
    $sql = "SELECT p.id as id,unite FROM typeproduit tp INNER JOIN produit p ON p.typeproduit = tp.id ";
    $req = $pdo->query($sql);
    $result = $req->fetchAll(PDO::FETCH_OBJ);
    
    return ($result)? $result: null;
}

function getUnite($pdo,$typeproduit){
    
    $sql = "SELECT unite
            FROM typeproduit
            WHERE id =:typeproduit";
    
    
    $req = $pdo->prepare($sql);
    $req->execute(array("typeproduit"=>$typeproduit));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->unite : null;
}

function getAmountPaid($pdo,$idcredit,$datereport){
    $sql = "SELECT ROUND(SUM(montant),2) as amountPaid
            FROM paiementcredit
            WHERE credit =:idcredit AND datepay =:datereport ";
    
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idcredit"=>$idcredit,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);

    return ($result)? $result->amountPaid : null;
}

function getAmountPaidByCustomerByProduit($pdo,$produit,$customer,$moiannee){
    
    $sql = "SELECT SUM(montant) as amountPaid "
            . " FROM paiementcredit "
            . " WHERE credit IN "
            . " (SELECT id FROM "
            . "     credit WHERE produit =:produit AND idcustomer =:customer"
            . "     AND DATE_FORMAT(datecredit,'%Y-%m') =:moiannee "
            . " )";
    
    
    $req = $pdo->prepare($sql);
    $req->execute(array("produit"=>$produit,"customer"=>$customer,"moiannee"=>$moiannee));
    $result = $req->fetch(PDO::FETCH_OBJ);

    return ($result)? $result->amountPaid : null;
    
}

function getAmountPaidByCustomerForMonthYear($pdo,$customer,$moiannee){
    
    $sql = "SELECT SUM(montant) as amountPaid "
            . " FROM paiementcredit "
            . " WHERE credit IN "
            . " (SELECT id FROM "
            . "     credit WHERE  idcustomer =:customer "
            . "     AND DATE_FORMAT(datecredit,'%Y-%m') =:moiannee "
            . " )";
    
    
    $req = $pdo->prepare($sql);
    $req->execute(array("customer"=>$customer,"moiannee"=>$moiannee));
    $result = $req->fetch(PDO::FETCH_OBJ);

    return ($result)? $result->amountPaid : 0;
    
}


function getPrixProduit($pdo,$idproduit){
    $sql ="SELECT prix as prixproduit "
            ." FROM prixproduit pp "
            ." WHERE datefinale is NULL "
            ." AND pp.produit =:idproduit ";
   
        
        $req = $pdo->prepare($sql);
        $req->execute(array("idproduit"=>$idproduit));
        $reqresult = $req->fetch(PDO::FETCH_OBJ);
        
    return ($reqresult)?   $reqresult->prixproduit : null;
    
}

function getPrixProduitFor($pdo,$produit,$datereport){
    
    
    $sqlprixCourant = "SELECT * FROM prixproduit WHERE produit =:produit AND datefinale is Null LIMIT 1 ";
    $reqprixCourant = $pdo->prepare($sqlprixCourant);
    $reqprixCourant->execute(array("produit" => $produit));
    $prixCourant = $reqprixCourant->fetch(PDO::FETCH_OBJ);


    if ($prixCourant) {
        $prixReport = $prixCourant->prix;
        if (!($prixCourant->dateinitiale <= $datereport)) {

            $sqlprixReport = "SELECT * FROM prixproduit "
                    . " WHERE produit =:produit AND datefinale is NOT Null "
                    . " AND (dateinitiale <=:datereport AND datefinale >=:datereport)";
            $reqprixReport = $pdo->prepare($sqlprixReport);
            $reqprixReport->execute(array("produit" => $produit, "datereport" => $datereport));
            $prixReportObj = $reqprixReport->fetch(PDO::FETCH_OBJ);

            $prixReport = ($prixReportObj) ? $prixReportObj->prix : null;
            
        }
        return $prixReport;
    }
    
    return null;
    
}

function updateQuantity($pdo,$produit,$newquantity){

    $sqlproduitupdate = "UPDATE produit SET qtestock =:qtestock"
            . " WHERE id =:produit ";
    $reqproduitupdate = $pdo->prepare($sqlproduitupdate);
    $reqproduitupdate->execute(array("produit"=>$produit,"qtestock"=>$newquantity));
          
}



function getAppro($pdo,$idappro){
    
    $sql = "SELECT * FROM approvisionnement "
                    . " WHERE id =:idappro ";
    $req = $pdo->prepare($sql);
    $req->execute(array("idappro"=>$idappro));
    $result  = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result : null;
    
}

function getApproFor($pdo,$produit,$datereport){
    
    $sql = "SELECT * FROM approvisionnement "
            . " WHERE produit =:produit "
            . " AND dateappro =:dateappro ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("produit"=>$produit,"dateappro"=>$datereport));
    $result  = $req->fetchAll(PDO::FETCH_OBJ);
    
    return ($result)? $result : null;
    
}

function getSumQteApproFor($pdo,$produit,$datereport){
    
    $appros = getApproFor($pdo, $produit, $datereport);
    
    if(!$appros){
        
        return 0;
    }
    
    $sumqteappro = 0; 
    
    foreach ($appros as $ap){
        $sumqteappro += $ap->qteappro;
    }
    
    return $sumqteappro;
}

function getApprosForInterval($pdo,$produit,$date1,$date2){
    
    $sql = "SELECT * FROM approvisionnement "
            . " WHERE produit =:produit "
            . " AND dateappro BETWEEN :date1 AND :date2 ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("produit"=>$produit,"date1"=>$date1,"date2"=>$date2));
    $result  = $req->fetchAll(PDO::FETCH_OBJ);
    
    return ($result)? $result : array();
    
}

function getVentesForInterval($pdo,$produit,$date1,$date2){
    
    $sql = "SELECT * FROM vente "
            . " WHERE produit =:produit "
            . " AND datevente BETWEEN :date1 AND :date2 ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("produit"=>$produit,"date1"=>$date1,"date2"=>$date2));
    $result  = $req->fetchAll(PDO::FETCH_OBJ);
    
    return ($result)? $result : array();
    
}

function getCreditsForInterval($pdo,$produit,$date1,$date2){
    
    $sql = "SELECT * FROM credit "
            . " WHERE produit =:produit "
            . " AND datecredit BETWEEN :date1 AND :date2 ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("produit"=>$produit,"date1"=>$date1,"date2"=>$date2));
    $result  = $req->fetchAll(PDO::FETCH_OBJ);
    
    return ($result)? $result : array();
    
}


function getProduit($pdo,$idproduit){
    
    $sqlproduit = "SELECT * FROM produit "
                    . " WHERE id =:idproduit ";
    $reqproduit = $pdo->prepare($sqlproduit);
    $reqproduit->execute(array("idproduit"=>$idproduit));
    $resultproduit  = $reqproduit->fetch(PDO::FETCH_OBJ);
    
    return ($resultproduit)? $resultproduit : null;
    
}

function getProduitByIntitule($pdo,$intitule){
    
    $sqlproduit = "SELECT * FROM produit "
                    . " WHERE intitule =:intitule ";
    $reqproduit = $pdo->prepare($sqlproduit);
    $reqproduit->execute(array("intitule"=>$intitule));
    $resultproduit  = $reqproduit->fetch(PDO::FETCH_OBJ);
    
    return ($resultproduit)? $resultproduit : null;
    
}

function getProduitUnite($pdo){
    
    $sqlunites = "SELECT p.id as id,tp.intitule as tpintitule, unite FROM typeproduit tp INNER JOIN produit p ON p.typeproduit = tp.id ";
    $requnites = $pdo->query($sqlunites);
    $unites = $requnites->fetchAll(PDO::FETCH_OBJ);
    
    return ($unites) ? $unites : null ;
}


function getCreditsByCustomerByProductForMonthYear($pdo,$customer,$moiannee,$station){
    
    
    $sqlcreditbycustomerformonthyear = "SELECT cm.nom as cusname,cm.id as cmid,pd.intitule as pdname,"
            . " pd.id as pdid,cd.id as idcredit,"
            . " cd.quantite as qtecredit, cd.datecredit as cddatecredit,"
            . " cd.etat as cdetat,cd.prix as prixcredit,"
            . " pd.typeproduit as idtypeproduit,"
            . " DATE_FORMAT(cd.datecredit, '%Y') as year,"
            . " DATE_FORMAT(cd.datecredit, '%Y-%m') as moiannee,"
            . " DATE_FORMAT(cd.datecredit, '%m') as moi "
            . " FROM produit pd INNER JOIN credit cd ON cd.produit = pd.id "
            . " INNER JOIN customer cm ON cd.idcustomer = cm.id "
            . " WHERE  cd.idcustomer =:customer AND pd.station =:station AND DATE_FORMAT(cd.datecredit, '%Y-%m') =:moiannee "
            . " ORDER BY cd.datecredit  ";
    
    
    $reqCreditsByDateForMonthYearAsc = $pdo->prepare($sqlcreditbycustomerformonthyear);
    $params = ["customer"=>$customer,"moiannee"=>$moiannee,"station"=>$station];
    $reqCreditsByDateForMonthYearAsc->execute($params);
    $creditsByDateForMonthYearAsc = $reqCreditsByDateForMonthYearAsc->fetchAll(PDO::FETCH_OBJ);
    
    return ($reqCreditsByDateForMonthYearAsc) ? $creditsByDateForMonthYearAsc : array();
    
}



function getTestsTankFor($pdo,$typetank,$datereport){
    $sql = "SELECT tests FROM indextank it WHERE it.datetank =:datereport"
            . "  AND it.tank = (SELECT id FROM tank WHERE typetank =:typetank ) LIMIT 1";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("typetank"=>$typetank,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->tests  : null ;
}

function getPumpSalesFor($pdo,$typetank,$datereport){
    $sql = "SELECT ROUND(SUM(indexfinal - indexinitial),2) as totalSales
      FROM indexpompe
      WHERE idpompe 
      IN (SELECT id FROM pompe
            WHERE tank
            IN
            (SELECT id FROM tank
              WHERE typetank =:typetank))
      GROUP BY dateindex
      HAVING dateindex =:datereport ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("typetank"=>$typetank,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);

    $othersalesQte = getProductExtraSalesQteFor($pdo,$typetank,$datereport);
    $tests = getTestsTankFor($pdo, $typetank, $datereport);
    
    return ($result)? $result->totalSales - $othersalesQte - $tests : null ;
}



function getProductExtraSalesQteFor($pdo,$intitule,$datereport){
    $sqlothersales = "SELECT SUM(qtevendu) as totalSalesQte "
            . " FROM vente "
            . " WHERE datevente =:datereport AND produit IN (SELECT id FROM produit WHERE intitule =:intitule) ";
    
    
    $reqothersales = $pdo->prepare($sqlothersales);
    $reqothersales->execute(array("intitule"=>$intitule,"datereport"=>$datereport));
    $resultothersales = $reqothersales->fetch(PDO::FETCH_OBJ);
    
    return ($resultothersales) ? $resultothersales->totalSalesQte : null;
}

function getPrixProduitOtherFor($pdo, $produit, $datereport){
    
     $sqlotherprix = "SELECT DISTINCT prixvente "
            . " FROM vente "
            . " WHERE datevente =:datereport AND produit =:produit"
             . " LIMIT 1";
    
    
    $reqotherprix = $pdo->prepare($sqlotherprix);
    $reqotherprix->execute(array("produit"=>$produit,"datereport"=>$datereport));
    $resultotherprix = $reqotherprix->fetch(PDO::FETCH_OBJ);
    
    return ($resultotherprix) ? $resultotherprix->prixvente : null;
    
}


function getPumpSales($pdo,$station,$datereport){
    $sql = "SELECT ROUND(SUM(indexfinal - indexinitial),2) as totalSales
      FROM indexpompe
      WHERE idpompe 
      IN (SELECT id FROM pompe
            WHERE tank
            IN
            (SELECT id FROM tank
              WHERE station =:idstation))
      GROUP BY dateindex
      HAVING dateindex =:datereport ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$station,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->totalSales : null ;
}
function getPumpSalesTanks($pdo,$idtank,$datereport){
    $sql = "SELECT ROUND(SUM(ip.indexfinal - ip.indexinitial),2) as  pumpSales "
            ." FROM indexpompe ip INNER JOIN pompe p ON ip.idpompe = p.id INNER JOIN tank tk ON tk.id = p.tank "
            ." WHERE ip.dateindex =:datereport"
            ." GROUP BY p.tank"
            ." HAVING p.tank =:idtank ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idtank"=>$idtank,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->pumpSales : null;
}
function getTotalCreditPayment($pdo,$idstation,$datereport){
    $sql = "SELECT ROUND(SUM(montant),2) as totalCreditPayment
            FROM paiementcredit
            WHERE datepay =:datereport 
            and credit IN(
                    SELECT id FROM credit WHERE  produit 
                    IN(SELECT id FROM produit WHERE station =:idstation)
                 ) ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$idstation,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->totalCreditPayment : null;
}
function getTotalCreditSales($pdo,$idstation,$datereport){
    $sql = "SELECT ROUND(SUM(quantite * prix),2) as totalCreditSales
            FROM credit
            WHERE datecredit =:datereport and produit IN(SELECT id FROM produit WHERE station =:idstation) 
          ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$idstation,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->totalCreditSales : null;
}
function getTotalExpenses($pdo,$idstation,$datereport){
    $sql = "SELECT ROUND(SUM(montant),2) as expenses   
                FROM depenses
                WHERE datedepense =:datereport and station =:idstation 
            ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$idstation,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->expenses : null ;
}
function getTotalProductSales($pdo,$idstation,$datereport){
    $sql = "SELECT ROUND(SUM(qtevendu * prixvente),2) as totalProductSales
                FROM vente
                WHERE datevente =:datereport 
                  and produit IN(SELECT id FROM produit WHERE station =:idstation) 
              ";
    
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$idstation,"datereport"=>$datereport));
    $result = $req->fetch(PDO::FETCH_OBJ);
    
    return ($result)? $result->totalProductSales : null;
}


function getReportFor($pdo,$idstation,$datereport){
    
    $sqlsearchreport = "SELECT * FROM dailyreport WHERE dateactivity =:datereport AND station =:idstation";
    $reqsearchreport = $pdo->prepare($sqlsearchreport);
    $reqsearchreport->execute(array("idstation"=>$idstation,"datereport"=>$datereport));
    $report = $reqsearchreport->fetch(PDO::FETCH_OBJ);
    
    return $report;
}


function existReportFor($pdo,$idstation,$datereport){
    
    $report = getReportFor($pdo,$idstation, $datereport);
    
    if($report){
        return true;
    }
    
    return false;
}


function getListTanksIndexes($pdo, $idstation) {
    $q = $pdo->prepare("SELECT *, it.id as id  "
            . "FROM indextank it INNER JOIN tank t ON it.tank = t.id "
            . "WHERE t.station =:idstation ORDER BY it.datetank DESC ");

    $q->execute(array("idstation" => $idstation));
    $tanksindex = $q->fetchAll();

    return $tanksindex;
}

function getListTanksIndexesFor($pdo, $date, $idstation) {
    $q = $pdo->prepare("SELECT *, it.id as id  "
            . "FROM indextank it INNER JOIN tank t ON it.tank = t.id "
            . "WHERE datetank =:date "
            . "AND t.station =:idstation");

    $q->execute(array("date" => $date, "idstation" => $idstation));
    $tanksindex = $q->fetchAll();

    return $tanksindex;
}

function getListTank($pdo, $idstation) {

    $q = $pdo->prepare("SELECT *  "
            . "FROM tank "
            . "WHERE station =:idstation");

    $q->execute(array("idstation" => $idstation));
    $tanks = $q->fetchAll();

    return $tanks;
}
