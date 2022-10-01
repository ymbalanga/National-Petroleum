<?php
include 'session/session_dash.php';
include('header.php');
require_once('db.php');
try{
    
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $req = $pdo->query($sql);

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
            <img src = "img/user.jpg">
        </div>



        <div class="cell well">
            <h5 class="text-light">USER INTERFACE</h5>
            <form method="post" onsubmit="return false;">
                <div class="row">
                    <div class="cell">
                        <label class="text-light">Login</label>
                        <div class="input-control text">
                            <input type="text" name="login" id="login" placeholder="Login"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="cell">
                        <label class="text-light">Password</label>
                        <div class="input-control password">

                            <input type="password" name="mot" id="mot" placeholder="Password"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="cell">
                        <label class="text-light">Role</label>
                        <div class="input-control select">
                            <select id="roletype" name="roletype">
                                <option value="admin">Admin</option>
                                <option value="user">user</option>
                            </select>

                        </div>
                    </div>
                </div>



                <div id="resu"></div><br/>
                <div>
                    <button class="button info" id="valideInsertUser">Validate</button>
                </div>
            </form>
        </div>



        <div class="cell colspan2 well">
            <h5 class="text-light">USERS ENABLED</h5>
            <!--<button class="button info"><a href="#">Daily report</a></button>-->
            <table class="dataTable border bordered" data-role="datatable" data-searching="true">
                <thead>
                    <tr>
                        <th class="">Login</th>
                        <!--<th class="">Password</th>-->
                        <th class="">Role</th>
                        <th class="">Etat</th>
                        <th class="">Action</th>
                    </tr>
                </thead>
                <tbody id="tableuser">
                    <?php
                    while ($users = $req->fetch(PDO::FETCH_OBJ)) {
                        ?>
                        <tr>
                            <td><?= $users->login; ?></td>
                            <!--<td><?= $users->pass; ?></td>-->
                            <td><?= $users->role; ?></td>
                            <td>
                                <?php
                                switch ($users->etat) {
                                    case 0: echo "<b class='label label-success'>Actived</b>";
                                        break;
                                    case 1: echo "<b class='label label-danger'>Desactived</b>";
                                        break;
                                }
                                ?>
                            </td>
                            <td>
                                <button class = 'btn btn-warning btn-sm' data-toggle="modal"  data-target="#updatepump-modal-sm-<?= $users->id; ?>">Update</button>
                            </td>
                        </tr>
                        <!--debut modal-->
                    <div class="modal fade" id="updatepump-modal-sm-<?= $users->id; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel-<?= $users->id; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="myModalLabel-<?= $users->id; ?>"> User Data Update</h4>
                                </div>
                                <div class="modal-body">
                                    <div id="formupdate-<?= $users->id; ?>">
                                        <div class="grid">
                                            <div class="row">
                                                <div class="cell">
                                                    <label class="text-light">Login</label>
                                                    <div class="input-control text full-size">
                                                        <input type="text" value="<?= $users->login; ?>" id="login" placeholder="Login"/>
                                                    </div> 
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="cell">
                                                    <label class="text-light">Password</label>
                                                    <div class="input-control password full-size" data-role="input">
                                                        <input type="password" value="<?= $users->pass; ?>" id="mot" placeholder="Password"/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="cell">
                                                    <label class="text-light">Role</label>
                                                    <br/>
                                                    <div class="input-control select">
                                                        <select id="roletype">
                                                            <option value="admin" <?= ($users->role == "admin") ? "selected='true'" : ""; ?> >Admin</option>
                                                            <option value="user" <?= ($users->role == "user") ? "selected='true'" : ""; ?> >user</option>
                                                        </select>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="resu-<?= $users->id; ?>"></div><br/>
                                        <div>
                                            <button class="button info" type="button" id="updateuser"  onclick="updateUser(<?= $users->id; ?>);">Validate</button>
                                            <?php
                                            if ($users->login != $_SESSION["login"]) {
                                                if ($users->etat == 0) {
                                                    ?>
                                                    <button class="button danger" type="button" id="desactiver" onclick="desactiverUser(<?= $users->id; ?>);">Deactivate</button>
                                                <?php } else { ?>
                                                    <button class="button success " type="button" onclick="activerUser(<?= $users->id; ?>);">Activate</button>
                                                    <?php
                                                }
                                            }
                                            ?>
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
            <img src = "img/users.png">
        </div>

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        activerSm("user");
        activerMenu("settings");
        $("#login").val('');

        $("#valideInsertUser").click(function () {
            var login = $("#login").val();
            var mot = $("#mot").val();
            var roletype = $("#roletype").val();
            if (login !== '' && mot !== '' && roletype !== '') {
                $.ajax({
                    url: "traitement/ajaxuser.php",
                    method: "post",
                    data: {login: login, mot: mot, roletype: roletype},
                    success: function (data) {
                        $("#resu").html(data);

                        //
                    }
                });
            } else {
                $("#resu").html("<p class='alert alert-danger'>Recording Failed !!!</p>");
            }
        });

    });


    function updateUser(id) {



        var login = $("#formupdate-" + id).find(" input[id='login'] ").val();
        var mot = $("#formupdate-" + id).find(" input[id='mot'] ").val();
        var roletype = $("#formupdate-" + id).find(" select[id='roletype'] ").val();
        //alert("login = "+login+" , role = "+roletype+" , pass = "+mot);

        if (login !== '' && mot !== '' && roletype !== '') {
            $.ajax({
                url: "traitement/ajaxuser.php",
                method: "post",
                data: {update: 1, userid: id, login: login, mot: mot, roletype: roletype},
                success: function (data) {
                    $("#resu-" + id).html(data);
                    window.location.reload();
                }
            });
        } else {
            $("#resu-" + id).html("<p class='alert alert-danger'>Fields required  !!!</p>");
        }
    }
    function desactiverUser(iduser) {


        $.ajax({
            url: "traitement/ajaxuser.php",
            method: "post",
            data: {iduser: iduser},
            success: function (data) {
                $("#resu-" + iduser).html(data);
                window.location.reload();
            }
        });
    }


    function activerUser(iduser) {


        $.ajax({
            url: "traitement/ajaxuser.php",
            method: "post",
            data: {iduseractive: iduser},
            success: function (data) {
                $("#resu-" + iduser).html(data);
                window.location.reload();
            }
        });
    }


</script>
<?php include('footer.php'); ?>