<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');

try {
    
    $station = $_SESSION["station"];

    $sql = "SELECT p.id as id,p.intitule as name,t.intitule as tank, s.intitule as stationname "
            . " FROM pompe p INNER JOIN tank t ON p.tank = t.id INNER JOIN station s ON s.id = t.station"
            . " AND t.station =:idstation ";
    $req = $pdo->prepare($sql);
    $req->execute(array("idstation"=>$station));
    
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
            <img src = "img/car.jpg">
        </div>



        <div class="cell well">
            <h5 class="text-light">PUMP INTERFACE</h5>
            <br/>
            <form method="post" onsubmit="return false;">
                <div class="grid">
                    <div class="row">
                        <div class="cell">

                            <label for="tank" class="text-light">Tank</label>
                            <div class="input-control select">
                                <select name="tank" id="tank">
                                    <?php
                                    $reqtanks = $pdo->prepare("SELECT * FROM tank WHERE station =:idstation ");
                                    $reqtanks->execute(array("idstation" => $station));
                                    while ($data = $reqtanks->fetch(PDO::FETCH_OBJ)) {
                                        echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                    }
                                    ?>                                
                                </select>
                            </div>

                        </div>
                        <div class="cell">

                            <label for="pump" class="text-light">Pump's Name</label>
                            <div class="input-control text">
                                <input type="text" name="pump" id="pump" placeholder="Name of Pump"/>
                            </div>


                        </div>
                    </div>
                </div>


                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="validepump">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">PUMP RECORDED</h5>
            <!--<button class="button info"><a href="#">Daily report</a></button>-->
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Station</th>
                        <th class="">Pump Name</th>
                        <th class="">Tank</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    while ($pump = $req->fetch(PDO::FETCH_OBJ)) {
                        ?>
                        <tr>
                            <td><?= $pump->stationname; ?></td>
                            <td><?= $pump->name; ?></td>
                            <td><?= $pump->tank; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatepump-modal-sm-<?= $pump->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatepump-modal-sm-<?= $pump->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $pump->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $pump->id; ?>"> User Data Updated</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="formupdate-<?= $pump->id; ?>">
                                        
                                        <div class="input-control select">
                                            <label class="text-light">Tank</label>
                                            <select name="tank" id="tank">
                                                <?php
                                                    $reqtanks = $pdo->prepare("SELECT * FROM tank WHERE station =:idstation ");
                                                    $reqtanks->execute(array("idstation" => $station));
                                                    while ($data = $reqtanks->fetch(PDO::FETCH_OBJ)) {
                                                        
                                                        $selected = ($pump->tank == $data->id)? "selected ='true' " : " " ;
                                                        echo "<option value=$data->id  $selected>". $data->intitule . "</option>";
                                                    }
                                                ?>                                
                                            </select>
                                        </div>
                                        <br/><br/>
                                        <div class="input-control text">
                                            <label class="text-light">Pump's Name</label>
                                            <input type="text" value="<?= $pump->name; ?>" id="pump" placeholder="Name of Pump"/>
                                        </div>


                                        <div id="resupump-<?= $pump->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updateuser"  onclick="updatePump(<?= $pump->id; ?>);">Validate</button>
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
            <img src = "img/fff.png">
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("pumpadmin");
        activerMenu("settings");
        $("#pump").val('');


        $("#validepump").click(function () {
            var tank = $("#tank").val();
            var pump = $("#pump").val();
            if (tank != '' && pump != '') {
                $.ajax({
                    url: "traitement/ajaxpumpadmin.php",
                    method: "post",
                    data: {tankad: tank, pumpad: pump},
                    success: function (data) {
                        $("#resu").html(data);

                        
                    }});
            } else {
                $("#resu").html("<p class='alert alert-danger'>Fields required  !!!</p>");
            }
        });

    });

    function updatePump(id) {
        var tank = $("#formupdate-" + id).find(" select[id='tank'] ").val();
        var pump = $("#formupdate-" + id).find(" input[id='pump'] ").val();

        //        alert("tank = "+tank+" , pump = "+pump);
//        return false;
        if (tank != '' && pump != '') {
            $.ajax({
                url: "traitement/ajaxpumpadmin.php",
                method: "post",
                data: {tank: tank, pump: pump, id: id},
                success: function (data) {
                    $("#resupump-" + id).html(data);

                    
                }
            });
        } else {
            $("#resupump-" + id).html("<p class='alert alert-danger'>Fields required  !!!</p>");
        }
    }

</script>
<?php include('footer.php'); ?>