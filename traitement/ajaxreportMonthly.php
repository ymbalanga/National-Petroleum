<?php

include '../db.php';


if (!empty($_POST['fromdate']) and ! empty($_POST['todate'])) {
    extract($_POST);
    $sqlreport = "SELECT SUM(expenses) as depenses,SUM(totalcashsales) as cashsales,SUM(totalcreditsales) as totalcredit,SUM(totalcreditpayment) as totalpayment,SUM(actualcashbank) as cashbank "
            . " FROM dailyreport d"
            . " WHERE d.dateenreg BETWEEN :fromdate AND :todate";

    $reqreport = $pdo->prepare($sqlreport);
    $reqreport->execute(array(
        "fromdate" => $fromdate,
        "todate" => $todate));
    $total = $reqreport->fetch(PDO::FETCH_OBJ);
}
?>
<button class="button succes" id="archideMois">Archive report</button>
<h4 class="text-light text-left text-shadow">MONTHLY REPORT FROM: <strong><?= $fromdate; ?></strong>  TO:  <strong> <?= $todate; ?></strong></h4>
<table class="table table-bordered table-condensed table-striped">
    <tr class="info">
        <th>MEMO</th>
        <th>CDF</th>
        
    </tr>
    <tr>
        <th>TotalCash Sales</th>
        <td><?= $total->cashsales; ?></td>
        
    </tr>
    <tr>
        <th>Expenses</th>
        <td><?= $total->depenses; ?></td>
       
    </tr>
    <tr>
        <th>TotalCredit Sales</th>
        <td><?= $total->totalcredit; ?></td>
       
    </tr>
    <tr>
        <th>TotalCredit Payment</th>
        <td><?= $total->totalpayment; ?></td>
       
    </tr>
    <tr>
        <th>ActualCash Bank</th>
        <td><?= $total->cashbank; ?></td>
       
    </tr>
</table>



