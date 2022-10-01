<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');


?>
<div class="grid">
    <div class="row cells5">




        <div class="cell">
            <?php include('gauchemenuadmin.php'); ?>
            <img src = "img/stat2.png">
        </div>



        <div class="cell well">
            <h5 class="text-light">GAS STATION INTERFACE</h5>
            <form method="post" onsubmit="return false;">
                <br/>
                <label>Gas station Name</label>
                <div class="input-control text">
                    <input type="text" name="intitule" id="intitule" placeholder="Gas station Name"/>
                </div>
                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="addstation">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">GAS STATIONS LIST</h5>
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Name</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    $sql = "SELECT * FROM station ORDER BY id DESC";
                    $req = $pdo->query($sql);
                    $stations = $req->fetchAll(PDO::FETCH_OBJ);
                    foreach ($stations as $st) {
                        ?>
                        <tr>
                            <td><?= $st->intitule; ?></td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatestation-<?= $st->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatestation-<?= $st->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $st->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $st->id; ?>"> Gas station Data Update</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="formupdate-<?= $st->id; ?>">

                                        <br/>
                                        <label>Gas station Name</label>
                                        <div class="input-control text full-size">
                                            <input type="text" value="<?= $st->intitule; ?>" id="intituleupdate" placeholder="Gas  station Name"/>
                                        </div>
                                        <br/>

                                        <div id="resu-<?= $st->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" id="updatestation"  onclick="updateStation(<?= $st->id; ?>);">Validate</button>
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
            <img src = "img/logo.JPG">
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("station");
        activerMenu("settings");

        $("#addstation").click(function () {
            var intitule = $("#intitule").val();


            if (intitule !== '') {
                $.ajax({
                    url: "traitement/ajaxstation.php",
                    method: "post",
                    data: {action: 1, intitule: intitule},
                    success: function (data) {
                        $("#resu").html(data);

                        window.location.reload();
                    }
                });
            } else {
                $("#resu").html("Field required !");
            }
        });


    });

    function updateStation(id) {

        var intituleupdate = $("#formupdate-" + id).find(" input[id='intituleupdate'] ").val();

        //alert("intitule "+intitule);
        //return false;
        if (intitule !== '') {
            $.ajax({
                url: "traitement/ajaxstation.php",
                method: "post",
                data: {action: 2, intituleupdate: intituleupdate, idstation: id},
                success: function (data) {
                    $("#resu-" + id).html(data);

                    window.location.reload();
                }
            });
        } else {
            $("#resu-" + id).html("<p class='alert alert-danger'>Field reauired  !!!</p>");
        }
    }

</script>
<?php include('footer.php'); ?>