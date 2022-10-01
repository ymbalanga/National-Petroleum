<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');

try{
    
    //Recuperation de la station
    $station = $_SESSION["station"];

    $reqpumps = $pdo->prepare("SELECT * FROM pompe p WHERE p.tank IN (SELECT id FROM tank WHERE station =:idstation) ");
    $reqpumps->execute(array("idstation" => $station));
    $datas = $reqpumps->fetchAll(PDO::FETCH_OBJ);

}catch(Exception $ex){
    echo "Ligne : ".$ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}catch(ErrorException $ex){
    echo "Ligne : ".$ex->getLine();
    echo "<br/>Erreur : <br/>";
    echo $ex->getMessage();
}
?>

<div class="grid">
    <div class="row cells5">
        <div class="cell">
            <?php include 'gauchemenu.php'; ?>
            <img src = "img/cot.png">
        </div>
        <!--        ---------fin gauche -------------------
        ---------partie central----------------->
        <div class="cell colspan4">
            <div class="row cells3">
                <!--        ---------pump ------------------->
                <div class="cell well"> 


                    <!--debut modal-->
                    <div class="modal fade" id="updatepump" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel">Update Pump Data</h4>
                                </div>

                                <div class="modal-body"> 
                                    <form method="post" id="pumpformUpdate">
                                        <input type="hidden" id="indexpumpUpdate"/>
                                        
                                        <div class="input-control select">
                                            <label>Pump type</label>
                                            <select name="pumpUpdate" id="pumpUpdate">
                                                <?php
                                               
                                                foreach ($datas as $data) {
                                                    echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                                }
                                                ?>                                
                                            </select>
                                        </div><br/><br/>
                                        <div class="input-control text">
                                            <label>Initial Index</label>
                                            <input type="text" id="indexinitialUpdate"  placeholder="Initial Index"/>
                                        </div><br/><br/>
                                        <div class="input-control text">
                                            <label>Final Index</label>
                                            <input type="text" id="indexfinalUpdate"  placeholder="Final Index"/>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text" data-role="datepicker">
                                            <label>Date</label>
                                            <input type="text" id="indexdateUpdate" name="indexdateUpdate" />
                                            <button class="button"><span class="mif-calendar"></span></button>
                                        </div><br/>
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




                    <h4 class="text-light">PUMP METER READINGS</h4>
                    <br/>
                    <p class='alert alert-danger hidden' id="msgindex">
                        An Index For This Pump is Already Saved For this Date !</p>
                    <form method="post" onsubmit="return false;" id="pumpform">                        
                        <div class="input-control select">
                            <label class="text-light">Pump type</label>
                            <select name="pump" id="pump">
                                <?php
                                
                                foreach ($datas as $data)  {
                                    echo "<option value='$data->id'>". $data->intitule ."</option>";
                                }
                                ?>                                
                            </select>
                        </div><br/><br/>
                        <div class="input-control text">
                            <label class="text-light">Initial Index</label>
                            <input type="text" name="pinitial" id="pinitial" placeholder="Initial stock"/>
                            <img src="img/loading.gif" class="button hidden" id="load"/>
                        </div><br/><br/>
                        <div class="input-control text">
                            <label class="text-light">Final Index</label>
                            <input type="text" name="pfinal" id="pfinal" placeholder="Final stock"/>
                            
                        </div>
                        <br/><br/>
                        <div class="input-control text" data-role="datepicker">
                            <label class="text-light">Date</label>
                            <input type="text" id="indexdate" name="indexdate"/>
                            <button class="button"><span class="mif-calendar"></span></button>
                        </div><br/>
                         <div id="resu"></div> 
                        <div>
                            <button class="button info" type="button" id="validepump">Validate</button>
                        </div>
                    </form>&nbsp;
                    
                </div>
                <!---------tank ------------------->

                <!-- bloc tableau-->
                <div class="cell colspan2">
                    <h4 class="text-light">PUMP SUMMARY</h4>
                    <!--<button class="button info"><a href="#">Daily report</a></button>-->
                    <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                        <thead>
                            <tr>
                                <th class="">No</th>
                                <th class="">Initial</th>
                                <th class="">Final</th>
                                <th class="">Date</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                        <tbody id="tablepump">
                            <?php
                            $sql = "SELECT * FROM indexpompe ip"
                                    . " WHERE ip.idpompe IN "
                                    . " (SELECT id FROM pompe p WHERE p.tank IN "
                                    . "     (SELECT id FROM tank WHERE station =:idstation) "
                                    . " ) ORDER BY id DESC";
                            $req = $pdo->prepare($sql);
                            $req->execute(array("idstation" => $station));
                            while ($indexpompe = $req->fetch(PDO::FETCH_OBJ)) {
                                $pompe = $pdo->prepare("SELECT intitule FROM pompe WHERE id= ?");
                                $pompe->execute(array($indexpompe->idpompe));
                                $pompetab = $pompe->fetch(PDO::FETCH_OBJ);

                                $id = $indexpompe->id;
                                $seek = $pdo->query("SELECT * FROM indexpompe WHERE id= $id");
                                while ($edit = $seek->fetch(PDO::FETCH_OBJ)) {
                                    $dateindex = str_replace("-", ".", $edit->dateindex);
                                    ?>
                                    <tr>
                                        <td><?= $pompetab->intitule; ?></td>
                                        <td><?= $indexpompe->indexinitial; ?></td>
                                        <td><?= $indexpompe->indexfinal; ?></td>
                                        <td><?= $indexpompe->dateindex; ?></td>
                                        <td>
                                            <button class="btn btn-warning btn-sm" data-toggle="modal"  data-target="#updatepump"
                                                    onclick="remplirModal(<?= $edit->id; ?>,<?= $edit->idpompe; ?>,'<?= $dateindex; ?>',
                                                    <?= $edit->indexinitial; ?>,<?= $edit->indexfinal; ?>);">Update
                                            </button>
                                        </td>
                                    </tr>

                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>


            </div>


        </div>
    </div>
</div>


<script type="text/javascript">
    
    function notifyOnErrorInput(input){
        var message = input.data('validateHint');
        $.Notify({
            caption: 'Error',
            content: message,
            type: 'alert'
        });
    }
    
    $(document).ready(function () {
        
        activerSm("pump");
        activerMenu("home");
        refreshFinalIndex($("#pump").val());
        
        $("#pump").change(function(){
            refreshFinalIndex(this.value);
        });
        
        $("#validepump").click(function(){
            
            var pump = $("#pump").val();
            var initial = $("#pinitial").val();
            var pfinal = $("#pfinal").val();
            var indexdate = $("#indexdate").val();
            $("#resu").html("Checking ...");
            
            
            if (initial !== '' & pfinal !== '' & pump !== '' & indexdate !== '') {
                var number = new RegExp("[,]","g");
                initial = initial.replace(number,".");
                pfinal = pfinal.replace(number,".");
                
                if(isNaN(initial)){
                    
                    errorInput("resu","Initial Index ");
                    return false;
                }
                
                if(isNaN(pfinal)){
                    
                    errorInput("resu","Final Index ");
                    return false;
                }
                
                $.ajax({
                    url: "traitement/ajaxpump.php",
                    method: "post",
                    data: {initial: initial, pfinal: pfinal, pump: pump, indexdate: indexdate},
                    success: function (data) {
                        $("#resu").html(data);
 
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Please, Complete empty Fields !!!</p>");
            }
        });
        
        $("#updateData").click(function(){
            
            var indexpump = $("#indexpumpUpdate").val();
            var pump = $("#pumpUpdate").val();
            var initial = $("#indexinitialUpdate").val();
            var pfinal = $("#indexfinalUpdate").val();
            var indexdate = $("#indexdateUpdate").val();
            
//            alert("indexpump = "+ indexpump + " , pump = "+ pump +" , initial = "+ initial +" , final = "+ pfinal +" , indexdate = "+ indexdate);
//            return false;
            
            if (indexpump !== '' & initial !== '' & pfinal !== '' & pump !== '' & indexdate !== '') {
                $.ajax({
                    url: "traitement/ajaxpump.php",
                    method: "post",
                    data: {action: "update",indexpump: indexpump,initial: initial, 
                            pfinal: pfinal, pump: pump, indexdate: indexdate},
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
    
    function refreshFinalIndex(pump){
       
        $.ajax({
            
            url: "traitement/ajaxpump.php",
            method: "post",
            data: {action:"finalIndex", pump: pump},
            dataType:"json",
            beforeSend:function(){
                $("#pinitial").val("");
                $("#load").removeClass("hidden");
            },
            success: function (data) {
                switch(data.type)
                {
                    case 1 : {
                       if(data.reponse !== -1)  {
                           $("#pinitial").val(data.reponse);
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
    
    function remplirModal(indexpumpUpdate,pumpUpdate,indexdateUpdate,indexinitialUpdate,indexfinalUpdate){
            
            $("#indexpumpUpdate").val(indexpumpUpdate);
            $("#pumpUpdate").val(pumpUpdate);
            $("#indexinitialUpdate").val(indexinitialUpdate);
            $("#indexfinalUpdate").val(indexfinalUpdate);
            $("#indexdateUpdate").val(indexdateUpdate);
    
    }
    
    function errorInput(idresu,input){
        var errorinput = "<p class='alert alert-danger'>"+input+",<br>Please, Only Number is required !!!</p>";
        $("#"+idresu).html(errorinput);
    }
    
    
</script>

<?php include('footer.php'); ?>