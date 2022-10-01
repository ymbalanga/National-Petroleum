<?php //session_start();      ?>
<!DOCTYPE html>
<html>
    <head lang="fr">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <meta name="description" content="application de gestion de station.">
        <meta name="keywords" content="app station">
        <meta name="author" content="Devlab-conception.org">

        <link rel='shortcut icon' type='image/x-icon' href='favicon.ico' />
        <title>Application | National Petroleum</title>

        <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
        <link href="css/metro.css" rel="stylesheet">
        <link href="css/metro-icons.css" rel="stylesheet">
        <link href="css/metro-responsive.css" rel="stylesheet">
        <link href="css/metro-schemes.css" rel="stylesheet">
        <link href="css/docs.css" rel="stylesheet">
        <!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.css"> -->
        <!-- les fichiers javascript -->
        <script src="js/jquery-2.1.3.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/metro.js"></script>
        <script src="js/docs.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <style>

        </style>
    </head>
    <body>

        <!--DEBUT MENU-->
        <script language="javascript">
            function date_heure(id)
            {
                date = new Date;
                annee = date.getFullYear();
                moi = date.getMonth();
                mois = new Array('January', 'February', 'March', 'Avril', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', 'December');
                j = date.getDate();
                jour = date.getDay();
                jours = new Array('Sunday', 'Monday', 'Tuesday', 'wednesday', 'Thursday', 'Friday', 'Saturday');
                h = date.getHours();
                if (h < 10)
                {
                    h = "0" + h;
                }
                m = date.getMinutes();
                if (m < 10)
                {
                    m = "0" + m;
                }
                s = date.getSeconds();
                if (s < 10)
                {
                    s = "0" + s;
                }
                resultat = annee + ' ' + ' ' + mois[moi] + ' ' + j + ' ' + jours[jour] + '  - Hours:  ' + h + ':' + m + ':' + s;
                document.getElementById(id).innerHTML = resultat;
                setTimeout('date_heure("' + id + '");', '1000');
                return true;
            }
        </script>
        <ul class="m-menu right">
            <li id="m-home"><a href="dashboad-Application.php">HOME</a></li>
            <li id="m-sales"><a href="sales.php">SALES</a></li>
            <li id="m-reports"><a href="reports.php">REPORTS</a></li>
            <li id="m-settings"><a href="settings.php">SETTINGS</a></li>
             <li id="m-tarif"><a href="" onclick="showTarification();" data-toggle="modal"  data-target="#tarification">TARIF</a></li>
            <li><a href="changestation.php">GAS STATION</a></li>
            <li><a href="" onclick="return false;">USER:
                    <?php
                    if (isset($_SESSION['login'])) {
                        echo "<b style='color:green' class='capital'>" . $_SESSION['login'] . "</b>";
                    }
//                    else {
//                    header("location:index.php");
//                    }
                    ?>
                </a></li>
            <li><a href="" onclick="return false;">STATION:
                    <?php
                    if (isset($_SESSION['station'])) {
                        $station = $_SESSION['station'];
                        require 'db.php';
                        $sql = 'SELECT intitule FROM station WHERE id= :id';
                        $reqstat = $pdo->prepare($sql);
                        $reqstat->execute(array('id' => $station));
                        $affiche = $reqstat->fetch(PDO::FETCH_OBJ);
                        echo "<b style='color:green' class='capital'>" . $affiche->intitule . "</b>";
                    }
                    ?>
                </a></li>
            <li><a href="" onclick="return false;">RATE:
                    <?php
                    if (isset($_SESSION['station'])) {
                        $taux = $_SESSION['taux'];
                        echo "<b style='color:green' class='capital'>" . $taux . "</b>";
                    }
                    ?>
                </a></li>
<!--            <li><a href="" onclick="return false;">ROLE:
                    <?php
                    if (isset($_SESSION['role'])) {
                        echo "<b style='color:green' class='capital'>" . $_SESSION['role'] . "</b>";
                    }
                    ?>
                </a></li>-->
            <li><a href="session/logout.php">
                    <img src="img/exit.png" class="img-rounded" title="Deconnect" width="30" height="15"/></a>
            </li>

        </ul>

        <div style=" margin-top:px;background:#c3d9c8; font-family:verdana; text-align: center; ">
            <h4 class="text-light">
                Date: <span id="date_heure"></span>
                <script type="text/javascript">window.onload = date_heure('date_heure');</script>

            </h4>
        </div>
        <!--debut modal-->
<div class="modal fade" id="tarification" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">close &times;</button>
                <h4 class="modal-title" id="myModalLabel">PRODUCTS  TARIFICATION</h4>
            </div>

            <div class="modal-body"> 
                <img src="img/loading.gif" class="hidden" id="load"/>
                
                <div class="grid" id="data">
                    
                </div>
            </div>
        </div>
    </div>
    
</div>

<!--fin modal-->