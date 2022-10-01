<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');

try{
    

    $station = $_SESSION["station"];

    $reqTank = $pdo->prepare("SELECT * FROM tank WHERE station =:idstation ");
    $reqTank->execute(array("idstation" => $station));
    
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
            <img src = "img/tank.jpg">
        </div>



        <div class="cell well">
            <h5 class="text-light">TANKS INTERFACE</h5>
            <br/>
            <form method="post" onsubmit="return false;">
                <label class="text-light">Tank's Name</label>
                <div class="input-control text">
                    <input type="text" name="intitule" id="intitule" placeholder="Name of Tank"/>
                </div>
                <label class="text-light">Tank Type</label>
                <div class="input-control text">
                    <input type="text" name="typetank" id="typetank" placeholder="Type of Tank"/>
                </div>

                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="validetank">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">TANKS RECORDED</h5>
            <!--<button class="button info"><a href="#">Daily report</a></button>-->
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Tank</th>
                        <th class="">Tank Type</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    while ($reqTanks = $reqTank->fetch(PDO::FETCH_OBJ)) {
                        ?>
                        <tr>
                            <td><?= $reqTanks->intitule; ?></td>
                            <td><?= $reqTanks->typetank; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatepump-modal-sm-<?= $reqTanks->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatepump-modal-sm-<?= $reqTanks->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $reqTanks->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $reqTanks->id; ?>">Tank Data Updated</h4>
                                </div>
                                <div class="modal-body"> 
                                    <div id="formupdate-<?= $reqTanks->id; ?>">
                                        <label class="text-light">Tank's Name</label>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $reqTanks->intitule; ?>" id="intitule" placeholder="Name of Tank"/>
                                        </div>
                                        <br/>
                                        <label class="text-light">Tank's Type</label>
                                        <div class="input-control text">
                                            <input type="text" value="<?= $reqTanks->typetank; ?>" id="typetank" placeholder="Type of Tank"/>
                                        </div>

                                        <div id="resutank-<?= $reqTanks->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updateuser"  onclick="updatePump(<?= $reqTanks->id; ?>);">Validate</button>
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
            <img src = "img/tank2.jpg">
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("tanksdmin");
        activerMenu("settings");

        $("#validetank").click(function () {
            var intitule = $("#intitule").val();
            var typetank = $("#typetank").val();
            if (intitule !== '' && typetank !== '') {
                $.ajax({
                    url: "traitement/ajaxtankadmin.php",
                    method: "post",
                    data: {intitule: intitule, typetank: typetank},
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

    function updatePump(id) {
        var intitule = $("#formupdate-" + id).find(" input[id='intitule'] ").val();
        var typetank = $("#formupdate-" + id).find(" input[id='typetank'] ").val();

//        alert("intitule = "+intitule+" , typetank = "+typetank);
//        return false;
        if (intitule !== '' && typetank !== '') {
            $.ajax({
                url: "traitement/ajaxtankadmin.php",
                method: "post",
                data: {intituleUP: intitule, typetankUP: typetank, id: id},
                success: function (data) {
                    $("#resutank-" + id).html(data);

                    window.location.reload();
                }
            });
        } else {
            $("#resutank-" + id).html("<p class='alert alert-danger'>Fields required  !!!</p>");
        }
    }

</script>
<?php include('footer.php'); ?>