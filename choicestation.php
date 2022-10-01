<?php
include 'session/session_choice.php';



require_once('db.php');

function getTaux($tabTaux, $station) {

    $taux = null;
    foreach ($tabTaux as $t) {
        if ($t["station"] == $station) {
            $taux = $t["valeurtaux"];
        }
    }
    return $taux;
}

function updateTaux($pdo, $idstation, $taux, $date) {

    if (existTaux($pdo, $idstation)) {

        $req = $pdo->prepare("UPDATE taux SET datefinale = now() "
                . "WHERE station =:idstation ");
        $req->execute(array("idstation" => $idstation));

        insertTaux($pdo, $idstation, $taux, $date);
    } else {
        insertTaux($pdo, $idstation, $taux, $date);
    }
}

function insertTaux($pdo, $idstation, $taux, $date) {
    $req2 = $pdo->prepare("INSERT INTO taux SET valeurtaux =:nouveautaux, "
            . "station =:idstation,"
            . "dateinitiale =:dateinitiale");
    $req2->execute(array("idstation" => $idstation, "nouveautaux" => $taux, "dateinitiale" => $date));
}

function existTaux($pdo, $idstation) {

    $req = $pdo->prepare("SELECT id FROM taux WHERE station =:idstation AND datefinale is NULL LIMIT 1");
    $req->execute(array("idstation" => $idstation));
    $tauxFound = $req->fetch();
    if ($tauxFound) {
        return true;
    }
    return false;
}

try {

    $sqlTauxstations = $pdo->query("SELECT * FROM taux  WHERE datefinale is NULL");
    $tauxSt = $sqlTauxstations->fetchAll();

    if (isset($_POST["launch"])) {
        extract($_POST);
        $_SESSION["station"] = $station;
        $_SESSION["taux"] = getTaux($tauxSt, $station);

        header("Location:dashboad-Application.php");
    }


    $messageTaux = "";
    if (isset($_SESSION["message"]) && $_SESSION["message"] == 1) {
        $messageTaux = "The Exchange rate is updated";

        $_SESSION["message"] = 0;
    }


    if (isset($_POST["valider"])) {

        extract($_POST);

        updateTaux($pdo, $station, $taux, $dateinitiale);


        $_SESSION["station"] = $station;
        $_SESSION["taux"] = $taux;
        $_SESSION["message"] = 1;

        header("Location: choicestation.php");
    }


    $sqltaux = "SELECT valeurtaux FROM taux WHERE datefinale is null";
    $reqtaux = $pdo->query($sqltaux)->fetch();
} catch (Exception $ex) {

    echo "Ligne : " . $ex->getLine();
    echo "<br/>";
    echo $ex->getMessage();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Application | National Petroleum </title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.css"/>
        <link href="css/metro.css" rel="stylesheet"/>
        <link href="css/metro-icons.css" rel="stylesheet"/>
        <link href="css/metro-schemes.css" rel="stylesheet"/>
        <!--style de l'opacite-->
        <link href="css/stylechoice.css" rel="stylesheet"/>

        <!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.css"> -->
        <!-- les fichiers javascript -->
        <script src="js/jquery-2.1.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/metro.js"></script>
        <script src="js/docs.js"></script>

        <style type="text/css">
            html, body {
                height: 100%;
                width: 100%;
                padding: 0;
                margin: 0;

            }       
            .login-form {
                width: 50rem;
                height: 29rem;
                top: 37%;
                margin-top: 4rem;
                margin-left: 18rem;
                background-color: #ffffff;
                border-radius: 10px;

            }
            #full-screen-background-image {
                z-index: -999;
                min-height: 100%;
                min-width: 1024px;
                width: 100%;
                height: auto;
                position: fixed;
                top: 0;
                left: 0;
            }
            .message-taux{
                font-size:13px;margin:1px;padding:1px;
            }

        </style>
    </head>
    <body>
        <img src="img/stat.jpg" id="full-screen-background-image" />
        <div class="login-section-agileits">
            <!-- <div class="login-form padding20 block-shadow login-section-agileits"> -->
            <form method="post" action="">
                <h4>
                    <span>Gas Station working Space</span> 
                    <span style="margin-left: 450px;"> 
                        <a href="session/logout.php">
                            <img src="img/exit.png" class="img-rounded" title="Logout" width="30" height="15"/>
                            Logout</a>
                    </span>
                </h4>

                <hr class="thin"/>


                <div class="grid">
                    <div class="row cells4">
                        <div class="cell colspan2">
                            <div class="icon">
                                <img src="img/stattion.jpg" />
                            </div>
                            <div style="margin-left:30px; margin-top: 120px">
                            </div>



                        </div>
                        <div class="cell colspan2">
                            <div class="input-control select full-size" data-role="input">
                                <label for="station">Stations</label>
                                <select type="select" name="station" id="station">
                                    <?php
                                    $sql = $pdo->query("SELECT * FROM station");
                                    while ($data = $sql->fetch(PDO::FETCH_OBJ)) {
                                        echo '<option value=' . $data->id . '>' . $data->intitule . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php
                            foreach ($tauxSt as $ts) {
                                ?>           
                                <input type="hidden" value ="<?= $ts['valeurtaux']; ?>" id="st-<?= $ts['station']; ?>"/>
                                <?php
                            }
                            ?>
                            <input type="hidden" id="temptaux"/>
                            <p style="font-size: 21px;" class="alert alert-danger">Exchange Rate: 1 $ = 
                                <span id="tauxSt" class="label label-danger"></span><span id="tauxStnc" ></span>.FC
                            </p>
                            <br />
                            <div class="input-control text full-size" data-role="datepicker">
                                <label for="taux">Initial Date</label>
                                <input type="text" id="dateinitiale" name="dateinitiale">
                                    <button class="button"><span class="mif-calendar"></span></button>
                            </div>
                            <br/><br/>
                            <div class="input-control text full-size" data-role="input">
                                <label for="taux">Exchange rate</label>
                                <input type="text" name="taux" id="taux" name="taux"/>
                            </div>
                            <p class="alert alert-success <?= ($messageTaux == '') ? 'hidden' : ''; ?>" ><?= $messageTaux; ?></p>
                            <p id="msg-error" class="hidden message-taux alert alert-warning">
                            </p>
                            <div class="form-actions">

                                <button type="submit" class="button info" id="valider" name="valider" >Validate</button> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; 
                                &nbsp;&nbsp;&nbsp; &nbsp;
                                <button type="submit" class="button success"  name="launch" id="launch">Launch the application</button>

                            </div>
                        </div>

                    </div>
                </div>
            </form>
            <?php ?>

        </div>

        <script>
            $(function () {
                var st = $("#station").val();
                afficherTaux(st);

                $("#station").change(function () {
                    afficherTaux(this.value);
                });

                $("#valider").click(function (e) {
                    if ($("#dateinitiale").val() === '' || $("#taux").val() === '' || $("#taux").val() <= 0) {
                        $("#msg-error").removeClass("hidden")
                                .html("Please ! Complete fields ! Exchange must be Greather than 0");
                        e.preventDefault();
                    }
                });

            });

            function afficherTaux(stid) {
                var t = $("#st-" + stid).val();

                if (t) {
                    $("#tauxSt").html(t);
                    $("#tauxStnc").html('');
                }
                else {
                    t = 0;
                    $("#tauxSt").html('');
                    $("#tauxStnc").html("<span class='alert alert-info message-taux'>Not configured !</span>");
                }

                $("#temptaux").val(t);
                showButtonLaunchApp(t);
            }
            function showButtonLaunchApp(t) {
                if (t === 0) {
                    //alert("must configured exchange rate");
                    $("#launch").addClass("hidden");
                }
                else {
                    $("#launch").removeClass("hidden");
                }
            }
        </script>
    </body>

</html>