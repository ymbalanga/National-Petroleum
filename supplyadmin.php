<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require './traitement/fonctions.php';

try {


    $stationSession = $_SESSION["station"];

    $sql = "SELECT * FROM station ORDER BY id";
    $reqstation = $pdo->query($sql);
    $stations = $reqstation->fetchAll(PDO::FETCH_OBJ);



    $sql = "SELECT pd.intitule as pdintitule, ap.qteappro as apqteappro, ap.dateappro as apdateappro,"
            . " ap.id as apid,pd.id as pdid, st.intitule as pdstation,ap.prixachat as prixachat, "
            . " pd.typeproduit as idtypeproduit "
            . " FROM produit pd INNER JOIN approvisionnement ap ON ap.produit = pd.id "
            . " INNER JOIN station st ON pd.station = st.id "
            . " ORDER BY dateappro DESC ";
    $req = $pdo->query($sql);
    $appros = $req->fetchAll(PDO::FETCH_OBJ);


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
            <?php include('gauchemenuadmin.php'); ?>
            <img src = "img/four.jpg">
        </div>



        <div class="cell well">
            <h5 class="text-light">SUPPLY INTERFACE</h5>
            <form method="post" onsubmit="return false;">
                <div class="grid">
                    <div class="row">
                        <div class="cell">
                            <label class="text-light">Select Gas Station</label>
                            <div class="input-control select full-size">
                                <select id="station" name="station">
                                    <?php
                                    foreach ($stations as $st) {
                                        ?>
                                        <option value="<?= $st->id; ?>" <?= ($stationSession == $st->id ) ? "selected='true'" : ""; ?>><?= $st->intitule; ?></option>
                                        <?php
                                    }
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label class="text-light">
                                Select Product
                                <span class="label label-danger" id="stockproduit"></span>
                            </label>
                            <div class="input-control select full-size">
                                <select id="produit" name="produit">
                                    <option value=""></option>
                                </select>

                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label class="text-light">Quantity Delivered</label><br/>
                            <div class="input-control text full-size">
                                <input type="text" name="qteappro" id="qteappro" placeholder="Quantity delivered"/>
                                <span class="button" id="unite"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label class="text-light">Purchase prize </label><br/>
                            <div class="input-control text full-size">
                                <input type="text"  id="prixachat" placeholder="Purchase prize"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <label class="text-light">Date</label><br/>
                            <div class="input-control text" data-role="datepicker">
                                <input type="text" name="dateappro" id="dateappro"/>
                                <button class="button"><span class="mif-calendar"></span></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="addappro">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">SUPPLY LIST</h5>
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Gas Station</th>
                        <th class="">Product</th>
                        <th class="">Quantity</th>
                        <th class="">Prize</th>
                        <th class="">Date</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    foreach ($appros as $appro) {
                        $unite = getUnite($pdo, $appro->idtypeproduit);
                        ?>
                        <tr>
                            <td><?= $appro->pdstation; ?></td>
                            <td><?= $appro->pdintitule; ?></td>
                            <td><?= $appro->apqteappro; ?> <span class="label label-default"> <?= $unite; ?> </span> </td>
                            <td><?= $appro->prixachat; ?></td>
                            <td><?= $appro->apdateappro; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updateappro-<?= $appro->apid; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updateappro-<?= $appro->apid; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $appro->apid; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $appro->apid; ?>"> Supply Data Update</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="formupdate-<?= $appro->apid; ?>">
                                        <br/>
                                        <label class="text-light">Gas Station</label>
                                        <div class="input-control select">
                                            <select id="station" name="station" disabled="true">
                                                <option value=""><?= $appro->pdstation; ?></option>
                                            </select>

                                        </div>
                                        <br/>
                                        <label class="text-light">Product</label><br/>
                                        <div class="input-control select">
                                            <select id="produit" name="produit" disabled="true">
                                                <option value="<?= $appro->pdid; ?>" selected="true"><?= $appro->pdintitule; ?></option>
                                            </select>

                                        </div>
                                        <br/>
                                        <label class="text-light">Quantity Delivered</label><br/>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $appro->apqteappro; ?>" name="qteapproupdate" id="qteapproupdate" placeholder="Quantity delivered"/>
                                        </div>
                                        <br/>
                                        <label class="text-light">Purchase prize</label><br/>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $appro->prixachat; ?>"  id="prixachatupdate" placeholder="Purchase prize"/>
                                        </div>
                                        <br/>
                                        <label class="text-light">Date</label><br/>
                                        <div class="input-control text" data-role="datepicker">
                                            <input type="text" name="dateappro" id="dateapproupdate" value="<?= $appro->apdateappro; ?>"/>
                                            <button class="button"><span class="mif-calendar"></span></button>
                                        </div>

                                        <div id="resu-<?= $appro->apid; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updatestation"  onclick="updateAppro(<?= $appro->apid; ?>);">Validate</button>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <!--fin modal-->

                <?php } ?>
                </tbody>
            </table>

        </div>




        <div class="cell">
            <?php include('droitmenu.php'); ?>
            <img src = "img/fou.jpg">
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

        activerSm("supply");
        activerMenu("settings");
        
        refreshProduit($("#station").val());

        $("#station").change(function () {
            refreshProduit(this.value);
            
        });

        $('#produit').change(function () {
            refreshStock(this.value, "stockproduit");
            afficherUnite(this.value);
        });

        $("#addappro").click(function () {

            var produit = $("#produit").val();
            var dateappro = $("#dateappro").val();
            var qteappro = $("#qteappro").val();
            var prixachat = $("#prixachat").val();

            //alert("produit = "+produit+", dateappro = " + dateappro+", qteappro = " +qteappro);
            if (qteappro !== '' && dateappro !== '' && prixachat !== '') {
                $.ajax({
                    url: "traitement/ajaxsupply.php",
                    method: "post",
                    data: {action: 1, produit: produit, dateappro: dateappro, qteappro: qteappro,prixachat:prixachat},
                    success: function (data) {
                        $("#resu").html(data);


                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Field Quantity,Date can't be empty !!!</p>");
            }
        });


    });


    function refreshProduit(idstation) {

        $.ajax({
            url: "traitement/ajaxsupply.php",
            method: "post",
            data: {action: 3, idstation: idstation},
            success: function (data) {
                $("#produit").html(data);
                refreshStock($("#produit").val(), "stockproduit");
                afficherUnite($("#produit").val());
                
            },
            error: function (e) {
                alert(e.message);
            }
        });


    }

    function updateAppro(id) {
        var dateapproupdate = $("#formupdate-" + id).find(" input[id='dateapproupdate'] ").val();
        var qteapproupdate = $("#formupdate-" + id).find(" input[id='qteapproupdate'] ").val();
        var prixachatupdate = $("#formupdate-" + id).find(" input[id='prixachatupdate'] ").val();

        //alert("produit = "+produit+", dateappro = " + dateappro+", qteappro = " +qteappro);
        if (qteapproupdate !== '' && dateapproupdate !== '' && prixachatupdate !=='') {
            $.ajax({
                url: "traitement/ajaxsupply.php",
                method: "post",
                data: {action: 2, idappro: id, dateapproupdate: dateapproupdate, qteapproupdate: qteapproupdate,
                            prixachatupdate:prixachatupdate},
                success: function (data) {
                    $("#resu-" + id).html(data);

                }
            });
        } else {
            $("#resu-" + id).html("<p class='alert alert-danger'>Field Quantity,Date can't be empty !!!</p>");
        }

    }
    
    function refreshStock(produitID, id) {
        $.ajax({
            url: "traitement/ajaxotherSales.php",
            method: "post",
            data: {produitID: produitID},
            success: function (data) {
                $('#' + id).html("   QTY =   "+data);

            }
        });
    }

    function afficherUnite(produit) {

        var unite = $("#u-" + produit).val();
        $("#unite").html(unite);
    }

</script>
<?php include('footer.php'); ?>