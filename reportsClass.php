<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require './traitement/fonctions.php';


class Report{
    private $pdo;
    private $datereport;
    
    
    public function __construct($pdo,$date) {
        ;
    }
    
    public function get(){}
}

$station = $_SESSION['station'];
$datecourante = date('Y-m-d');


//Recuperation de la date report

if (isset($_GET["datereport"]) && !empty($_GET["datereport"])) {
    $datecourante = $_GET["datereport"];
}

//recherche du taux utilisé à la date datereport

try {


    $sqltauxCourant = "SELECT * FROM taux WHERE station =:idstation AND datefinale is Null LIMIT 1 ";
    $reqTauxCourant = $pdo->prepare($sqltauxCourant);
    $reqTauxCourant->execute(array("idstation" => $station));
    $tauxCourant = $reqTauxCourant->fetch(PDO::FETCH_OBJ);


    if ($tauxCourant) {
        $tauxReport = $tauxCourant->valeurtaux;
        if (!($tauxCourant->dateinitiale <= $datecourante)) {

            $sqltauxReport = "SELECT * FROM taux "
                    . " WHERE station =:idstation AND datefinale is NOT Null "
                    . " AND (dateinitiale <=:datereport AND datefinale >=:datereport)";
            $reqTauxReport = $pdo->prepare($sqltauxReport);
            $reqTauxReport->execute(array("idstation" => $station, "datereport" => $datecourante));
            $tauxReportObj = $reqTauxReport->fetch(PDO::FETCH_OBJ);

            $tauxReport = ($tauxReportObj) ? $tauxReportObj->valeurtaux : null;
        }
    }
} catch (Exception $ex) {
    
    echo "Ligne : ".$ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}


// code sql du report pump

$sqlPump = "SELECT p.intitule as pompeIntitule,indexinitial,indexfinal,"
        . " ROUND(SUM(indexfinal - indexinitial),2) as salesPump "
        . " FROM indexpompe ip INNER JOIN pompe p ON ip.idpompe = p.id"
        . " WHERE p.tank IN (SELECT id FROM tank WHERE station =:idstation)"
        . " AND dateindex =:datereport "
        . " GROUP BY pompeIntitule";
try {
    $reqDailyPump = $pdo->prepare($sqlPump);
    $reqDailyPump->execute(array('datereport' => $datecourante, 'idstation' => $station));
    $dailypumps = $reqDailyPump->fetchAll(PDO::FETCH_OBJ);

// total des totaux Sales pump (From mysql fonction)
    
    $TotalPumpSales = getPumpSales($pdo, $station, $datecourante);

    // code sql du report tanks
    $sqltanksDaily = "SELECT tk.id as idtank, idt.openstock as open,idt.tests as test,"
            . " idt.purchase as purchase,idt.dip as dip,"
            . " tk.intitule as tankname " 
            . " FROM indextank idt INNER JOIN tank tk ON idt.tank = tk.id"
            . " WHERE idt.datetank =:datetank "
            . " AND tk.station =:idstation ";
    $reqtanksDaily = $pdo->prepare($sqltanksDaily);
    $reqtanksDaily->execute(array(
        'datetank' => $datecourante,
        'idstation' => $station
    ));
    $dailyTanks = $reqtanksDaily->fetchAll(PDO::FETCH_OBJ);

    

    //EXPENSES
    $reqexpenses = $pdo->prepare("SELECT id,intitule,montant,DATE_FORMAT(datedepense, '%Y-%m-%d') AS datedepense "
            . "FROM depenses "
            . "WHERE datedepense =:datereport "
            . "ORDER BY id DESC");
    $reqexpenses->execute(array("datereport" => $datecourante));
    $expenses = $reqexpenses->fetchAll(PDO::FETCH_OBJ);

    } catch (Exception $ex) {
    
    echo "Ligne : ".$ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}





//  CODES CASH SALES SUMMARY
/**
 * Pour la station selectionnee
  1.recuperer tous les produits

  pour la date selectionnee

  3.pour chaque produit grouper par type de produit:
  -recuperer le prix en vigueur
  -si le type de produit est carburant alors recuperer la somme de litres vendues pour chaque type de caerburant
  -sinon recuperer la somme des unites vendues
  4.calculer le produit des unites vendues par le prix
 */
try {

    $sqlproduitsGaz = "SELECT pd.intitule as intitule, tpd.id as tpdid,pd.id as idproduit FROM produit pd "
            . "INNER JOIN typeproduit tpd ON tpd.id = pd.typeproduit "
            . "WHERE station =:idstation AND tpd.intitule ='GAZ'  ";

    $sqlAutresproduits = "SELECT tpd.intitule as tpintitule, tpd.id as tpdid FROM produit pd "
            . "INNER JOIN typeproduit tpd ON tpd.id = pd.typeproduit "
            . "WHERE station =:idstation AND tpd.intitule !='GAZ' "
            . "GROUP BY typeproduit ";

    $reqproduitsGaz = $pdo->prepare($sqlproduitsGaz);
    $reqproduitsGaz->execute(array("idstation" => $station));
    $produitsGaz = $reqproduitsGaz->fetchAll(PDO::FETCH_OBJ);

    $reqAutresproduits = $pdo->prepare($sqlAutresproduits);
    $reqAutresproduits->execute(array("idstation" => $station));
    $Autresproduits = $reqAutresproduits->fetchAll(PDO::FETCH_OBJ);
} catch (Exception $ex) {
    echo $ex->getMessage();
}

// code sql du report
$sqlcustomer = "SELECT * FROM customer ORDER BY id DESC";
$reqcustomer = $pdo->query($sqlcustomer);
$customers = $reqcustomer->fetchAll(PDO::FETCH_OBJ);
?>
<style type="text/css">
    .table-cashflow th{
        background: #169f2c;
        color: #FFFFCC;
        font-weight: bold;
    }
    .message{
        
    }
    .money{
        margin-left: 5%;
        font-weight: bold;
    }
</style>
<script type="text/javascript">
    function day_click(short) {
        window.location.href = "reports.php?datereport=" + short;
    }
</script>
<div class="grid">
    <div class="row cells5">




        <div class="cell colspan5 well">
            <h3 class="text-light text-center text-shadow">REPORTS INTERFACES</h3>
            <div class="tabcontrol2" data-role="tabcontrol">
                <ul class="tabs">
                    <li><a href="#frame_5_day">DAILY REPORT</a></li>
                    <li><a href="#frame_5_cash">CASH FLOW SUMMARY</a></li>
                    <li><a href="#frame_5_month">MONTHLY REPORT</a></li>
                    <!--                    <li><a href="#frame_5_month">Monthly</a></li>
                                        <li><a href="#frame_5_year">Yearly</a></li>-->

                </ul>
                <div class="frames">
                    <div class="frame" id="frame_5_day">
                        <div class="row cells4">
                            <div class="cell">
                                <div class="darcula" data-role="calendar" data-day-click="day_click">></div>

                            </div>
                            <div class="cell colspan3">

                                <?php
                                if ($tauxReport == NULL || $datecourante > date("Y-m-d")) {
                                    ?>

                                    <div class="alert alert-danger">There is not report of this date</div>
                                    <?php
                                    exit();
                                }
                                ?>

                                <h4 class="text-light text-left text-shadow">DAILY REPORT OF THIS DATE <strong><?= $datecourante; ?></strong>
                                </h4>
                                <button type="submit" onclick="printContent('DailyReportPrint')" class='button small-button warning'>Print</button>
                                <div id="DailyReportPrint">
                                    <div id="DailyReportPrint" class="accordion" data-role="accordion" data-close-any="true">











                                        <div class="frame">
                                            <div class="heading">PUMPS<span class="mif-gas-station icon"></span></div>
                                            <div class="content">
                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">PUMPS</th>
                                                            <th class="">INITIAL</th>
                                                            <th class="">FINAL</th>
                                                            <th class="">SALES</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        foreach ($dailypumps as $dailypump) {
                                                            ?>
                                                            <tr>
                                                                <td><?= $dailypump->pompeIntitule; ?></td>
                                                                <td><?= $dailypump->indexinitial; ?></td>
                                                                <td><?= $dailypump->indexfinal; ?></td>
                                                                <td><?= $dailypump->salesPump; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="3">TOTAL</td><td><?= $TotalPumpSales; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>



















                                        <div class="frame">
                                            <div class="heading">TANKS<span class="mif-truck icon"></span></div>
                                            <div class="content">



                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">Products</th>
                                                            <th class="">Opening Stock/Ltrs</th>
                                                            <th class="">Sales</th>
                                                            <th class="">Tests</th>
                                                            <th class="">Purchase</th>
                                                            <th class="">Closing Stock/Ltrs</th>
                                                            <th class="">Dip/Ltrs</th>
                                                            <th class="">Dif/Ltrs</th>
                                                            <th class="">Diff in %</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $STopen = 0;
                                                        $STsales = 0;
                                                        $STtest = 0;
                                                        $STpurchase = 0;
                                                        $STclose = 0;
                                                        $STdip = 0;
                                                        $STdif = 0;
                                                        $STdiffin = 0;
                                                        foreach ($dailyTanks as $dailyTank) {
                                                            
                                                            
                                                            $close = 0;
                                                            $dif = 0;
                                                            $diffin = 0;
                                                            
                                                            
                                                            $produit = getProduitByIntitule($pdo, $dailyTank->tankname);
                                
                                                            $appros = getApproFor($pdo, $produit->id, $datecourante);
                                                            $purchasetk = 0;
                                                            if($appros != null ){
                                                                foreach ($appros as $ap){
                                                                    $purchasetk += $ap->qteappro;
                                                                }
                                                            }
                                                            
                                                            // code total par lignes
                                                            // calcul avec les formules
                                                            $sales = getPumpSalesTanks($pdo, $dailyTank->idtank, $datecourante);
                                                            $close = round($dailyTank->open - $sales + $dailyTank->test + $purchasetk, 2);
                                                            $dif = round($dailyTank->dip - $close, 2);
                                                            $diffin = round(($dif / $sales) * 100, 2);
                                                            // code calcul sous totaux par lignes
                                                            $STopen += $dailyTank->open;
                                                            $STsales += $sales;
                                                            $STtest += $dailyTank->test;
                                                            $STpurchase += $purchasetk;
                                                            $STclose += $close;
                                                            $STdip += $dailyTank->dip;
                                                            $STdif += $dif;
                                                            $STdiffin += $diffin;
                                                            ?>
                                                            <tr>
                                                                <td><?= $dailyTank->tankname; ?></td>
                                                                <td><?= $dailyTank->open; ?></td>
                                                                <td><?= $sales; ?></td>
                                                                <td><?= $dailyTank->test; ?></td>
                                                                <td><?= $purchasetk; ?></td>
                                                                <td><?= $close; ?></td>
                                                                <td><?= $dailyTank->dip; ?></td>
                                                                <td><?= $dif; ?></td>
                                                                <td><?= $diffin; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td>TOTAL</td>
                                                            <td><?= $STopen; ?></td>
                                                            <td><?= $STsales; ?></td>
                                                            <td><?= $STtest; ?></td>
                                                            <td><?= $STpurchase; ?></td>
                                                            <td><?= $STclose; ?></td>
                                                            <td><?= $STdip; ?></td>
                                                            <td><?= $STdif; ?></td>
                                                            <td><?= $STdiffin; ?></td>



                                                        </tr>

                                                    </tbody>
                                                </table>





                                            </div>
                                        </div>






                                        <div class="frame">
                                            <div class="heading">SPECIALITIES MOUVEMENT<span class="mif-gas-station icon"></span></div>
                                            <div class="content">
                                                <div class="table-responsive">
                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">Name of Product</th>
                                                            <th class="">Opening Stock (Pcs)</th>
                                                            <th class="">Purchase (Pcs)</th>
                                                            <th class="">Sales (Pcs)</th>
                                                            <th class="">Closing Stock (Pcs)</th>
                                                            <th class="">Selling Price</th>
                                                            <th class="">AMOUNT </th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        try {

                                                             //Recuperer tous les credits en cours et les paiements effectués à la date courante
                                                            $sqlspecialities = "SELECT *,p.id as idproduit,"
                                                                        ." SUM(v.qtevendu) AS sales,v.prixvente as sellingprice "
                                                                        ." FROM produit p INNER JOIN vente v ON p.id = v.produit "
                                                                        ." WHERE  p.station  =:idstation "
                                                                        ." AND p.typeproduit IN (SELECT id FROM typeproduit WHERE LOWER(intitule) = 'lubes') " 
                                                                        ." GROUP BY v.produit "
                                                                        ." HAVING v.datevente =:datereport ";

                                                            $reqspecialities = $pdo->prepare($sqlspecialities);
                                                            $reqspecialities->execute(
                                                                    array(
                                                                        "datereport" => $datecourante,
                                                                        "idstation" => $station
                                                                    )
                                                            );
                                                            $specialities = $reqspecialities->fetchAll(PDO::FETCH_OBJ);


                                                            $stAmountspecialities = 0;
                                                            $stsalesspecialities = 0;
                                                            $stclosingspecialities = 0;
                                                            $stqtestock = 0;
                                                            
                                                            foreach ($specialities as $spt) {
                                                                
                                                                $purchase = getSumQteApproFor($pdo,$spt->idproduit,$datecourante);
                                                                
                                                                $amount = round($spt->sales * $spt->sellingprice,2);
                                                                
                                                                
                                                                //Calcul de la quantite en stock à la date datereport
                                                                //la quantite en stock actuelle - les approvisionnement 
                                                                //+ les ventes + les credits actuels jusqu'à la date datereport
                                                                $produit = $spt->idproduit;
                                                                $date1 = $datecourante;
                                                                $date2 = date("Y-m-d");
                                                                $appros = getApprosForInterval($pdo, $produit, $date1, $date2);
                                                                $ventes = getVentesForInterval($pdo, $produit, $date1, $date2);
                                                                $credits = getCreditsForInterval($pdo, $produit, $date1, $date2);
//                                                                var_dump($produit);
//                                                                var_dump($date1);
//                                                                var_dump($date2);
//                                                                var_dump($ventes);
//                                                                var_dump($credits);
//                                                                var_dump($appros);exit();
                                                                $qtesappro = 0;
                                                                $qtesvente = 0;
                                                                $qtescredit = 0;
                                                                foreach ($appros as $ap){
                                                                    $qtesappro += $ap->qteappro;
                                                                }
                                                                foreach ($ventes as $v){
                                                                    $qtesvente += $v->qtevendu;
                                                                }
                                                                foreach ($credits as $c){
                                                                    $qtescredit += $c->quantite;
                                                                }
                                                                
                                                                $qtestock = ($spt->qtestock - $qtesappro + $qtesvente + $qtescredit) ;
                                                                
                                                                $stAmountspecialities += $amount;
                                                                $stsalesspecialities += $spt->sales;
                                                                $stqtestock += $qtestock;
                                                                $closingstock = ($qtestock + $purchase - $spt->sales);
                                                                $stclosingspecialities += $closingstock;
                                                                ?>
                                                                <tr>
                                                                    <td><?= $spt->intitule; ?></td>
                                                                    <td><?= $qtestock; ?></td>
                                                                    <td><?= $purchase; ?></td>
                                                                    <td><?= $spt->sales; ?></td>
                                                                    <td><?= $closingstock; ?></td>
                                                                    <td><?= $spt->sellingprice; ?> <span class="money">CDF</span></td>
                                                                    <td><?= $amount; ?> <span class="money">CDF</span></td>

                                                                </tr>
                                                                <?php
                                                            }
                                                        } catch (Exception $e) {
                                                            echo $e->getMessage();
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>TOTAL</td>
                                                            <td><?= $stqtestock; ?></td>
                                                            <td></td>
                                                            <td><?= $stsalesspecialities; ?></td>
                                                            <td><?= $stclosingspecialities; ?></td>
                                                            <td></td>
                                                            <td><?= $stAmountspecialities; ?> <span class="money">CDF</span> </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </div>



                                        <div class="frame">
                                            <div class="heading">CREDITS AND PAYMENTS STATUS<span class="mif-gas-station icon"></span></div>
                                            <div class="content">
                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">CUSTOMER</th>
                                                            <th class="">PRODUIT</th>
                                                            <th class="">QUANTITY</th>
                                                            <th class="">PRIZES</th>
                                                            <th class="">AMOUNT CREDITED (FC)</th>
                                                            <th class="">AMOUNT PAID (FC)</th>

                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        try {

                                                             //Recuperer tous les credits en cours et les paiements effectués à la date courante
                                                            $sqlcredit = "SELECT cdt.id as idcredit, cust.nom as cusnom, pd.intitule as pdnom,cdt.quantite as qtecredit"
                                                                    . ",cdt.prix as prixcredit,(cdt.quantite * cdt.prix) as amountcredited "
                                                                    . " FROM credit cdt INNER JOIN customer cust ON cdt.idcustomer = cust.id"
                                                                    . " INNER JOIN produit pd ON pd.id = cdt.produit"
                                                                    . " WHERE cdt.datecredit =:datereport"
                                                                    . " AND pd.station  =:idstation ";

                                                            $reqreportcredit = $pdo->prepare($sqlcredit);
                                                            $reqreportcredit->execute(
                                                                    array(
                                                                        "datereport" => $datecourante,
                                                                        "idstation" => $station
                                                                    )
                                                            );
                                                            $reportcredit = $reqreportcredit->fetchAll(PDO::FETCH_OBJ);

                                                           
                                                            $stAmountcredited = 0;
                                                            $stAmountPaid = 0;
                                                            foreach ($reportcredit as $rpc) {
                                                                
                                                                $amountPaid = getAmountPaid($pdo, $rpc->idcredit, $datecourante);
                                                                
                                                                $stAmountcredited += $rpc->amountcredited;
                                                                $stAmountPaid += $amountPaid;
                                                                ?>
                                                                <tr>
                                                                    <td><?= $rpc->cusnom; ?></td>
                                                                    <td><?= $rpc->pdnom; ?></td>
                                                                    <td><?= $rpc->qtecredit; ?></td>
                                                                    <td><?= $rpc->prixcredit; ?></td>
                                                                    <td><?= $rpc->amountcredited; ?></td>
                                                                    <td><?= $amountPaid; ?></td>

                                                                </tr>
                                                                <?php
                                                            }
                                                        } catch (Exception $e) {
                                                            echo $e->getMessage();
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td colspan="4">TOTAL</td>
                                                            <td><?= $stAmountcredited; ?></td>
                                                            <td><?= $stAmountPaid; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                         <div class="frame">
                                            <div class="heading">EXPENSES SUMMARY<span class="mif-home icon"></span></div>
                                            <div class="content">
                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                    
                                                        <tr>
                                                            <th class="">REASON</th>
                                                            <th class="">AMOUNT</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $totalExpenses = 0;
                                                        foreach ($expenses as $exp) {

                                                            $totalExpenses += $exp->montant;
                                                            ?>
                                                            <tr>
                                                                <td><?= $exp->intitule; ?></td>
                                                                <td><?= $exp->montant; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                        <tr>
                                                            <td colspan="">TOTAL</td>
                                                            <td><?= $totalExpenses; ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>


                                       <div class="frame">
                                            <div class="heading">CASH SALES SUMMARY<span class="mif-home icon"></span></div>
                                            <div class="content">
                                                <!-- Gaz Sales Summary -->
                                                  
                                                 <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">PRODUCTS</th>
                                                            <th class="">QUANTITY/UNITS</th>
                                                            <th class="">Prize</th>
                                                            <th class="">TOTAL FC</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        
                                                        
                                                        
                                                        try {
                                                            //total general des ventes
                                                            $totalCashSales = 0;
                                                            
                                                            $STotalSalesGaz = 0;
                                                            $STotalQteGaz = 0;
                                                            
                                                            //Vente normal de Gaz
                                                            foreach ($produitsGaz as $pd) {
                                                                $unite = getUnite($pdo, $pd->tpdid);
  
                                                                $totalQteSales = getPumpSalesFor($pdo, $pd->intitule, $datecourante);
                                                                $prixgaz = getPrixProduitFor($pdo, $pd->idproduit, $datecourante);
                                                                $salesgaz = $totalQteSales * $prixgaz;
  
                                                                $STotalQteGaz += $totalQteSales;
                                                                $STotalSalesGaz += $salesgaz;
                                                                
                                                                
                                                                ?>

                                                                <tr>
                                                                    <td><?= $pd->intitule; ?></td>
                                                                    <td><?= $totalQteSales; ?> [ <?= $unite; ?> ]</td>
                                                                    <td><?= $prixgaz; ?></td> 
                                                                    <td><?= $salesgaz; ?></td> 
                                                                </tr>

                                                                <?php
                                                            }
                                                        
                                                            
                                                            //Vente de gaz avec reduction de prix
                                                            foreach ($produitsGaz as $pd){
                                                                
                                                                $totalQteSales = getProductExtraSalesQteFor($pdo, $pd->intitule, $datecourante);
                                                                $prixgaz = getPrixProduitOtherFor($pdo, $pd->idproduit, $datecourante);
                                                                $salesgaz = $totalQteSales * $prixgaz;
                                                            
                                                                $intitule = ($pd->intitule == 'PMS')? "CLIENT PMS [ KADAFFI ]" : "CLIENT AGO [ KADAFFI ]";
                                                                
                                                                
                                                                   $STotalQteGaz += $totalQteSales;
                                                                   $STotalSalesGaz += $salesgaz;

                                                                   
                                                                   ?>

                                                                   <tr>
                                                                       <td><?= $intitule ?></td>
                                                                       <td><?= $totalQteSales; ?> [ <?= $unite; ?> ]</td>
                                                                       <td><?= $prixgaz; ?></td> 
                                                                       <td><?= $salesgaz; ?></td> 
                                                                   </tr>

                                                                   <?php
                                                            }
                                                            
                                                            
                                                            $totalCashSales += $STotalSalesGaz;
                                                            
                                                            } catch (Exception $ex) {
                                                            echo $ex->getMessage();
                                                        }
                                                        
                                                        
                                                        
                                                                    
                                                        ?>
                                                        <tr>
                                                            <td>TOTAL</td>
                                                            <td><?= $STotalQteGaz ?></td>
                                                            <td></td>
                                                            <td><?= $STotalSalesGaz ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                
                                                
                                                <!-- fin Gaz -->
                                                <table class="table table-bordered table-condensed table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th class="">PRODUCTS</th>
                                                            <th class="">QUANTITY/UNITS</th>
                                                            <th class="">TOTAL FC</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        
                                                        try {
                                                            
                                                            $STotalSales = 0;
                                                            $STotalQte = 0;
                                                            
                                                            //vente des autres produits, autre que les gaz
                                                            foreach ($Autresproduits as $pd) {
                                                                $unite = getUnite($pdo, $pd->tpdid);
                                                                $sqlqteTotalSales = "SELECT SUM(prixvente * qtevendu) as sales, SUM(qtevendu) as qteTotal "
                                                                        . " FROM vente WHERE datevente =:datereport "
                                                                        . " AND produit IN "
                                                                        . " (SELECT id FROM produit WHERE station =:idstation AND typeproduit =:idtypeproduit) ";
                                                                $reqqteTotalSales = $pdo->prepare($sqlqteTotalSales);
                                                                $reqqteTotalSales->execute(
                                                                        array("datereport" => $datecourante,
                                                                            "idtypeproduit" => $pd->tpdid,
                                                                            "idstation" => $station));
                                                                $qteTotalSales = $reqqteTotalSales->fetch(PDO::FETCH_OBJ);

                                                                $STotalSales += $qteTotalSales->sales;
                                                                $STotalQte += $qteTotalSales->qteTotal;
                                                                
                                                                
                                                                ?>

                                                                <tr>
                                                                    <td><?= $pd->tpintitule; ?></td>
                                                                    <td><?= $qteTotalSales->qteTotal; ?> [ <?= $unite; ?> ]</td>
                                                                    <td><?= $qteTotalSales->sales; ?></td> 
                                                                </tr>

                                                                <?php
                                                            }
                                                            
                                                            $totalCashSales += $STotalSales;
                                                            
                                                        } catch (Exception $ex) {
                                                            echo $ex->getMessage();
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td>TOTAL</td>
                                                            <td><?= $STotalQte ?></td>
                                                            <td><?= $STotalSales ?></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table class="table table-condensed table-striped table-bordered " style="font-size:23px;">
                                                    
                                                    <thead>
                                                        <tr>
                                                            <th colspan="3">TOTAL CASH SALES</th>
                                                            
                                                        </tr>
                                                        
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                           <td><?= $STotalSalesGaz ?></td>
                                                           <td><?= $STotalSales ?></td>
                                                           <td><span class="label label-success"><?= $totalCashSales; ?></span></td>
                                                        </tr>
                                                        
                                                    </tbody>
                                                    
                                                </table>
                                            </div>
                                        </div>





                                    </div>                                
                                </div>
                            </div>

                        </div>
                    </div>





                    <div class="frame" id="frame_5_cash">
                        <div class="grid">
                           
                            <div class="row cells6">
                                <div class="cell colspan2 well">
                                    
                                    <?php 
                                        
                                        $report = getReportFor($pdo, $station, $datecourante);
                                        $disabledACB = "";
                                        $disabledCF = "";
                                        $archive = false;
                                        
                                        $coefficient = null;
                                        $actualCashBanked = null;
                                        
                                        if($report){
                                            $coefficient = $report->coefficientmult;
                                            $actualCashBanked = $report->actualcashbank;
                                            
                                            $disabledACB = "disabled";
                                            $disabledCF = "disabled";
                                            $archive = true;
                                        }
                                    
                                    ?>
                                    
                                    <table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>PARAMETERS</th>
                                                <th>VALUES</th>
                                            </tr>
                                            
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>EXCHANGE RATE</td>
                                                <td><?= $tauxReport ;?> CDF</td>
                                            </tr>
                                            <tr>
                                                <td>CASH IN THE BANK ($)</td>
                                                <td><input type="text" id="coefficient" value="<?= $coefficient ;?>" <?= $disabledCF ;?>/></td>
                                            </tr>
                                            <tr>
                                                <td>ACTUAL CASH BANKED (CDF)</td>
                                                <td><input type="text" id="actualCashBanked" value="<?= $actualCashBanked ;?>" <?= $disabledACB ;?>/></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <input type="text" value="<?= $archive ;?>" id="archive" class="hidden"/>
                                    <input type="text" value="<?= $tauxReport ;?>" id="taux" class="hidden"/>
                                    <input type="text" value="<?= $totalCashSales ;?>" id="totalCashSales" class="hidden"/>
                                    <input type="text" value="<?= $datecourante ;?>" id="datereport" class="hidden"/>
                                    <input type="text" value="<?= $station ;?>" id="station" class="hidden"/>
                                    <div class="row cells6">
                                        <div class="cell colspan2">
        
                                          <button type="button" id="btn-view" class="btn btn-success full-size <?= ($archive == true) ? 'hidden' : '' ; ?>">View Report</button>  
                                        </div>
                                        <div class="cell colspan4 hidden" id="msgvr">
                                            <p class='alert alert-danger uppercase'>cash in the bank and actual cash banked can't be null</p>
                                        </div>
                                    </div>
                                    
                                </div>
                                <div class="cell colspan4 ">
                                    <img src="img/ajax_loader.gif" id="loading" class="hidden"/> 
                                    <div id="cashFlow">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>



















                        <div class="frame" id="frame_5_month">
                        <div class="grid">
                            <div class="row cells5">
                                <div class="cell">

                                    <div class="row">
                                        <div class="cell">
                                            <label class="text-light" for="intitule">From Date:</label>
                                            <div class="input-control text full-size" data-role="datepicker">
                                                <input type="text" id="fromdate"/>
                                                <button class="button"><span class="mif-calendar"></span></button>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="row">
                                        <div class="cell">
                                            <label class="text-light" for="intitule">To Date:</label>
                                            <div class="input-control text full-size" data-role="datepicker">
                                                <input type="text" id="todate"/>
                                                <button class="button"><span class="mif-calendar"></span></button>
                                            </div>
                                        </div> 
                                    </div>
                                    <button id="fromvalide" class="button info">Validate</button>

                                </div>







                                <div class="cell colspan4">
                                    <div id="resumonthly">
                                        <img src="img/ajax_loader.gif" id="loading" class="hidden"/> 
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>















                </div>
            </div>


        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#fromvalide").click(function () {
            var fromdate = $("#fromdate").val();
            var todate = $("#todate").val();
//            alert("FROM:"+fromdate+",TODATE:"+todate);return false;
            if (fromdate !== '' && todate !== '') {
                $.ajax({
                    method: "post",
                    url: "traitement/ajaxreportMonthly.php",
                    data: {fromdate: fromdate, todate: todate},
                    beforeSend: function () {
                        $("#loading").removeClass("hidden");
                    },
                    success: function (data) {
                        $("#resumonthly").html(data);
                         $("#loading").addClass("hidden");
                    }
                });

            } else {
                $("#resumonthly").html("<p class='alert alert-danger'>Fields required   !!!</p>");
            }
        });
    });

</script>
<script  type="text/javascript">
    
    
    

    $(document).ready(function (){
        activerMenu("reports");
        
        
        
        $("#btn-view").click(function(){
            
            
            var idstation = $("#station").val();
            var datereport = $("#datereport").val();
            var taux = $("#taux").val();
            var coefficient = $("#coefficient").val();
            var totalCashSales = $("#totalCashSales").val();
            var actualCashBanked = $("#actualCashBanked").val();
            var archive = $("#archive").val();
           
//           alert(idstation+" , "+datereport+" , "+taux+" , "+coefficient+" , "+actualCashBanked);
//            return false;

            if(coefficient === '' || actualCashBanked === ''){
                $("#msgvr").removeClass("hidden");
                
                return false;
            }
            $("#msgvr").addClass("hidden");
            $.ajax({
                url:"traitement/ajaxreport.php",
                method:"post",
                data:{action:1,station:idstation,
                    datecourante:datereport,
                    tauxReport:taux,
                    coefficient:coefficient,
                    totalCashSales:totalCashSales,
                actualCashBanked:actualCashBanked,archive:archive},
                beforeSend:function(){
                    $("#loading").removeClass("hidden");
                },
                success:function(data){
                    
                    $("#cashFlow").html(data);
                    $("#loading").addClass("hidden");
                },
                error:function(e){
                    alert(e);
                }
            });
        });
        
       
         <?php if($archive == true) {?>
            $("#btn-view").trigger("click");
        <?php } ?>
    });
    
    
    function printContent(el) {
        
        $(".frame").removeClass("active").addClass("active");
        var restorepage = document.body.innerHTML;
        var printcontant = document.getElementById(el).innerHTML;
        document.body.innerHTML = printcontant;
        window.print();
        document.body.innerHTML = restorepage;

    }

</script>
<?php include('footer.php'); ?>