<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');

try{
    
    $sql = "SELECT * FROM typeproduit";
    $reqProduits = $pdo->query($sql);

} catch (Exception $ex) {
    
    echo "Ligne : ".$ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}


?>
<div class="grid">
    <div class="row cells5">




        <div class="cell">
            <?php include('gauchemenuadmin.php'); ?>
            <img src = "img/typ.jpg">
        </div>



        <div class="cell well">
            <h5 class="text-light">PRODUCT TYPE INTERFACE</h5>
            <br/>
            <form method="post" onsubmit="return false;">
                <div class="grid">
                    <div class="row">
                        <div class="cell">

                            <label class="text-light" for="intitule">Name of product type</label>
                            <div class="input-control text">
                                <input type="text" name="intitule" id="intitule" placeholder="Name of product type"/>
                            </div>

                        </div>
                        <div class="cell">

                            <label class="text-light" for="unite">Unit</label>
                            <div class="input-control text">
                                <input type="text" name="unite" id="unite" placeholder="Unit"/>
                            </div>

                        </div>
                        <div class="cell">

                            <label class="text-light" for="datetype">Date</label>
                            <div class="input-control text" data-role="datepicker">
                                <input type="text" id="datetype" name="datetype">
                                <button class="button"><span class="mif-calendar"></span></button>
                            </div>  

                        </div>
                    </div>
                </div>




                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="validetypeproduit">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">PRODUCT TYPES RECORDED</h5>
            <!--<button class="button info"><a href="#">Daily report</a></button>-->
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Intitule</th>
                        <th class="">Unit</th>
                        <th class="">Date</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    while ($typeproduit = $reqProduits->fetch(PDO::FETCH_OBJ)) {
                        ?>
                        <tr>
                            <td><?= $typeproduit->intitule; ?></td>
                            <td><?= $typeproduit->unite; ?></td>
                            <td><?= $typeproduit->datetype; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatepump-modal-sm-<?= $typeproduit->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatepump-modal-sm-<?= $typeproduit->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $typeproduit->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $typeproduit->id; ?>">Product Data Updated</h4>
                                </div>
                                <div class="modal-body"> 
                                    <div id="formupdate-<?= $typeproduit->id; ?>">
                                        <label class="text-light" for="intitule">Name of product type</label>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $typeproduit->intitule; ?>" id="intitule"/>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light" for="unite">Unit</label>
                                            <input type="text" value="<?= $typeproduit->unite; ?>" id="unite"/>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text" data-role="datepicker">
                                            <label class="text-light" for="datetype">Date</label>
                                            <input type="text" id="datetype" value="<?= $typeproduit->datetype; ?>">
                                            <button class="button"><span class="mif-calendar"></span></button>
                                        </div>
                                        <div id="resupump-<?= $typeproduit->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updateuser"  onclick="updateTypeProduit(<?= $typeproduit->id; ?>);">Validate</button>
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
            <img src = "img/type.jpg">
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("produittype");
        activerMenu("settings");
        $("#pump").val(' ');


        $("#validetypeproduit").click(function () {
            var intitule = $("#intitule").val();
            var unite = $("#unite").val();
            var datetype = $("#datetype").val();
            if (intitule !== '' && datetype !== '' && unite !== '') {
                $.ajax({
                    url: "traitement/ajaxtypeproduitadmin.php",
                    method: "post",
                    data: {intitule: intitule, unite: unite, datetype: datetype},
                    success: function (data) {
                        $("#resu").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Fields required  !!!</p>");
            }
        });


    });

    function updateTypeProduit(id) {
        var intitule = $("#formupdate-" + id).find(" input[id='intitule'] ").val();
        var unite = $("#formupdate-" + id).find(" input[id='unite'] ").val();
        var datetype = $("#formupdate-" + id).find(" input[id='datetype'] ").val();

//        alert("intitule = " + intitule + " , unite = " + unite + ",datetype =" + datetype);
//        return false;
        if (intitule !== '' && datetype !== '' && unite !== '') {
            $.ajax({
                url: "traitement/ajaxtypeproduitadmin.php",
                method: "post",
                data: {intituleUP: intitule, uniteUP: unite, datetypeUP: datetype, id: id},
                success: function (data) {
                    $("#resupump-" + id).html(data);

                    window.location.reload();
                }
            });
        } else {
            $("#resupump-" + id).html("<p class='alert alert-danger'>Fields required  !!!</p>");
        }
    }

</script>
<?php include('footer.php'); ?>