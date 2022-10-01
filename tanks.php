<?php 
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
require("traitement/fonctions.php");

?>
<div class="grid">
    <div class="row cells5">
        <div class="cell">
             <?php include 'gauchemenu.php'; ?>
            <img src = "img/cit.jpg">
        </div>
        <!-----------fin gauche --------------
        ---------partie central----------------->
        <div class="cell colspan4">
            <?php
            $idstation = $_SESSION["station"];
            $indexTanks = array();
            try {

                $tanks = getListTank($pdo, $idstation);
                $date = date("Y-m-d");
                $indexesTanks = getListTanksIndexes($pdo,$idstation);
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
            ?>
            <div class="row">

                <!--        ---------tank ------------------->
                <div class="cell well">
                    
                    <!--debut modal-->
                    <div class="modal fade" id="updateTank" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Update TANK DATA</h4>
                                </div>

                                <div class="modal-body"> 
                                    <form method="post" id="tankformUpdate">
                                        <input type="hidden" id="indexTank"/>
                                        
                                        <div class="input-control select">
                                            <label class="text-light">Tank type</label>
                                            <select name="tank" id="tankUpdate">
                                                <?php
                                                foreach ($tanks as $tk) {
                                                    ?>
                                                    <option value="<?php echo $tk["id"] ?>"><?php echo $tk["intitule"] ?></option>
                                                    <?php
                                                }
                                                ?>                          
                                            </select>
                                        </div><br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light">Openning stock</label>
                                            <input type="text" name="openStockUpdate" id="openStockUpdate" placeholder="Opening stock/Ltrs"/>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light">Tests</label>
                                            <input type="text" name="testsUpdate" id="testsUpdate" placeholder="Tests"/>
                                        </div>
<!--                                        <br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light">Purchase</label>
                                            <input type="text" name="purchaseUpdate" id="purchaseUpdate" placeholder="Purchase"/>
                                        </div>-->
                                        <br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light">Dip</label>
                                            <input type="text" name="dipUpdate" id="dipUpdate" placeholder="Dip/Ltrs"/>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text" data-role="datepicker">
                                            <label class="text-light">Date</label>
                                            <input type="text" name="datetankUpdate" id="datetankUpdate" />
                                            <button class="button"><span class="mif-calendar"></span></button>
                                        </div>
                                        <div id="resuUpdate"></div>

                                        <div class="modal-footer">
                                            <button type="button" id="updateData" class="btn btn-primary">Update</button>
                                            <button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--fin modal-->
                    
                    
                    <h4 class="text-light">WHITE PRODUCT MOVEMENT: TANKS</h4>
                    <br/>
                    <form method="post" onsubmit="return false;">
                        <div class="input-control select">
                            <label class="text-light">Tank Type</label>
                            <select name="tank" id="tank">
                                <?php
                                foreach ($tanks as $tk) {
                                    ?>
                                    <option value="<?php echo $tk["id"] ?>"><?php echo $tk["intitule"] ?></option>
                                    <?php
                                }
                                ?>                          
                            </select>
                        </div>
                        <div class="input-control text">
                            <label class="text-light">Openning stock</label>
                            <input type="text" name="openStock" id="openStock" placeholder="Opening stock/Ltrs"/>
                            <img src="img/loading.gif" class="button hidden" id="load"/>
                        </div>
                        <div class="input-control text">
                            <label class="text-light">Tests</label>
                            <input type="text" name="tests" id="tests" placeholder="Tests"/>
                        </div>
<!--                        <div class="input-control text">
                            <label class="text-light">Purchase</label>
                            <input type="text" name="purchase" id="purchase" placeholder="Purchase"/>
                        </div>-->
                        <div class="input-control text">
                            <label class="text-light">Dip</label>
                            <input type="text" name="dip" id="dip" placeholder="Dip/Ltrs"/>
                        </div>
                        <br/><br/>
                        <div class="input-control text" data-role="datepicker">
                            <label class="text-light">Date</label>
                            <input type="text" name="datetank" id="datetank" />
                            <button class="button"><span class="mif-calendar"></span></button>
                        </div>
                        <br/><br/>
                        <div id="resu"></div>
                        <button class="button info" type="button" name="addTankIndex" id="addTankIndex">Validate</button>
                    </form>
                </div>
            </div>
            <!-- bloc tableau-->
            <div class="row">
                <div class="cell">
                    <h4 class="text-light">TANKS SUMMARY</h4>
                    <table class="table table striped hovered border bordered" data-role="datatable" data-searching="true">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="">Opening Stock/Ltrs</th>
                                <th class="">Tests</th>
                                <th class="">Purchase</th>
                                <th class="">Dip</th>
                                <th class="">Date</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($indexesTanks as $it) {
                                $dateindex = str_replace("-", ".", $it["datetank"]);
                                
                                $produit = getProduitByIntitule($pdo, $it["intitule"]);
                                
                                $appros = getApproFor($pdo, $produit->id, $it["datetank"]);
                                $purchase = null;
                                if($appros != null ){
                                    foreach ($appros as $ap){
                                        $purchase += $ap->qteappro;
                                    }
                                }

                                ?>
                                <tr>
                                    <td><?php echo $it["intitule"] ;?></td>
                                    <td><?php echo $it["openstock"] ;?></td>
                                    <td><?php echo $it["tests"] ;?></td>
                                    <td><?php echo $purchase ;?></td>
                                    <td><?php echo $it["dip"] ;?></td>
                                    <td><?php echo $it["datetank"] ;?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm" data-toggle="modal"  data-target="#updateTank"
                                                    onclick="remplirModal('<?= $it["id"]; ?>','<?= $it["tank"]; ?>','<?= $it["openstock"]; ?>',
                                                    '<?= $it["tests"] ; ?>', '<?= $it["purchase"]; ?> ',' <?= $it["dip"]; ?>' ,'<?= $dateindex; ?>');">Update
                                            </button>
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

</div>
<!--        ----------fin partie central -------------->

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("tank");
        activerMenu("home");
        
        
        
        refreshOpenStock($("#tank").val());
        
        $("#tank").change(function(){
            refreshOpenStock(this.value);
        });
        
        $("#addTankIndex").click(function(){
            
            var openStock = $("#openStock").val();
            var tank = $("#tank").val();
            var tests = $("#tests").val();
            //var purchase = $("#purchase").val();
            var dip = $("#dip").val();
            var datetank = $("#datetank").val();
            $("#resu").html(" ");
             
//            alert(tank+" , "+openStock+" , "+tests+" , "+purchase+" , "+dip+" , "+datetank);
//            return false;
            
            if (openStock !== '' & datetank !== '') {
                $.ajax({
                    url: "traitement/ajaxtank.php",
                    method: "post",
                    data: {action: "addTankIndex", tank: tank,openStock:openStock,tests:tests,dip:dip,datetank:datetank},
                    success: function (data) {
                        $("#resu").html(data);

                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Please, Complete empty Fields [openStock or date ] !!!</p>");
            }
        });
        
        $("#updateData").click(function(){
            
            var indexTank = $("#indexTank").val();
            var tankUpdate = $("#tankUpdate").val();
            var openStockUpdate = $("#openStockUpdate").val();
            var testsUpdate = $("#testsUpdate").val();
            //var purchaseUpdate = $("#purchaseUpdate").val();
            var dipUpdate = $("#dipUpdate").val();
            var datetankUpdate = $("#datetankUpdate").val();
            
//            alert(indexTank+" , "+tankUpdate+" , "+openStockUpdate+" , "+testsUpdate+" , "+purchaseUpdate+" , "+dipUpdate+" , "+datetankUpdate);
//            return false;
            
            if (openStockUpdate !== '' & datetankUpdate !== '') {
                $.ajax({
                    url: "traitement/ajaxtank.php",
                    method: "post",
                    data: {action: "update",indexTank: indexTank,tankUpdate:tankUpdate,
                        openStockUpdate:openStockUpdate,testsUpdate:testsUpdate,
                        dipUpdate:dipUpdate,datetankUpdate:datetankUpdate},
                    success: function (data) {
                        $("#resuUpdate").html(data);
                        
                        window.location.reload();
                        
                    }
                });
            } else {
                $("#resuUpdate").html("<p class='alert alert-danger'>Please, Complete empty Fields [openStock or date ] !!!</p>");
            }
            
        });
        
    });
    
    function remplirModal(indexTank,tankUpdate,openStockUpdate,testsUpdate,dipUpdate,datetankUpdate){
            
            $("#indexTank").val(indexTank);
            $("#tankUpdate").val(tankUpdate);
            $("#openStockUpdate").val(openStockUpdate);
            $("#testsUpdate").val(testsUpdate);
            //$("#purchaseUpdate").val(purchaseUpdate);
            $("#dipUpdate").val(dipUpdate);
            $("#datetankUpdate").val(datetankUpdate);
    
    }
    
    function refreshOpenStock(tank){
       
        $.ajax({
            
            url: "traitement/ajaxtank.php",
            method: "post",
            data: {action:"lastOpenStock", tank: tank},
            dataType:"json",
            beforeSend:function(){
                $("#openStock").val("");
                $("#load").removeClass("hidden");
            },
            success: function (data) {
                switch(data.type)
                {
                    case 1 : {
                       if(data.reponse !== -1)  {
                           $("#openStock").val(data.reponse);
                       }
                    };break;
                    case 2 :{
                         alert(data.reponse);   
                    } ;break;
                }
                $("#load").addClass("hidden");
            }
        });
        
    }
    
</script>

<?php include('footer.php'); ?>