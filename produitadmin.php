<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require "./traitement/fonctions.php";

$station = $_SESSION["station"];

try{
    

    $sqlproduits = "SELECT p.id as id, p.typeproduit as  idtypeproduit,p.intitule as pintitule,"
                    . " tp.intitule as tpintitule,p.qtestock as qtestock "
                    . " FROM produit p INNER JOIN typeproduit tp ON tp.id = p.typeproduit "
                    . " WHERE p.station =:idstation ";
    $reqProduits = $pdo->prepare($sqlproduits);
    $reqProduits->execute(array("idstation"=>$station));
    
    $retypeproduit = $pdo->query("SELECT * FROM typeproduit");
    $typesproduit = $retypeproduit->fetchAll(PDO::FETCH_OBJ);
    
    
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
            <img src = "img/pro.jpg">
        </div>



        <div class="cell well">
            <h4 class="text-light">PRODUCT INTERFACE</h4>
            <form method="post" onsubmit="return false;">
                <div class="grid">
                    <div class="row">
                        <div class="cell">
                            <label class="text-light" for="intitule">Product's Name </label>
                            <div class="input-control text">
                                <input type="text" name="intitule" id="intitule" placeholder="Name of product"/>
                            </div>
                        </div> 
                    </div>
                    
                    <div class="row">
                        <div class="cell">
                            <label class="text-light" for="typeproduit">Product Type</label>
                            <div class="input-control select">
                                <select name="typeproduit" id="typeproduit">
                                    <?php
                                    
                                    foreach($typesproduit as $data) {
                                        echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                    }
                                    ?>                                
                                </select>
                            </div>
                        </div>
                    </div>
<!--                    <div class="row">
                        <div class="cell">
                            <label class="text-light" for="qtestock">Initial Quantity</label>
                            <div class="input-control text full-size">
                                <input type="text" name="qtestock" id="qtestock" placeholder="Quantity"/>
                                <span class="button" id="unite"></span>
                            </div>
                        </div>
                    </div>-->
                    <div class="row">
                        <div class="cell">
                            <label class="text-light" for="prix">Prize</label>
                            <div class="input-control text">
                                
                                <input type="text" name="prix" id="prix" placeholder="Prize"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="cell">
                            <div id="resu"></div><br/>
                        </div>
                    </div>
                    <div>
                        <button class="button info" id="valideproduit">Validate</button>
                    </div>
                </div>

            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">PRODUCTS RECORDED</h5>
            <!--<button class="button info"><a href="#">Daily report</a></button>-->
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Intitule</th>
                        <th class="">Type Product</th>
                        <th class="">Quantity Stock</th>
                        <th class="">Prize</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    
                    
                    
                    while ($produit = $reqProduits->fetch(PDO::FETCH_OBJ)) {
                        
                        try {
                            $prixproduit = getPrixProduit($pdo, $produit->id);
                        } catch (Exception $ex) {
                             
                            echo $ex->getMessage();
                            exit();
                        }
                        
                        
                        ?>
                        <tr>
                            <td><?= $produit->pintitule; ?></td>
                            <td><?= $produit->tpintitule; ?></td>
                            <td><?= $produit->qtestock; ?></td>
                            <td><?= $prixproduit; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatepump-modal-sm-<?= $produit->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatepump-modal-sm-<?= $produit->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $produit->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $produit->id; ?>">Product Data Updated</h4>
                                </div>
                                <div class="modal-body"> 
                                    <div id="formupdate-<?= $produit->id; ?>">
                                        <label class="text-light">Product's Name</label>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $produit->pintitule; ?>" id="intitule" />
                                        </div>
                                        <label class="text-light">Product Type</label>
                                        <div class="input-control select">
                                            <select name="typeproduit" id="typeproduit">
                                                <?php
                                                foreach($typesproduit as $data) {
                                                    $selected = ($data->id == $produit->idtypeproduit)? " selected='true' " : " " ;
                                                    echo "<option value=$data->id ".$selected." >" . $data->intitule . "</option>";
                                                }
                                                ?>                                
                                            </select>
                                        </div>
<!--                                        <label class="text-light">Qte Stock</label>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $produit->qtestock; ?>" id="qtestock"/>
                                        </div>-->
                                        <br/>
                                        <label class="text-light">Prize</label><br/>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $prixproduit; ?>" id="prix"/>
                                        </div>

                                        <div id="resuproduit-<?= $produit->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updateuser"  onclick="updateProduit(<?= $produit->id; ?>);">Validate</button>
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
            <img src = "img/prod.jpg">
        </div>

    </div>
    <div class="row">
        <div class="cell">
                
            <?php 
            foreach ($typesproduit as $tp){
            ?> 
                <input type="hidden" value="<?= $tp->unite ;?>" id="u-<?= $tp->id;?>" />
            <?php 
                }
            ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("produitadmin");
        activerMenu("settings");
        afficherUnite($("#typeproduit").val());
        
        $("#typeproduit").change(function(){
            afficherUnite(this.value);
        });


        $("#valideproduit").click(function () {
            var intitule = $("#intitule").val();
            var typeproduit = $("#typeproduit").val();
           // var qtestock = $("#qtestock").val();
            var prix = $("#prix").val();
            
            if (intitule !== '' && typeproduit !== ''  && prix !== '') {
                $.ajax({
                    url: "traitement/ajaxproduitadmin.php",
                    method: "post",
                    data: {intitule: intitule, typeproduit: typeproduit, prix:prix},
                    success: function (data) {
                        $("#resu").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Fields required [ prize, quantity,  product's name ]  !!!</p>");
            }
        });


    });

    function updateProduit(id) {
        var typeproduit = $("#formupdate-" + id).find(" select[id='typeproduit'] ").val();
        var intitule = $("#formupdate-" + id).find(" input[id='intitule'] ").val();
        //var qtestock = $("#formupdate-" + id).find(" input[id='qtestock'] ").val();
        var prix = $("#formupdate-" + id).find(" input[id='prix'] ").val();

//        alert("intitule = "+intitule+" , typeproduit = "+typeproduit+", qtestock = "+qtestock);
//        return false;
        if (intitule !== '' && typeproduit !== ''  && prix !== '') {
            $.ajax({
                url: "traitement/ajaxproduitadmin.php",
                method: "post",
                data: {intituleUP: intitule, typeproduitUP: typeproduit, id: id,prixUP:prix},
                success: function (data) {
                    $("#resuproduit-" + id).html(data);

                    
                }
            });
        } else {
            $("#resuproduit-" + id).html("<p class='alert alert-danger'>Fields required  !!!</p>");
        }
    }
    
    function afficherUnite(typeproduit){
        var unite = $("#u-"+typeproduit).val();
        $("#unite").html(unite);
    }

</script>
<?php include('footer.php'); ?>