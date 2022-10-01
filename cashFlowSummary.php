<?php
require_once('db.php');

extract($_POST);

$totalCreditSales = getTotalCreditSales($pdo, $station, $datecourante);
$totalCreditPayment = getTotalCreditPayment($pdo, $station, $datecourante);
$totalExpenses = getTotalExpenses($pdo, $station, $datecourante);


$totalCashSalesAndCreditPayment = round($totalCashSales - $totalExpenses - $totalCreditSales + $totalCreditPayment - $coefficient * $tauxReport, 1);
$difference = abs($actualCashBanked - $totalCashSalesAndCreditPayment);
?>


<script type="text/javascript">

    function archiveReport(datareport) {

        $.ajax({
            url: "traitement/ajaxreport.php",
            method: "post",
            data: {action: 2, data: datareport},
            beforeSend: function () {
                $("#loadingm").removeClass("hidden");
            },
            success: function (data) {

                $("#message").html(data);
                $("#loadingm").addClass("hidden");
            },
            error: function (e) {
                alert(e);
            }
        });
    }

</script>
<div class="grid">
    <div class="row cells8">
        <div class="cell colspan2">
            <?php if ($archive == 0) { ?>
                <button type="button" id="btn-archive" class="btn  small-button btn-success" 
                        onclick="archiveReport(['<?= $station; ?>', '<?= $tauxReport; ?>', '<?= $coefficient; ?>', '<?= $datecourante; ?>', '<?= $totalCashSales; ?>', '<?= $totalExpenses; ?>', '<?= $totalCreditSales; ?>', '<?= $totalCreditPayment; ?>', '<?= $actualCashBanked; ?>']);
                        ">    
                    Validate and Archive
                </button>
            <?php } ?>
        </div>
        <div class="cell colspan6">

            <div id="message" class="message">
                <img src="img/ajax_loader.gif" id="loadingm" class="hidden" /> 
            </div>
        </div>
    </div>
    <div class="row">
        <div class="cell">
            <h4 class="text-light text-left text-shadow">DAILY REPORT OF THIS DATE <strong><?= $datecourante; ?></strong>
            </h4>
            <table class="table table-bordered table-condensed">
                <tr class=" table-cashflow">
                    <th>MEMO</th>
                    <th>CDF</th>
                    <th>USD</th>
                </tr>
                <tr>
                    <th>Total Cash Sales</th>
                    <td><?= $totalCashSales; ?> <span class="money">CDF</span></td>
                    <td> <?= round($totalCashSales / $tauxReport, 1); ?> <span class="money">$</span></td>
                </tr>
                <tr>

                    <th>Expenses</th>
                    <td><?= $totalExpenses; ?> &nbsp;&nbsp;&nbsp; <span class="money">CDF</span></td>
                    <td> <?= round($totalExpenses / $tauxReport, 1); ?>&nbsp;&nbsp;&nbsp; <span class="money">$</span></td>
                </tr>

                <tr>
                    <th>Total Credit Sales</th>
                    <td><?= $totalCreditSales; ?> <span class="money">CDF</span></td>
                    <td> <?= round($totalCreditSales / $tauxReport, 1); ?> <span class="money">$</span></td>
                </tr>
                <tr>
                    <th>Credit payment</th>
                    <td><?= $totalCreditPayment; ?> <span class="money">CDF</span></td>
                    <td> <?= round($totalCreditPayment / $tauxReport, 1); ?> <span class="money">$</span> </td>
                </tr>
                <tr>
                    <th>CASH IN THE BANK</th>
                    <td><?= round($coefficient * $tauxReport, 1); ?> <span class="money">CDF</span></td>
                    <td> <?= $coefficient; ?> <span class="money">$</span> </td>
                </tr>
               
                <tr>
                    <th>ACTUAL CASH BANKED</th>
                    <td><?= $actualCashBanked; ?> <span class="money">CDF</span></td>
                    <td> <?= round($actualCashBanked / $tauxReport, 1); ?> <span class="money">$</span></td>
                </tr>
                 <tr>
                    <th>Total Cash Sales & Credit PYT</th>
                    <td><?= $totalCashSalesAndCreditPayment; ?> <span class="money">CDF</span></td>
                    <td><?= round($totalCashSalesAndCreditPayment / $tauxReport, 1); ?> <span class="money">$</span></td>
                </tr>
                <tr>
                    <th class="danger text-danger">DIFFERENCE</th>
                    <td><?= $difference; ?> <span class="money">CDF</span></td>
                    <td><?= round($difference / $tauxReport, 1); ?> <span class="money">$</span></td>
                </tr>
            </table>
        </div>

    </div>
    <div class="row">
        <div class="cell">
            <h4 class="text-light text-left text-shadow">GRAPHIQUE</h4>
            <pre id="t"></pre>
            <div id="graphique" style="height:300px;padding: 0px; position: relative;" class="well full-size">

            </div>
        </div>
    </div>

</div>

    
<script  src="assets/plugins/flot/jquery.flot.js"></script>
<script src="assets/plugins/flot/jquery.flot.resize.js"></script>
<script  src="assets/plugins/flot/jquery.flot.pie.js"></script>
    
<script type="text/javascript">

 
    $(function(){

        
        var mydata =[{label:"Sales",data:<?= $totalCashSales; ?>},
            {label:"Expenses",data:<?= ($totalExpenses  == null)? 0 : $totalExpenses; ?>},
            {label:"Credits",data:<?= ($totalCreditSales == null)? 0 : $totalCreditSales ; ?>},
            {label:"Credits Payment",data:<?= ($totalCreditPayment == null)? 0 : $totalCreditPayment; ?>}
        ];
        var placeholder = $("#graphique");
        
        placeholder.unbind();

          $.plot(placeholder, mydata, {
                series: {
                    pie: {
                        show: true,
                        radius: 3 / 3,
                        label: {
                            show: true,
                            radius: 3 / 4,
                            formatter: labelFormatter,
                            background: {
                                opacity: 0.5,
                                color: "#000"
                            },
                            fill: true, fillColor: "#eed"
                        }
                    }
                },
                legend: {
                    show: false
                }
            });            
       
    });
    
    function labelFormatter(label, series) {
        return "<div  style='font-size:8pt; text-align:center; \n\
                    padding:2px; color:white;'>" + label + "<br/>" + Math.round(series.percent) + "%</div>";
    }
</script>



