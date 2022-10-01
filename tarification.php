<?php
session_start();
require_once('db.php');
require "./traitement/fonctions.php";

$station = $_SESSION["station"];
$role =  $_SESSION["role"];


try {


    $sqlproduits = "SELECT p.id as id, p.typeproduit as  idtypeproduit,p.intitule as pintitule,"
                    . " tp.intitule as tpintitule,pp.id as ppid,pp.prix as prix,pp.dateinitiale as dateinitiale "
                    . " FROM produit p INNER JOIN typeproduit tp ON tp.id = p.typeproduit "
                    . " INNER JOIN prixproduit pp ON pp.produit = p.id "
                    . " WHERE p.station =:idstation AND pp.datefinale is NULL ";
    $reqProduits = $pdo->prepare($sqlproduits);
    $reqProduits->execute(array("idstation" => $station));
    $produits = $reqProduits->fetchAll(PDO::FETCH_OBJ);


} catch (Exception $ex) {

    echo "Ligne : " . $ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}
?>

<div class="row ">
    <div class="cell ">
        <label id="resuprix" class="label label-success"></label>
        <label id="resuprixerror" class="label label-warning"></label>
        <div class="table-responsive">
        <table class="table dataTable  table-condensed table-bordered table-striped" data-role="datatable" data-searching="true">
            <thead>
                <tr>
                    <th class="">PRODUCT TYPE</th>
                    <th class="">PRODUCT NAME</th>
                    <th class="">PRIZE</th>                        
                    <th class="">DATE</th>
                    <?php if ($role == "admin"){ ?>
                    <th class="">ACTION</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach  ($produits as $produit) {

                    ?>
                    <tr>
                        <td><?= $produit->tpintitule; ?></td>
                        <td><?= $produit->pintitule; ?></td>
                        <td>
                            <div class="input-control text">
                                <input type="text" value="<?= $produit->prix; ?>" id="prixvente-<?= $produit->ppid; ?>" readonly="true"/>
                                <button type="button" class="button info hidden" id="updateprix-<?= $produit->ppid; ?>" onclick="updatePrix(<?= $produit->ppid; ?>);">
                                    <label class="mif-icon mif-checkmark"></label>
                                </button>
                            </div>
                        </td>
                        <td id="tddate-<?=$produit->ppid; ?>"><?= $produit->dateinitiale; ?></td>

                        <?php if ($role == "admin"){ ?>
                        <td>
                            <button type="button" onclick="activerUpadte(''+this.id,<?=$produit->ppid; ?>);" class ="btn btn-warning btn-sm" id="btn-<?= $produit->ppid; ?>">
                                <label class="mif-icon mif-pencil"></label>
                            </button>
                            <button type="button" onclick="annuler(''+this.id,<?=$produit->ppid; ?>);" class ="btn btn-warning btn-sm hidden" id="btnanuler-<?= $produit->ppid; ?>">
                                <label class="mif-icon mif-cross"></label>
                            </button>
                        </td>
                        <?php } ?>
                        

                    </tr>

                <?php } ?>
            </tbody>
        </table>
        </div>
    </div>



</div>

<script type="text/javascript">

    var tempprix;
    function updatePrix(idprixproduit) {
        var nouveauprix = $("#prixvente-"+idprixproduit).val();
        $("#resuprixerror").html("");
        if(isNaN(nouveauprix)){
            $("#resuprixerror").html("Please Enter Valid Number");
            return false;
        }
//        alert("idprixproduit = "+idprixproduit+" , nouveauprix = "+nouveauprix);
//        return false;
        if (nouveauprix  !== '') {
            $.ajax({
                url: "traitement/ajaxtarification.php",
                method: "post",
                data: {idprixproduit: idprixproduit, nouveauprix: nouveauprix},
                dataType:"json",
                beforeSend:function(){
                    $("#prixvente-"+idprixproduit).attr("disabled","true");
                    $("#resuprix").html("");
                    $("#resuprixerror").html("");
                },
                success: function (data) {                    
                    switch(data.type)
                    {
                        case 1 : {
                               $("#prixvente-"+idprixproduit).val(nouveauprix);
                               $("#tddate-"+idprixproduit).html(data.dateinitiale);
                               $("#resuprix").html(data.message); 
                               
                        };break;
                        case 2 : {
                                
                                $("#resuprixerror").html(data.message);
                                $("#prixvente-"+idprixproduit).val(tempprix);
                        };break;
                    }
                    reinitialiser(idprixproduit);
                }
            });
        } else {
            $("#resuprixerror").html("Complete field !");
        }
    }
    
    function activerUpadte(btnid,idprixproduit){
        $("#updateprix-"+idprixproduit).removeClass("hidden");
        $("#prixvente-"+idprixproduit).removeAttr("readOnly");
        tempprix = $("#prixvente-"+idprixproduit).val();
        $("#"+btnid).addClass("hidden");
        $("#btnanuler-"+idprixproduit).removeClass("hidden");
    }
    function reinitialiser(idprixproduit){
        $("#updateprix-"+idprixproduit).addClass("hidden");
        $("#prixvente-"+idprixproduit).attr("readOnly","true");
        $("#btn-"+idprixproduit).removeClass("hidden");
        $("#btnanuler-"+idprixproduit).addClass("hidden");
        $("#prixvente-"+idprixproduit).removeAttr("disabled");
    }
    
    function annuler(btnid,idprixproduit){
        $("#prixvente-"+idprixproduit).val(tempprix);
        $("#btn-"+idprixproduit).removeClass("hidden");
        $("#"+btnid).addClass("hidden");
        reinitialiser(idprixproduit);
    }

</script>