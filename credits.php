<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require './traitement/fonctions.php';

try {

    $station = $_SESSION['station'];
// code credit
    $sqlcredit = "SELECT cm.nom as cusname,cm.id as cmid,pd.intitule as pdname,pd.id as pdid,cd.id as idcredit,"
            . " cd.quantite as qtecredit, cd.datecredit as cddatecredit, cd.etat as cdetat,cd.prix as prixcredit,"
            . "pd.typeproduit as idtypeproduit "
            . " FROM produit pd INNER JOIN credit cd ON cd.produit = pd.id "
            . " INNER JOIN customer cm ON cd.idcustomer = cm.id "
            . " WHERE  pd.station =:station "
            . " ORDER BY cd.datecredit DESC ";
    $reqcredits = $pdo->prepare($sqlcredit);
    $reqcredits->execute(array("station" => $station));
    $credits = $reqcredits->fetchAll(PDO::FETCH_OBJ);

// calcul montant et prix from credit
// code paiement
    $sqlpayment = "SELECT cm.nom as cname,pc.montant as pcmontant,pc.datepay as pcdatepay,"
            . "pdt.intitule as pintitule,pdt.typeproduit as idtypeproduit "
            . " FROM paiementcredit pc INNER JOIN credit cd ON pc.credit = cd.id"
            . " INNER JOIN customer cm ON cd.idcustomer = cm.id INNER JOIN produit pdt ON cd.produit = pdt.id"
            . " WHERE  pdt.station =:station "
            . " ORDER BY pc.datepay DESC ";
    $reqpayment = $pdo->prepare($sqlpayment);
    $reqpayment->execute(array("station" => $station));
    $payments = $reqpayment->fetchAll(PDO::FETCH_OBJ);


//produits
    $reqproduits = $pdo->prepare("SELECT * FROM produit WHERE station =:station");
    $reqproduits->execute(array("station" => $station));
    $produits = $reqproduits->fetchAll(PDO::FETCH_OBJ);


// code customer
    $sqlcustomer = "SELECT * FROM customer ORDER BY id DESC";
    $reqcustomer = $pdo->query($sqlcustomer);
    $customers = $reqcustomer->fetchAll(PDO::FETCH_OBJ);


    $unites = getProduitUnite($pdo);
} catch (Exception $ex) {

    echo "Ligne : " . $ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}
?>
<div class="grid">
    <div class="row cells5">




        <div class="cell">
            <?php include('gauchemenu.php'); ?>
            <img src = "img/vv.png">
        </div>

        <!--debut modal-->
        <div class="modal fade" id="addpay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Add Payment</h4>
                    </div>
                    <div class="modal-body">
                        <label>Amount to pay</label>
                        <div class="input-control text full-size">
                            <input type="text" id="amountdue" name="amountdue" placeholder="Customer Name" readonly="true"/>
                        </div>
                        <div class="input-control text full-size">
                            <input type="text" id="amountpay" name="amountpay" placeholder="Input Payment"/>
                        </div>
                        <div class="input-control text full-size">
                            <input type="hidden" id="idcredit" name="idcredit"/>
                        </div>
                        <div id="resupay"></div>
                        <div>
                            <button class="button info" id="addpaiement">Validate</button>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <!--fin modal-->
        <!--debut modal UPDATE -->
        <div class="modal fade" id="updateCredit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="myModalLabel">Update Credit</h4>
                    </div>
                    <div class="modal-body">
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Customer Name</label>
                                    <div class="input-control select full-size">
                                        <select name="customer" id="customerch" readonly="true">
                                            <?php
                                            foreach ($customers as $data) {
                                                echo '<option value=' . $data->id . '>' . $data->nom . '</option>';
                                            }
                                            ?>                                
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Product</label>
                                    <div class="input-control select full-size">
                                        <select name="produit" id="produitch" readonly="true">
                                            <?php
                                            foreach ($produits as $data) {
                                                echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                            }
                                            ?>                                
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Quantity In Stock</label>
                                    <div class="input-control text full-size">
                                        <input type="text" id="stockproduitUP" readonly="true"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Quantity</label>
                                    <div class="input-control text full-size">
                                        <input type="text" name="qtecredit" id="qtecreditch" placeholder="Quantity Credit"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Date</label>
                                    <div class="input-control text" data-role="datepicker">
                                        <input type="text" id="creditdatech" name="creditdate"/>
                                        <button class="button"><span class="mif-calendar"></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="cell">
                                    <label class="text-light">Product's Price</label>
                                    <div class="input-control text full-size">
                                        <input type="text" name="prixcre" id="prixcrech" readonly="true"/>
                                    </div>
                                </div>
                            </div>
                         <input type="hidden" id="idCreditUP"/>
                        <div id="resuUpdateCredit"></div>
                        <div>
                            <button class="button info" id="UpdateCreditUP">Validate</button>
                        </div>
                    </div>
                </div> 
            </div>
        </div>

        <!--fin modal-->


        <div class="cell well">
            <h5 class="text-light">INPUT CREDIT INTERFACE</h5>
            <!--debut modal-->
            <div class="modal fade" id="addcustomer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="myModalLabel">Add Customer</h4>
                        </div>
                        <div class="modal-body">
                            <label class="text-light">Customer Name</label>
                            <div class="input-control text full-size">
                                <input type="text" id="nom" name="nom" placeholder="Customer Name" />
                            </div>
                            <label class="text-light">Party's Name</label>
                            <div class="input-control text full-size">
                                <input type="text" id="resp" name="resp" placeholder="Party's name"/>
                            </div>


                            <div id="resucus"></div>
                            <div>
                                <button class="button info" id="addcus">Validate</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>

            <!--fin modal-->

            <form method="post" onsubmit="return false;">
                <label class="text-light">Customer Name</label>
                <div class="input-control select full-size">
                    <select name="customer" id="customer">
                        <?php
                        foreach ($customers as $data) {
                            echo '<option value=' . $data->id . '>' . $data->nom . '</option>';
                        }
                        ?>                                
                    </select>
                    <button class="button  primary" data-toggle="modal"  data-target="#addcustomer"><span class="mif-plus"></span></button>
                </div><br/><br/>
                <div class="input-control select">
                    <label class="text-light">Product</label>
                    <select name="produit" id="produit">
                        <?php
                        foreach ($produits as $data) {
                            echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                        }
                        ?>                                
                    </select>
                </div><br/>
                <label class="text-light">Quantity In Stock</label><br/>
                <div class="input-control text">
                    <input type="text" id="stockproduit" readonly="true"/>
                </div><br/>
                <label class="text-light">Quantity</label>
                <div class="input-control text full-size">
                    <input type="text" name="qtecredit" id="qtecredit" placeholder="Quantity Credit"/>
                    <span class="button" id="unite"></span>
                </div><br/>
                <label class="text-light">Date</label>
                <div class="input-control text" data-role="datepicker">
                    <input type="text" id="creditdate" name="creditdate">
                    <button class="button"><span class="mif-calendar"></span></button>
                </div>
                <label class="text-light">Product's Price</label>
                <div class="input-control text full-size">
                    <input type="text" name="prixcre" id="prixcre" readonly="true"/>
                </div>
                <div id="resu"></div>
                <div>
                    <button class="button info" id="validecredit">Validate</button>
                </div>
            </form>
        </div>







        <div class="cell colspan3 well">
            <h5 class="text-light">CREDIT OPTIONS</h5>
            <div class="tabcontrol2" data-role="tabcontrol">
                <ul class="tabs">
                    <li><a href="#frame_5_cre">Credtis</a></li>
                    <li><a href="#frame_5_pay">Payment</a></li>
                    <li><a href="#frame_5_cus">Customer</a></li>

                </ul>
                <div class="frames">
                    <div class="frame" id="frame_5_cre">
                        <h4 class="text-light">Credited Customers</h4>
                        <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                            <thead>
                                <tr>
                                    <th class="">Customer</th>
                                    <th class="">Product</th>
                                    <th class="">Quantity</th>
                                    <th class="">Prize</th>
                                    <th class="">Amount Due</th>
                                    <th class="">Date</th>
                                    <th class="">State</th>
                                    <th class="">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($credits as $credit) {
                                    $unite = getUnite($pdo, $credit->idtypeproduit);

                                    $diffMontantApayer = 0;

                                    $calculQtePrix = $credit->qtecredit * $credit->prixcredit;

                                    if ($credit->cdetat == 1) {
                                        $sqlMontant = "SELECT SUM(montant) as montantDejaPayer FROM paiementcredit WHERE credit=:idcredit";
                                        $reqMontant = $pdo->prepare($sqlMontant);
                                        $reqMontant->execute(array('idcredit' => $credit->idcredit));
                                        $resuMontant = $reqMontant->fetch(PDO::FETCH_OBJ);

                                        $diffMontantApayer = $calculQtePrix - $resuMontant->montantDejaPayer;
                                    } else {
                                        $diffMontantApayer = $credit->qtecredit * $credit->prixcredit;
                                    }
                                    $datecredit = str_replace('-', '.', $credit->cddatecredit);
                                    ?>
                                    <tr>
                                        <td><?= $credit->cusname; ?></td>
                                        <td><?= $credit->pdname; ?></td>
                                        <td><?= $credit->qtecredit; ?> <span class="label label-default"> <?= $unite; ?> </span></td>
                                        <td><?= $credit->prixcredit; ?></td>
                                        <td><?= $diffMontantApayer; ?></td>
                                        <td><?= $credit->cddatecredit; ?></td>
                                        <td>
                                            <?php
                                            switch ($credit->cdetat) {
                                                case 0: echo "<b class='label label-danger'>Not paid</b>";
                                                    break;
                                                case 1: echo "<b class='label label-warning'>Advance</b>";
                                                    break;
                                                case 2: echo "<b class='label label-success'>Paid</b>";
                                                    break;
                                            }
                                            ?>

                                        </td>
                                        <td>
                                            <?php if ($credit->cdetat != 2) { ?>
                                                <button class="button small-button warning" data-toggle="modal"  data-target="#addpay" onclick="remplirModal(<?= $credit->idcredit; ?>,<?= $diffMontantApayer; ?>);">Payment</button> |
                                                <button class="button small-button info" data-toggle="modal"  data-target="#updateCredit" onclick="remplirModalUpdate(<?= $credit->idcredit; ?>,<?= $credit->qtecredit; ?>, '<?= $datecredit; ?>', <?= $credit->cmid; ?>, <?= $credit->pdid; ?>, <?= $credit->prixcredit; ?>);">Update</button>
                                            <?php } elseif ($credit->cdetat == 2) {
                                                ?>
                                                <label class="button small-button success">Acquitted</label>
                                            <?php }
                                            ?>
                                        </td>


                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!--debut modal-->
                    <div class="modal fade" id="addpay" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Add Payment</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="input-control text full-size">
                                        <input type="text" id="nom" name="nom" placeholder="Customer Name" />
                                    </div>
                                    <div class="input-control text full-size">
                                        <input type="text" id="resp" name="resp" placeholder="Party's name"/>
                                    </div>
                                    <div id="resucus"></div>
                                    <div>
                                        <button class="button info" id="addcus">Validate</button>
                                    </div>
                                </div>
                            </div> 
                        </div>
                    </div>

                    <!--fin modal-->












                    <div class="frame" id="frame_5_pay">
                        <h4 class="text-light">Payment Customers</h4>

                        <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                            <thead>
                                <tr>
                                    <th class="">Customer</th>
                                    <th class="">Product</th>
                                    <th class="">Amount</th>
                                    <th class="">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($payments as $payment) {
                                    ?>
                                    <tr>
                                        <td><?= $payment->cname; ?></td>
                                        <td><?= $payment->pintitule; ?></td>
                                        <td><?= $payment->pcmontant; ?></td>
                                        <td><?= $payment->pcdatepay; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>











                    <div class="frame" id="frame_5_cus">
                        <h4 class="text-light">List of Customers</h4>
                        <button class="button small-button info" data-toggle="modal"  data-target="#addcustomer">Add Customer</button>
                        <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                            <thead>
                                <tr>
                                    <th class="">Name</th>
                                    <th class="">Party's Name</th>
                                    <th class="">State</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($customers as $customer) {
                                    ?>
                                    <tr>
                                        <td><?= $customer->nom; ?></td>
                                        <td><?= $customer->partieresponsable; ?></td>
                                        <td><?= $customer->etat; ?></td>  
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>








                </div>
            </div>




        </div>
    </div>
    <div class="row">
        <div class="cell">

            <?php
            foreach ($unites as $u) {
                ?> 
                <input type="hidden" value="<?= $u->unite; ?>" id="u-<?= $u->id; ?>" />
                <?php
            }
            ?>
        </div>
    </div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        activerSm("credit");
        activerMenu("home");
        refreshStock($('#produit').val(), "stockproduit");
        afficherUnite($("#produit").val());
        refreshprix($('#produit').val());

//        $('#produit').change(function () {
//            refreshprix(this.value);
//        });

        $('#produit').change(function () {
            refreshprix(this.value);
            refreshStock(this.value, "stockproduit");
            afficherUnite(this.value);
        });

        $('#produitch').change(function () {
            refreshStock(this.value, "stockproduitUP");
        });


        function refreshprix(produit) {
            $.ajax({
                url: "traitement/ajaxcredit.php",
                method: "post",
                data: {produit: produit},
                success: function (data) {
                    $('#prixcre').val(data);
                }
            });
        }



        $("#validecredit").click(function () {
            var customer = $("#customer").val();
            var produit = $("#produit").val();
            var qtecredit = $("#qtecredit").val();
            var creditdate = $("#creditdate").val();
            var prix = $("#prixcre").val();
            var stockproduit = $("#stockproduit").val();

            if (customer !== '' && produit !== '' && qtecredit !== '' && creditdate !== '' && prix !== '') {

                if (parseFloat(qtecredit) > parseFloat(stockproduit)) {
                    $("#resu").html("<p class='alert alert-danger'>This Quantity is  More than Our Stock</p>");
                    return false;
                }

                $.ajax({
                    url: "traitement/ajaxcredit.php",
                    method: "post",
                    data: {customer: customer, produit: produit, qtecredit: qtecredit, creditdate: creditdate, prix: prix},
                    success: function (data) {
                        $("#resu").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Recording Failed !!!</p>");
            }
        });

        $("#UpdateCreditUP").click(function () {
            var idCreditUP = $("#idCreditUP").val();
            var customer = $("#customerch").val();
            var produit = $("#produitch").val();
            var qtecredit = $("#qtecreditch").val();
            var creditdate = $("#creditdatech").val();
            var prix = $("#prixcrech").val();
            var stockproduit = $("#stockproduitUP").val();
//            alert(idCreditUP+","+customer+","+produit+","+qtecredit+","+creditdate+","+prix);
//            return false;


            if (customer !== '' && produit !== '' && qtecredit !== '' && creditdate !== '' && prix !== '') {

                if (parseFloat(qtecredit) > parseFloat(stockproduit)) {
                    $("#resuUpdateCredit").html("<p class='alert alert-danger'>This Quantity is  More than Our Stock</p>");
                    return false;
                }


                $.ajax({
                    url: "traitement/ajaxcredit.php",
                    method: "post",
                    data: {idCreditUP: idCreditUP, customerUP: customer, produitUP: produit, qtecreditUP: qtecredit, creditdateUP: creditdate, prixUP: prix},
                    success: function (data) {
                        $("#resuUpdateCredit").html(data);


                    }
                });
            } else {
                $("#resuUpdateCredit").html("<p class='alert alert-danger'>Recording Failed !!!</p>");
            }
        });



        $("#addcus").click(function () {
            var nom = $("#nom").val();
            var resp = $("#resp").val();
            if (nom !== '' && resp !== '') {
                $.ajax({
                    url: "traitement/ajaxcustomer.php",
                    method: "post",
                    data: {nom: nom, resp: resp},
                    success: function (data) {
                        $("#resucus").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resucus").html("<p class='alert alert-danger'>Recording Failed !!!</p>");
            }
        });


        $("#addpaiement").click(function () {
            var idcredit = $("#idcredit").val();
            var amountpay = $("#amountpay").val();
            var amountdue = $("#amountdue").val();



            if (amountpay !== '' && idcredit !== '') {

                var changerEtatCredit = 0;
                if (amountdue === amountpay) {

                    changerEtatCredit = 2;
                }
                else if (amountpay > amountdue) {
                    $("#resupay").html("<p class='alert alert-danger'>Amount Exceeded !!!</p>");
                    return false;
                }
                else if (amountpay < amountdue && amountpay !== 0) {
                    changerEtatCredit = 1;
                }

                $.ajax({
                    url: "traitement/ajaxpaiement.php",
                    method: "post",
                    data: {idcredit: idcredit, amountpay: amountpay, changerEtatCredit: changerEtatCredit},
                    success: function (data) {
                        $("#resupay").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resupay").html("<p class='alert alert-danger'>Recording Failed !!!</p>");
            }
        });


    });

    function remplirModal(idcredit, amountdue) {
        $("#amountdue").val(amountdue);
        $("#idcredit").val(idcredit);
    }

    function remplirModalUpdate(idCreditUP, qte, datecredit, customerId, produit, prix) {
        $("#idCreditUP").val(idCreditUP);
        $("#customerch").val(customerId);
        $("#produitch").val(produit);
        $("#qtecreditch").val(qte);
        $("#creditdatech").val(datecredit);
        $("#prixcrech").val(prix);
        
        
        refreshStock($("#produitch").val(), "stockproduitUP");
    }
    function refreshStock(produitID, id) {
        $.ajax({
            url: "traitement/ajaxotherSales.php",
            method: "post",
            data: {produitID: produitID},
            success: function (data) {
                $('#' + id).val(data);

            }
        });
    }

    function afficherUnite(produit) {
        var unite = $("#u-" + produit).val();
        $("#unite").html(unite);
    }

</script>
<?php include('footer.php');
?>