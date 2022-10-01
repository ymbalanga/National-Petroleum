<?php session_start(); ?>
<!DOCTYPE HTML>
<html lang="zxx">

    <head>
        <title>Application | National Petroleum</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <script type="application/x-javascript">
            addEventListener("load", function () {
            setTimeout(hideURLbar, 0);
            }, false);

            function hideURLbar() {
            window.scrollTo(0, 1);
            }
        </script>
        
        <link href="css/font-awesome.css" rel="stylesheet">
        <link href="css/style.css" rel='stylesheet' type='text/css' />
        <!--fonts-->
        <link href="//fonts.googleapis.com/css?family=Josefin+Sans:100,100i,300,300i,400,400i,600,600i,700,700i" rel="stylesheet">
        <link href="//fonts.googleapis.com/css?family=PT+Sans:400,400i,700,700i" rel="stylesheet">
        <!--//fonts-->
    </head>

    <body>
        <div class="login-section-agileits">
            <h3 class="form-head">Login Interface</h3>
            <form action="#" method="post">
                <div class="w3ls-icon">
                    <span class="fa fa-user" aria-hidden="true"></span>
                    <input type="text" class="lock" name="login" placeholder="username" required="" />
                </div>
                <div class="w3ls-icon">
                    <span class="fa fa-lock" aria-hidden="true"></span>
                    <input type="password" class="lock" id="password1" name="motdepasse" placeholder="Password" required="" />
                </div>
                <input type="submit" value="Validate" name="valider">
            </form>
            <br/>

            <?php
            if (isset($_POST['valider'])) {
                if (!empty($_POST['login']) && !empty($_POST['motdepasse'])) {
                    $login = strip_tags(addslashes($_POST['login']));
                    $motdepasse = strip_tags($_POST['motdepasse']);

                    require 'db.php';
                    $req = $pdo->prepare("SELECT * FROM users WHERE login= :login and pass= :pass");
                    $req->bindParam(":login", $login, PDO::PARAM_STR);
                    $req->bindParam(":pass", $motdepasse, PDO::PARAM_STR);

                    $req->execute();

                    $em = $req->rowCount();
                    if ($em == 1) {

                        $found = $req->fetch(PDO::FETCH_OBJ);

                        if ($found->etat == 1) {
                            echo "<p style='color:red; width:400px' class='alert alert-danger'>This User is Deactived !!!</p>";
                            exit();
                        }
                        $_SESSION['login'] = $found->login;
                        $_SESSION['pass'] = $found->pass;
                        $_SESSION['role'] = $found->role;


                        //setcookie("login", $login, time() + 60 * 60 * 60, null, null, false, true);
                        //setcookie("pass", $motdepasse, time() + 60 * 60 * 60, null, null, false, true);

                        header("location:choicestation.php");
                    } else {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Bad login and Password !!!</p>";
                    }
                } else {

                    if (!empty($_POST['login']) && is_numeric($_POST['login'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>The login includes only letters !!!</p>";
                    }
                    if (empty($_POST['login']) && empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please fill in all fields !!!</p>";
                    }
                    if (!empty($_POST['login']) && empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please enter the password !!!</p>";
                    }
                    if (empty($_POST['login']) && !empty($_POST['motdepasse'])) {
                        echo "<p style='color:red; width:400px' class='alert alert-danger'>Please enter your login !!!</p>";
                    }
                }
            }
            ?>
        </div>
        
        <p class="footer-agile">© 2018 January | Designed by
            <a href="http://devlab-conception.org"> DevLab-Conception</a>
        </p>


        <script type="text/javascript">
            // window.onload = function () {
            //     document.getElementById("password1").onchange = validatePassword;
            //     document.getElementById("password2").onchange = validatePassword;
            // }

            // function validatePassword() {
            //     var pass2 = document.getElementById("password2").value;
            //     var pass1 = document.getElementById("password1").value;
            //     if (pass1 != pass2)
            //         document.getElementById("password2").setCustomValidity("Passwords do not Match");
            //     else
            //         document.getElementById("password2").setCustomValidity('');
            //     //empty string means no validation error
            // }
        </script>

    </body>

</html>