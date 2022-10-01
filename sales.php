<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require './traitement/fonctions.php';


try {
    
$station = $_SESSION["station"];
$sqlproduits = "SELECT * FROM produit "
        . " WHERE station =:station "
        . " AND typeproduit  NOT IN ( SELECT id FROM typeproduit WHERE intitule = 'GAZ')";
$reqproduits = $pdo->prepare($sqlproduits);
$reqproduits->execute(array("station"=>$station));
$produits = $reqproduits->fetchAll(PDO::FETCH_OBJ);


    

$sqlunites = "SELECT p.id as id,unite FROM typeproduit tp INNER JOIN produit p ON p.typeproduit = tp.id ";
$requnites = $pdo->query($sqlunites);
$unites = $requnites->fetchAll(PDO::FETCH_OBJ);


} catch (Exception $ex) {
    
    echo "Ligne : ".$ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}


?>
<!--debut modal-->
<div class="modal fade" id="updatevente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Update Sales Data</h4>
            </div>

            <div class="modal-body"> 
                <input type="hidden" id="idvente"/>
                <div class="input-control select">
                    <label class="text-light">Product</label>
                    <select id="produitUP" name="produitUP">
                        <?php
                        
                            foreach($produits as $data) {
                                echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                            }
                        ?>                                
                    </select>

                </div><br/>
                <label class="text-light">Quantity In Stock</label><br/>
                <div class="input-control text">
                    <input type="text" id="stockproduitUP" readonly="true"/>
                </div><br/>
                <label class="text-light">Sale's date</label><br/>
                <div class="input-control text" data-role="datepicker">
                    <input type="text" id="dateventeUP" name="datevente"/>
                    <button class="button"><span class="mif-calendar"></span></button>
                </div>
                <br/>
                <label class="text-light">Sale's Quantity</label><br/>
                <div class="input-control text">
                    <input type="text" name="qtevendu" id="qtevenduUP" placeholder="Sales Pcs"/>
                </div><br/>
                <label class="text-light">Product's price</label><br/>
                <div class="input-control text">
                    <input type="text" name="prixvente" id="prixventeUP" readonly="true"/>
                </div>
                <div id="resuUpdate"></div><br/>
                <div>
                    <button class="button info" id="updateVente">Validate</button>
                </div>
            </div>
        </div>
    </div>
    <!--fin modal-->
</div>
<div class="grid">
    <div class="row cells5">
        <div class="cell">
            <?php include 'gauchemenuSales.php'; ?>
            <img src = "img/fff.png">
        </div>
        <!--        ---------fin gauche -------------------
        ---------partie central----------------->
        <div class="cell colspan4">
            <div class="row cells4">
                <!--        ---------pump ------------------->
                <div class="cell well"> 
                    <h4 class="text-light">SALES INTERFACE</h4><br/>
                    <form method="post" onsubmit="return false;" id="salesform">
                        <div class="input-control select">
                            <label class="text-light">Product</label>
                            <select id="produit" name="produit">
                                <?php
                                
                                foreach($produits as $data) {
                                    echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                }
                                ?>                                
                            </select>

                        </div><br/>
                        <label class="text-light">Quantity In Stock</label><br/>
                        <div class="input-control text">
                            <input type="text" id="stockproduit" readonly="true"/>
                        </div><br/> 
                        <label class="text-light">Sale's date</label><br/>
                        <div class="input-control text" data-role="datepicker">
                            <input type="text" id="datevente" name="datevente"/>
                            <button class="button"><span class="mif-calendar"></span></button>
                        </div>
                        <br/>
                        <label class="text-light">Sale's Quantity</label><br/>
                        <div class="input-control text full-size">
                            <input type="text" name="qtevendu" id="qtevendu" placeholder="Sales Quantity"/>
                            <span class="button" id="unite"></span>
                        </div><br/>
                        <label class="text-light">Product's price</label><br/>
                        <div class="input-control text full-size">
                            <input type="text" name="prixvente" id="prixvente" readonly="true"/>
                        </div>
                        <div>
                            <button class="button info" id="validesales">Validate</button>
                        </div>
                    </form>&nbsp;
                    <div id="resu"></div>
                </div>
                <!---------tank ------------------->

                <!-- bloc tableau-->
                <div class="cell colspan3">
                    <h4 class="text-light">SALES SUMMARY</h4>
                    <!--<button class="button info"><a href="#">Daily report</a></button>-->
                    <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                        <thead>
                            <tr>

                                <th class="">Product</th>
                                <th class="">Date</th>
                                <th class="">Sale Quantity</th>
                                <th class="">Prize</th>
                                <th class="">Total</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tablepump">

                            <?php
                            

                            $sqlvente = "SELECT vt.id as vtid, pd.id as id,pd.typeproduit as idtypeproduit, pd.intitule as pdintitule, "
                                    . "vt.qtevendu as vtqtevendu, vt.datevente as vtdatevente,"
                                    . " vt.prixvente as  prixvente,"
                                    . " (vt.qtevendu * vt.prixvente) as amount "
                                    . " FROM vente vt INNER JOIN produit pd ON vt.produit = pd.id "
                                    . " WHERE pd.station =:station "
                                    . " AND  typeproduit  NOT IN ( SELECT id FROM typeproduit WHERE intitule = 'GAZ') "
                                    . " ORDER BY vt.datevente";


                            $reqvente = $pdo->prepare($sqlvente);
                            $reqvente->execute(array("station" => $station));
                            $venteSummary = $reqvente->fetchAll(PDO::FETCH_OBJ);



                            foreach ($venteSummary as $vente) {
                                
                                $unite = getUnite($pdo, $vente->idtypeproduit);
                                
                                ?>
                                <tr>
                                    <td><?= $vente->pdintitule; ?></td>
                                    <td><?= $vente->vtdatevente; ?></td>
                                    <td><?= $vente->vtqtevendu; ?> <span class="label label-default"> <?= $unite; ?> </span></td>
                                    <td><?= $vente->prixvente; ?></td>
                                    <td><?= $vente->amount; ?></td>
                                    <td>
                                        <button class = 'btn btn-warning btn-sm' onclick="remplirModal(<?= $vente->vtid; ?>, <?= $vente->id; ?>, <?= $vente->vtqtevendu; ?>, '<?= $vente->vtdatevente; ?>', <?= $vente->prixvente; ?>)" data-toggle="modal"  data-target="#updatevente">Update</button>
                                    </td>
                                </tr>

                                <?php
                            }
                            ?>
                            
                        </tbody>

                    </table>
                </div>


            </div>


        </div>
    </div>
    <div class="row">
        <div class="cell">
                
            <?php 
            foreach ($unites as $u){
            ?> 
                <input type="hidden" value="<?= $u->unite ;?>" id="u-<?= $u->id;?>" />
            <?php 
                }
            ?>
        </div>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        
        activerSm("sales");
        activerMenu("sales");
        refreshStock($('#produit').val(), "stockproduit");
        afficherUnite($("#produit").val());
        refreshprix($('#produit').val(),"prixvente");
        
       
        $('#produit').change(function () {
            refreshStock(this.value, "stockproduit");
            refreshprix(this.value,"prixvente");
            afficherUnite(this.value);
        });
        
        $('#produitUP').change(function () {
            refreshStock(this.value, "stockproduitUP");
            refreshprix(this.value,"prixventeUP");
        });
        function refreshprix(produitID,idPrixVente) {
            $.ajax({
                url: "traitement/ajaxsales.php",
                method: "post",
                data: {produitID: produitID},
                success: function (data) {
                    $('#'+idPrixVente).val(data);
                }
            });
        }

        $("#validesales").click(function () {
            var produit = $("#produit").val();
            var qtevendu = $("#qtevendu").val();
            var datevente = $("#datevente").val();
            var prixvente = $("#prixvente").val();
            var stockproduit = $("#stockproduit").val();
            
            $("#resu").html(" ");
            //alert("produit = "+produit+", date  =  "+datevente+", qtevendu = "+qtevendu);

            if (qtevendu !== '' && datevente !== '' && prixvente !== '') {
                
                if (parseFloat(qtevendu)>  parseFloat(stockproduit)) {
                    $("#resu").html("<p class='alert alert-danger'>This Quantity is  More than Our Stock</p>");
                    return false;
                }
                
                $.ajax({
                    url: "traitement/ajaxsales.php",
                    method: "post",
                    data: {produit: produit, qtevendu: qtevendu, datevente: datevente, prixvente: prixvente},
                    success: function (data) {
                        $("#resu").html(data);
                        $("#qtevendu").val(' ');
                        $("#datevente").val(' ');
                        $("#prixvente").val(' ');

                        window.location.reload();
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>fields required !!!</p>");
            }
        });
        $("#updateVente").click(function () {

            var idvente = $("#idvente").val();
            var produit = $("#produitUP").val();
            var qtevendu = $("#qtevenduUP").val();
            var datevente = $("#dateventeUP").val();
            var prixvente = $("#prixventeUP").val();
            var stockproduit = $("#stockproduitUP").val();
            //alert("produit = "+produit+", date  =  "+datevente+", stock  = "+stockproduit+", qtevendu = "+qtevendu);
            
            if (qtevendu !== '' && datevente !== '' && prixvente !== '' && prixvente !== '') {
                
                if (parseFloat(qtevendu)>  parseFloat(stockproduit)) {
                    $("#resu").html("<p class='alert alert-danger'>This Quantity is  More than Our Stock</p>");
                    return false;
                }
                
                $.ajax({
                    url: "traitement/ajaxsales.php",
                    method: "post",
                    data: {idventeUP: idvente, produitUP: produit, qtevenduUP: qtevendu, dateventeUP: datevente, prixventeUP: prixvente},
                    success: function (data) {
                        $("#resuUpdate").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resuUpdate").html("<p class='alert alert-danger'>Please, Complete empty Fields !!!</p>");
            }

        });



    });
    function remplirModal(idvente, produit, qtevendu, datevente, prixvente) {
        $("#idvente").val(idvente);
        $("#produitUP").val(produit);
        $("#qtevenduUP").val(qtevendu);
        $("#dateventeUP").val(datevente);
        $("#prixventeUP").val(prixvente);
        
        refreshStock(produit, "stockproduitUP");
        refreshprix($('#produitUP').val(),"prixventeUP");

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
    
    function afficherUnite(produit){
        var unite = $("#u-"+produit).val();
        $("#unite").html(unite);
    }

</script>

<?php include('footer.php'); ?>